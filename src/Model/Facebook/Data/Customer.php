<?php

namespace DevGenii\SocialConnect\Model\Facebook\Data;

class Customer extends \DevGenii\SocialConnect\Model\Facebook\Data
{
    /**
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     *
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helper;

    /**
     *
     * @var \DevGenii\SocialConnect\Helper\Facebook
     */
    protected $helperFacebook;

    /**
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     * @param \DevGenii\SocialConnect\Helper\Facebook $helperFacebook
     * @param \DevGenii\SocialConnect\Model\Facebook\Client $client
     * @param array $params
     * @param string $target
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \DevGenii\SocialConnect\Helper\Data $helper,
        \DevGenii\SocialConnect\Helper\Facebook $helperFacebook,

        // Parent
        \DevGenii\SocialConnect\Model\Facebook\Client $client,
        array $params = [],
        $target = 'me',
        array $data = array())
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->helperFacebook = $helperFacebook;
        parent::__construct($client, $params, $target, $data);
    }


    public function loadByCustomerId($customerId)
    {
        $this->customer = $this->customerRepository->getById($customerId);

        if(!$this->customer->getId()) {
            throw new \Exception(
                __('Could not load by customer id')
            );
        }

        if(!($socialconnectFid = $this->customer->getCustomAttribute('devgenii_socialconnect_fid')) ||
            !($socialconnectFtoken = $this->customer->getCustomAttribute('devgenii_socialconnect_ftoken'))) {
            throw new \Exception(
                __('Could not retrieve token by customer id')
            );
        }

        $this->setTarget($socialconnectFid);
        $this->setAccessToken($socialconnectFtoken);
        $this->load();

        return $this;
    }

    /**
     * Load customer user data
     *
     * @throws \Exception
     * @return \DevGenii\SocialConnect\Model\Facebook\Data\Customer
     */
    public function load()
    {
        if(!$this->customerSession->isLoggedIn() && !$this->customer->getId()) {
            throw new \Exception(
                __('Could not load customer data since customer isn\'t logged in')
            );
        }

        $this->customer = $this->customerSession->getCustomer();
        if(!$this->customer->getId()) {
            throw new \Exception(
                __('Could not load by customer id')
            );
        }

        if(!($facebookId = $this->customer->getExtensionAttributes()->getDevgeniiSocialconnectFid()) ||
            !($facebookToken = $this->customer->getExtensionAttributes()->getDevgeniiSocialconnectFtoken())) {
            throw new \Exception(
                __('Could not retrieve token by customer id')
            );
        }

        $this->setAccessToken($facebookToken);
        parent::load();

        return $this;
    }

    /**
     *
     * @param \Exception $e
     * @throws \Exception
     */
    protected function onException(\Exception $e)
    {
        $this->helperFacebook->disconnect($this->customer);

        parent::onException($e);
    }
    
}