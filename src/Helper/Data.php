<?php

namespace DevGenii\SocialConnect\Helper;

use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\GroupManagementInterface
     */
    protected $customerGroupManagement;

    /** @var \Magento\Customer\Api\AccountManagementInterface */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    protected $writeFactory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Math\Random $random,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Framework\Image\Factory $imageFactory,

        // Parent
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_appState = $appState;
        $this->random = $random;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->accountManagement = $accountManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->writeFactory = $writeFactory;
        $this->imageFactory = $imageFactory;

        // Parent
        parent::__construct($context);
    }

    /**
     * @param $message
     * @param int $level
     */
    public function log($message, $level = \Monolog\Logger::DEBUG)
    {
        if($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            // Notice the order of arguments
            $this->_logger->log($level, $message);
        }
    }

    /**
     * Generate random string to be used as CSRF token
     *
     * @return string
     */
    public function generateCsrfToken()
    {
        return $this->random->getRandomString(32);
    }

    /**
     * @param $id
     * @param \stdClass $token
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param $idAttribute
     * @param $tokenAttribute
     * @throws \Exception
     */
    public function connectByCustomer(
        $id,
        \stdClass $token,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $idAttribute,
        $tokenAttribute
    )
    {
        $customer->setCustomAttributes([
            $idAttribute => $id,
            $tokenAttribute => serialize($token)
        ]);

        $this->customerRepository->save($customer);
    }

    /**
     * @param $id
     * @param $token
     * @param $email
     * @param $firstName
     * @param $lastName
     * @param $idAttribute
     * @param $tokenAttribute
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Exception
     */
    public function connectByCreatingAccount(
        $id,
        $token,
        $email,
        $firstName,
        $lastName,
        $idAttribute,
        $tokenAttribute
    )
    {
        $store = $this->storeManager->getStore();

        $customerData = array(
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'sendemail_store_id' => $store->getId(),
            'sendemail' => '1',
            'custom_attributes' => [
                [
                    \Magento\Framework\Api\AttributeInterface::ATTRIBUTE_CODE => $idAttribute,
                    \Magento\Framework\Api\AttributeInterface::VALUE => $id
                ],
                [
                    \Magento\Framework\Api\AttributeInterface::ATTRIBUTE_CODE => $tokenAttribute,
                    \Magento\Framework\Api\AttributeInterface::VALUE => serialize($token)
                ],
            ]
        );

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customerDataObject = $this->customerFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $customerData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        $customerDataObject->setGroupId(
            $this->customerGroupManagement->getDefaultGroup($store->getId())->getId()
        );

        $customerDataObject->setWebsiteId($store->getWebsiteId());
        $customerDataObject->setStoreId($store->getId());
        $customerDataObject->setAddresses(null);

        $password = null;
        $redirectUrl = '';

        $customer = $this->accountManagement
            ->createAccount($customerDataObject, $password, $redirectUrl);

        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => null, 'customer' => $customer]
        );

        return $customer;
    }

    /**
     * @param $id
     * @param $idAttribute
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerById(
        $id,
        $idAttribute
    )
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            $idAttribute,
            $id,
            'eq'
        )->create();

        $result = $this->customerRepository->getList($searchCriteria);

        if($result->getTotalCount()) {
            return $result->getItems()[0];
        }

        return null;
    }

    /**
     * @param $email
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomersByEmail($email)
    {
        return $this->customerRepository->get($email);
    }
}