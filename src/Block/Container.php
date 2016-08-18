<?php

namespace DevGenii\SocialConnect\Block;

abstract class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * Facebook client model
     *
     * @var \DevGenii\SocialConnect\Model\Facebook\Client
     */
    protected $clientFacebook;

    /**
     * @var \DevGenii\SocialConnect\Helper\Facebook
     */
    protected $helperFacebook;

    /**
     * @param \DevGenii\SocialConnect\Model\Facebook\Client $clientFacebook
     * @param \DevGenii\SocialConnect\Helper\Facebook $helperFacebook
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\Client $clientFacebook,
        \DevGenii\SocialConnect\Helper\Facebook $helperFacebook,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {
        $this->clientFacebook = $clientFacebook;
        $this->helperFacebook = $helperFacebook;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function facebookEnabled()
    {
        return $this->helperFacebook->isReadyToUse();
    }

} 