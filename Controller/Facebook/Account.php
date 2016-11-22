<?php

namespace DevGenii\SocialConnect\Controller\Facebook;
use DevGenii\SocialConnect\Model\Facebook\Client\Exception;
use Magento\Framework\Controller\ResultFactory;

class Account extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \DevGenii\SocialConnect\Model\Facebook\Data\CustomerFactory
     */
    protected $dataFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \DevGenii\SocialConnect\Model\Facebook\Data\CustomerFactory $dataFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\Data\CustomerFactory $dataFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,

        // Parent
        \Magento\Framework\App\Action\Context $context)
    {
        $this->dataFactory = $dataFactory;
        $this->customerSession = $customerSession;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->dataFactory->create();

            // Current user data to be used by the blocks on the page
            $this->registry->register('devgenii_socialconnect_facebook_data', $data);
        } catch (\Exception $e) {
            // Not connected or connected but not valid
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Facebook Connect'));
        return $resultPage;
    }
}