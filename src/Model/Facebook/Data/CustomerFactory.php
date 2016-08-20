<?php

namespace DevGenii\SocialConnect\Model\Facebook\Data;

/**
 * Factory class for @see \DevGenii\SocialConnect\Model\Facebook\Data\Customer
 */
class CustomerFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var array
     */
    protected $instance = [];

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        $instanceName = '\DevGenii\SocialConnect\Model\Facebook\Data\Customer')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->customerSession = $customerSession;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param int $customerId
     * @throws \Exception
     * @return \DevGenii\SocialConnect\Model\Facebook\Data\Customer
     */
    public function create($customerId = 0)
    {
        if(!isset($this->instance[$customerId])) {
            /** @var \DevGenii\SocialConnect\Model\Facebook\Data\Customer $instance */
            $instance = $this->objectManager->create('\DevGenii\SocialConnect\Model\Facebook\Data\Customer');

            if($customerId) {
                $instance->loadByCustomerId($customerId);
            } else if($this->customerSession->isLoggedIn()) {
                $instance->load();
            } else {
                throw new \Exception(
                    'Could not create customer data object. Please try again.'
                );
            }
        }

        // Currently logged in user info under 0 key
        return $this->instance[$customerId];
    }

}