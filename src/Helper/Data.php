<?php

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
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Math\Random $random
     *
     * * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Math\Random $random,

        // Parent
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_appState = $appState;
        $this->random = $random;
        parent::__construct($context);
    }

    /**
     * @param $message
     * @param int $level
     */
    public function log($message, $level = \Zend_Log::DEBUG)
    {
        if($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            $this->_logger->log($message, $level);
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
     * @param $customerId
     * @param $idAttribute
     * @param $tokenAttribute
     */
    public function connectById(
        $id,
        \stdClass $token,
        $customerId,
        $idAttribute,
        $tokenAttribute
    )
    {
        $customerDetailsObject = $this->_customerAccountService->getCustomerDetails($customerId);
        /* @var $customerDetailsObject \Magento\Customer\Service\V1\Data\CustomerDetails */

        $customerDataObject = $customerDetailsObject->getCustomer();
        /* @var $customerDetailsObject \Magento\Customer\Service\V1\Data\Customer */

        // Merge old and new data
        $customerDetailsArray = array_merge(
            $customerDataObject->__toArray(),
            array('custom_attributes' =>
                array(
                    array(
                        \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_fid',
                        \Magento\Framework\Service\Data\AttributeValue::VALUE => $facebookId
                    ),
                    array(
                        \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_ftoken',
                        \Magento\Framework\Service\Data\AttributeValue::VALUE => serialize($token)
                    )
                )
            )
        );

        // Pass result to customerBuilder
        $this->_customerBuilder->populateWithArray($customerDetailsArray);

        // Pass result to customerDetailsBuilder
        $this->_customerDetailsBuilder->setCustomer($this->_customerBuilder->create());

        // Update customer
        $this->_customerAccountService->updateCustomer($customerId, $this->_customerDetailsBuilder->create());

        // Set customer as logged in
        $this->_customerSession->setCustomerDataAsLoggedIn($customerDataObject);
    }

    /**
     * @param $id
     * @param $token
     * @param $email
     * @param $firstName
     * @param $lastName
     * @param $idAttribute
     * @param $tokenAttribute
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
        $customerDetails = array(
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'sendemail' => 0,
            'confirmation' => 0,
            'custom_attributes' => array(
                array(
                    \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_fid',
                    \Magento\Framework\Service\Data\AttributeValue::VALUE => $facebookId
                ),
                array(
                    \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_ftoken',
                    \Magento\Framework\Service\Data\AttributeValue::VALUE => serialize($token)
                )
            )
        );

        $customer = $this->_customerBuilder->populateWithArray($customerDetails)
            ->create();

        // Save customer
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customer)
            ->setAddresses(null)
            ->create();

        $customerDataObject = $this->_customerAccountService->createCustomer($customerDetails);
        /* @var $customer \Magento\Customer\Service\V1\Data\Customer */

        // Convert data object to customer model
        $customer = $this->_converter->createCustomerModel($customerDataObject);
        /* @var $customer \Magento\Customer\Model\Customer */

        $customer->sendNewAccountEmail('confirmed', '');

        $this->_customerSession->setCustomerAsLoggedIn($customer);
    }

    public function loginByCustomer(
        \Magento\Customer\Model\Customer $customer
    )
    {
        if($customer->getConfirmation()) {
            $customer->setConfirmation(null);
            $customer->save();
        }

        $this->_customerSession->setCustomerAsLoggedIn($customer);
    }

    public function getCustomersById(
        $id,
        $idAttribute
    )
    {
        $customer = $this->_customerFactory->create();

        $collection = $customer->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('inchoo_socialconnect_fid', $facebookId)
            ->setPage(1, 1);

        return $collection;
    }

    public function getCustomersByEmail($email)
    {
        $customer = $this->_customerFactory->create();

        $collection = $customer->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('email', $email)
            ->setPage(1, 1);

        return $collection;
    }

    public function getProperDimensionsPictureUrl(
        $id,
        $pictureUrl,
        $idAttribute
    )
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'/inchoo/socialconnect/facebook/'.$facebookId;

        $filename = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).$facebookId;

        $directory = dirname($filename);

        if (!file_exists($directory) || !is_dir($directory)) {
            if (!@mkdir($directory, 0777, true))
                return null;
        }

        if(!file_exists($filename) ||
            (file_exists($filename) && (time() - filemtime($filename) >= 3600))) {
            $client = $this->_httpClientFactory->create($pictureUrl);
            $client->setStream();
            $response = $client->request('GET');
            stream_copy_to_stream($response->getStream(), fopen($filename, 'w'));

            $imageObj = $this->_imageFactory->create($filename);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(150, 150);
            $imageObj->save($filename);
        }

        return $url;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function disconnect(\Magento\Customer\Model\Customer $customer, $idAttribute)
    {
        // TODO: Move to \Inchoo\SocialConnect\Model\Facebook\Info\User
        try {
            $this->_client->setAccessToken(unserialize($customer->getInchooSocialconnectFtoken()));
            $this->_client->api('/me/permissions', 'DELETE');
        } catch (Exception $e) {}

        $pictureFilename = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            )
            .'/inchoo/socialconnect/facebook/'
            .$customer->getInchooSocialconnectFid();

        if(file_exists($pictureFilename)) {
            @unlink($pictureFilename);
        }

        $customer->setInchooSocialconnectFid(null)
            ->setInchooSocialconnectFtoken(null)
            ->save();
    }
}