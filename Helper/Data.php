<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Helper;

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

    /** @var  \Magento\Framework\Api\AttributeValueFactory */
    protected $customAttributeValueFactory;

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
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeValueFactory
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
        \Magento\Framework\Api\AttributeValueFactory $customAttributeValueFactory,

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
        $this->customAttributeValueFactory = $customAttributeValueFactory;

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
        $idAttribute = $this->customAttributeValueFactory->create()
                ->setAttributeCode($idAttribute)
                ->setValue($id);

        $tokenAttribute = $this->customAttributeValueFactory->create()
            ->setAttributeCode($tokenAttribute)
            ->setValue(serialize($token));

        $customer->setCustomAttributes([
            $idAttribute,
            $tokenAttribute
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

        $customerData = [
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
        ];

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

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
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
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerByEmail($email)
    {
        try {
            $this->customerRepository->get($email);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param $idAttribute
     * @param $tokenAttribute
     * @throws \Exception
     */
    public function disconnectByCustomer(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $idAttribute,
        $tokenAttribute
    )
    {
        $idAttribute = $this->customAttributeValueFactory->create()
            ->setAttributeCode($idAttribute)
            ->setValue(null);

        $tokenAttribute = $this->customAttributeValueFactory->create()
            ->setAttributeCode($tokenAttribute)
            ->setValue(null);

        $customer->setCustomAttributes([
            $idAttribute,
            $tokenAttribute
        ]);

        $this->customerRepository->save($customer);
    }
}