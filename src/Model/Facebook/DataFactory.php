<?php
namespace DevGenii\SocialConnect\Model\Facebook;


/**
 * Factory class for @see \DevGenii\SocialConnect\Model\Facebook\Data
 */
class DataFactory
{
    /**
     * Object Manager instance
     *
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Used for caching API results
     *
     * @var array
     */
    protected $instance = [];

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\DevGenii\SocialConnect\Model\Facebook\Data')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param \StdClass $accessToken
     * @return \DevGenii\SocialConnect\Model\Facebook\Data
     */
    public function create(\StdClass $accessToken)
    {
        if(!isset($this->instance[$accessToken->access_token])) {
            /** @var \DevGenii\SocialConnect\Model\Facebook\Data $instance */
            $instance = $this->objectManager->create('\DevGenii\SocialConnect\Model\Facebook\Data');
            $instance->loadByAccessToken($accessToken);
            $this->instance[$accessToken->access_token] = $instance;
        }

        return $this->instance[$accessToken->access_token];
    }
}