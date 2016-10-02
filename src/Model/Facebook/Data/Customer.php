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
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     * @param \DevGenii\SocialConnect\Helper\Facebook $helperFacebook
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,

        // Parent
        \DevGenii\SocialConnect\Model\Facebook\Client $client,
        array $params = [],
        $target = 'me',
        array $data = [])
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->helperFacebook = $helperFacebook;
        $this->storeManager = $storeManager;

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

        $socialConnectFid = $this->customer->getCustomAttribute(
            \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE
        )->getValue();

        $socialConnectFtoken = $this->customer->getCustomAttribute(
            \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
        )->getValue();

        if(!$socialConnectFid|| !$socialConnectFtoken) {
            throw new \Exception(
                __('Could not retrieve token by customer id')
            );
        }

        $this->setTarget($socialConnectFid);
        $this->setAccessToken($socialConnectFtoken);
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

        /** @var \Magento\Customer\Api\Data\CustomerInterface  $customer */
        $this->customer = $this->customerSession->getCustomer()->getDataModel();
        if(!$this->customer->getId()) {
            throw new \Exception(
                __('Could not load by customer id')
            );
        }

        if(!($facebookId = $this->customer->getCustomAttribute(
            \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE
            )) ||
            !($facebookToken = $this->customer->getCustomAttribute(
                \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
            ))) {
            throw new \Exception(
                __('Could not retrieve token for current customer')
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
        $this->disconnect();

        parent::onException($e);
    }

    /**
     * Disconnect customer data customer
     */
    public function disconnect()
    {
        try {
            $this->client->setAccessToken(unserialize($this->customer->getCustomAttribute(
                \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
                ))
            );
            $this->client->api('/me/permissions', 'DELETE');
        } catch (\Exception $e) {
            // Best effort attempt to revoke permissions
        }

        $pictureFilename = $this->storeManager->getStore()->getUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            )
            .'/devgenii/socialconnect/facebook/'
            .$this->customer->getCustomAttribute(\DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE);

        if(file_exists($pictureFilename)) {
            @unlink($pictureFilename);
        }

        $this->customer->setCustomAttribute(\DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE, null)
            ->setCustomAttribute(\DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE, null);

        $this->customerRepository->save($this->customer);
    }
}