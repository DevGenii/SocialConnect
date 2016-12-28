<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Block;

abstract class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \DevGenii\SocialConnect\Model\Facebook\ConfigInterface
     */
    protected $configFacebook;

    /**
     * @param \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {
        $this->configFacebook = $configFacebook;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function facebookEnabled()
    {
        return $this->configFacebook->isReadyToUse();
    }
} 