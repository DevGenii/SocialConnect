<?php

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
     * CustomConfigProvider constructor.
     * @param ConfigInterface $configFacebook
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\ConfigInterface $configFacebook,
        \DevGenii\SocialConnect\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession)
    {
        $this->configFacebook = $configFacebook;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $appId = $this->configFacebook->getClientId();

        $state = $this->helper->generateCsrfToken();
        $this->customerSession->setFacebookCsrf($state);

        $scope = implode(',', $this->configFacebook->getScope());;

        $config = [
            'devGeniiSocialConnect' => [
                'facebook' => [
                    'appId' => $appId,
                    'state' => $state,
                    'ajaxUrl' => \DevGenii\SocialConnect\Block\Facebook\Button::AJAX_ROUTE_CONNECT,
                    'scope' => $this->configFacebook->getScope()
                ]
            ]
        ];
        return $config;
    }
}