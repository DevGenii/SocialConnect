<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Model\Facebook;

class Config implements \DevGenii\SocialConnect\Model\Facebook\ConfigInterface
{
    const XML_PATH_ENABLED = 'devgenii_socialconnect/facebook/enabled';
    const XML_PATH_CLIENT_ID = 'devgenii_socialconnect/facebook/client_id';
    const XML_PATH_CLIENT_SECRET = 'devgenii_socialconnect/facebook/client_secret';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    protected $scope = [
        'public_profile',
        'email',
        'user_birthday'
    ];

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * Facebook constructor.
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->config = $config;
        $this->encryptor = $encryptor;
    }

    /**
     * @return bool
     */
    public function isReadyToUse()
    {
        return $this->isEnabled() && $this->getClientId() && $this->getClientSecret();
    }

    /**
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->encryptor->decrypt($this->getStoreConfig(self::XML_PATH_CLIENT_SECRET));
    }

    /**
     * @param string $xmlPath
     * @return string|null
     */
    protected function getStoreConfig($xmlPath)
    {
        return $this->config->getValue($xmlPath);
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }
}