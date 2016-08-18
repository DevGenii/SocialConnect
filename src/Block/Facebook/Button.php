<?php

namespace DevGenii\SocialConnect\Block\Facebook;

class Button extends \Magento\Framework\View\Element\Template
{

    const AJAX_ROUTE = 'socialconnect/facebook/connect';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Facebook client model
     *
     * @var \DevGenii\SocialConnect\Model\Facebook\Client
     */
    protected $clientFacebook;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \DevGenii\SocialConnect\Model\Facebook\Client $clientFacebook
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\Client $clientFacebook,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array())
    {

        $this->clientFacebook = $clientFacebook;
        $this->registry = $registry;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        // CSRF protection
        $this->customerSession->setFacebookCsrf($csrf = md5(uniqid(rand(), true)));
        $this->clientFacebook->setState($csrf);
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
        // Get user info for currently logged in user if it already exists
        $data = $this->registry->registry('devgenii_socialconnect_data');

        if (is_null($data) || !$data->hasData()) {
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
        return $this->clientFacebook->getScope();
    }

    /**
     * @return mixed|string
     */
    public function getAppId()
    {
        return $this->clientFacebook->getClientId();
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->clientFacebook->getState();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(self::AJAX_ROUTE);
    }
}