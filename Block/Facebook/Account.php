<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Block\Facebook;

class Account extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {

        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isConnected()
    {
        /** @var \DevGenii\SocialConnect\Model\Facebook\Data\Customer|null $data */
        $data = $this->registry->registry('devgenii_socialconnect_facebook_data');

        if (is_null($data)) {
            return false;
        } else {
            return true;
        }
    }
}