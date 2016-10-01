<?php

namespace DevGenii\SocialConnect\Controller\Facebook;
use Magento\Framework\Controller\ResultFactory;

class Account extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Facebook Connect'));
        return $resultPage;
    }
}