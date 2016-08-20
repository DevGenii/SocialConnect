<?php

namespace DevGenii\SocialConnect\Controller\Facebook;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class Disconnect extends \Magento\Framework\App\Action\Action
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
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @var \DevGenii\SocialConnect\Model\Facebook\Data\CustomerFactory
     */
    protected $customerDataFactory;

    /**
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DevGenii\SocialConnect\Helper\Data $helperData
     * @param AccountRedirect $accountRedirect
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
    AccountRedirect $accountRedirect,
    \Magento\Customer\Model\Session $customerSession,
    \DevGenii\SocialConnect\Helper\Data $helperData,
    \DevGenii\SocialConnect\Model\Facebook\Data\CustomerFactory $customerDataFactory,

    // Parent
    \Magento\Framework\App\Action\Context $context)
    {
        $this->accountRedirect = $accountRedirect;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->customerDataFactory = $customerDataFactory;

        parent::__construct($context);
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->disconnectCallback();
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

    protected function disconnectCallback(\Magento\Customer\Model\Customer $customer) {
        $customerData = $this->customerDataFactory->create();
        $customerData->load();
        $customerData->disconnect();

        $this->messageManager->addSuccessMessage(
            __('You have successfully disconnected your Facebook account from our store account.')
        );
    }
}