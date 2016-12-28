<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Model\Facebook;

use Magento\Checkout\Model\ConfigProviderInterface;

class CheckoutButtonConfigProvider implements ConfigProviderInterface
{

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
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * CustomConfigProvider constructor.
     * @param ConfigInterface $configFacebook
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook,
        \DevGenii\SocialConnect\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->configFacebook = $configFacebook;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $enabled = $this->configFacebook->isEnabled();
        $appId = $this->configFacebook->getClientId();

        $state = $this->helper->generateCsrfToken();
        $this->customerSession->setFacebookCsrf($state);

        $scope = implode(',', $this->configFacebook->getScope());;

        $config = [
            'devGeniiSocialConnect' => [
                'facebook' => [
                    'enabled' => $enabled,
                    'appId' => $appId,
                    'state' => $state,
                    'ajaxUrl' => $this->urlBuilder->getUrl(\DevGenii\SocialConnect\Block\Facebook\Button::AJAX_ROUTE_CONNECT),
                    'scope' => $scope
                ]
            ]
        ];
        return $config;
    }
}