<?php

namespace DevGenii\SocialConnect\Controller\Facebook;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class Connect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     *
     * @var \DevGenii\SocialConnect\Helper\Facebook
     */
    protected $helperFacebook;

    /**
     *
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helperData;

    /**
     * @var \DevGenii\SocialConnect\Model\Facebook\DataFactory
     */
    protected $dataFactory;

    /**
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DevGenii\SocialConnect\Helper\Facebook $helperFacebook
     * @param \DevGenii\SocialConnect\Helper\Data $helperData
     * @param \DevGenii\SocialConnect\Model\Facebook\DataFactory $dataFactory
     * @param AccountRedirect $accountRedirect
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \DevGenii\SocialConnect\Helper\Facebook $helperFacebook,
        \DevGenii\SocialConnect\Helper\Data $helperData,
        \DevGenii\SocialConnect\Model\Facebook\DataFactory $dataFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        AccountRedirect $accountRedirect,

        // Parent
        \Magento\Framework\App\Action\Context $context)
    {
        $this->accountRedirect = $accountRedirect;
        $this->customerSession = $customerSession;
        $this->helperFacebook = $helperFacebook;
        $this->helperData = $helperData;
        $this->dataFactory = $dataFactory;

        parent::__construct($context);
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->connectCallback();
        } catch (\DevGenii\SocialConnect\Model\Facebook\Client\Exception $e) {
            $this->messageManager->addNoticeMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $return = array(
            'redirect' => $this->accountRedirect->getRedirect()
        );

        echo json_encode($return);
    }

    /**
     * @throws \Exception
     */
    protected function connectCallback() {
        $state = $this->getRequest()->getParam('state');
        $accessToken = $this->getRequest()->getParam('access_token');
        $expiresIn = $this->getRequest()->getParam('expires_in');

        if( !$expiresIn ||
            !$accessToken ||
            !$state ||
            $state != $this->customerSession->getFacebookCsrf()) {
            // Direct route access - deny

            throw new \Exception(
                __('Security check failed.')
            );
        }

        $token = new \stdClass();
        $token->access_token = $accessToken;
        $token->expires = $expiresIn;

        $data = $this->dataFactory->create($token);

        // Reload access token in case it got extended
        $token = $data->getAccessToken();

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customerDataByFacebookId */
        $customerDataByFacebookId = $this->helperFacebook->getCustomerById($data->getId());
        if($this->customerSession->isLoggedIn()) {
            // Logged in user
            if($customerDataByFacebookId) {
                // Facebook account already connected to other account - deny
                $this->messageManager->addNoticeMessage(
                    __('Your Facebook account is already connected to one of our store accounts.')
                );

                return $this;
            }

            // Connect from account dashboard - attach
            $customer = $this->customerSession->getCustomer()->getDataModel();
            $this->helperFacebook->connectByCustomer(
                $data->getId(),
                $token,
                $customer
            );

            $this->messageManager->addSuccessMessage(
                __('Your Facebook account is now connected to your store account. You can now login using our Facebook '.
                    'Login button or using store account credentials you will receive to your email address.')
            );

            return $this;
        }

        if($customerDataByFacebookId && $customerDataByFacebookId->getId()) {
            // Existing connected user - login
            $this->customerSession->setCustomerDataAsLoggedIn($customerDataByFacebookId);

            $this->messageManager->addSuccessMessage(
                __('You have successfully logged in using your Facebook account.')
            );

            return $this;
        }

        $customerByEmail = $this->helperData->getCustomerByEmail($data->getEmail());
        if($customerByEmail && $customerByEmail->getId()) {
            // Email account already exists - attach, login
            $customer = $customerByEmail->getDataModel();
            $this->helperFacebook->connectByCustomer(
                $data->getId(),
                $token,
                $customer
            );

            // Log customer in
            $this->customerSession->setCustomerAsLoggedIn($customer);

            $this->messageManager->addSuccessMessage(
                __('We have discovered you already have account at our store. Your Facebook account is now connected '.
                    'to your store account.')
            );

            return $this;
        }

        // New connection - create, attach, login

        $email = $data->getEmail();
        if(!$email) {
            throw new \Exception(
                __('Sorry, could not retrieve your Facebook last name. Please try again.')
            );
        }

        $firstName = $data->getFirstName();
        if(!$firstName) {
            throw new \Exception(
                __('Sorry, could not retrieve your Facebook first name. Please try again.')
            );
        }

        $lastName = $data->getLastName();
        if(!$lastName) {
            throw new \Exception(
                __('Sorry, could not retrieve your Facebook last name. Please try again.')
            );
        }

        $this->helperFacebook->connectByCreatingAccount(
            $data->getId(),
            $token,
            $email,
            $firstName,
            $lastName
        );

        $this->messageManager->addSuccessMessage(
            __('Your Facebook account is now connected to your new customer account at our store. Now you can login using '.
                'our Facebook Login button and optionally set a password on your customer account using link you will '.
                'receive to your email shortly.')
        );
    }
}