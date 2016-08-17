<?php

namespace DevGenii\SocialConnect\Helper;

class Facebook extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'customer/devgenii_socialconnect_facebook/enabled';
    const XML_PATH_CLIENT_ID = 'customer/devgenii_socialconnect_facebook/client_id';
    const XML_PATH_CLIENT_SECRET = 'customer/devgenii_socialconnect_facebook/client_secret';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * Facebook constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config
    )
    {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    protected function isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }

    /**
     * @param $xmlPath
     * @return mixed
     */
    public function getStoreConfig($xmlPath)
    {
        return $this->config->getValue($xmlPath);
    }
}