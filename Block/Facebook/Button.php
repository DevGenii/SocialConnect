<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Block\Facebook;

class Button extends \Magento\Framework\View\Element\Template
{
    const AJAX_ROUTE_CONNECT = 'socialconnect/facebook/connect';
    const AJAX_ROUTE_DISCONNECT = 'socialconnect/facebook/disconnect';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \DevGenii\SocialConnect\Model\Facebook\ConfigInterface
     */
    protected $configFacebook;

    /**
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helper;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook
     * @param \Magento\Framework\Registry $registry
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook,
        \Magento\Framework\Registry $registry,
        \DevGenii\SocialConnect\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {

        $this->configFacebook = $configFacebook;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
        /** @var \DevGenii\SocialConnect\Model\Facebook\Data\Customer|null $data */
        $data = $this->registry->registry('devgenii_socialconnect_facebook_data');

        if (is_null($data)) {
            // No user info, see if we have something set through layout
            if (!($text = $this->getData('button_text'))) {
                // "Connect" is fallback used when text isn't set through layout
                $text = __('Connect');
            }
        } else {
            $text = __('Disconnect');
        }

        return $text;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->configFacebook->getScope();
    }

    /**
     * @return mixed|string
     */
    public function getAppId()
    {
        return $this->configFacebook->getClientId();
    }

    /**
     * @return string
     */
    public function getState()
    {
        // CSRF protection
        $csrf = $this->helper->generateCsrfToken();

        $this->customerSession->setFacebookCsrf($csrf);

        return $csrf;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        /** @var \DevGenii\SocialConnect\Model\Facebook\Data\Customer|null $data */
        $data = $this->registry->registry('devgenii_socialconnect_facebook_data');

        if (is_null($data)) {
            return $this->getUrl(self::AJAX_ROUTE_CONNECT);
        } else {
            return $this->getUrl(self::AJAX_ROUTE_DISCONNECT);
        }


    }
}