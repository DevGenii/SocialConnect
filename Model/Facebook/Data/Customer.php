<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Model\Facebook\Data;

class Customer
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
     * @var \DevGenii\SocialConnect\Model\Facebook\Data
     */
    protected $data;

    /**
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DevGenii\SocialConnect\Helper\Data $helper
     * @param \DevGenii\SocialConnect\Helper\Facebook $helperFacebook
     * @param \DevGenii\SocialConnect\Model\Facebook\Data $data
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \DevGenii\SocialConnect\Helper\Data $helper,
        \DevGenii\SocialConnect\Helper\Facebook $helperFacebook,
        \DevGenii\SocialConnect\Model\Facebook\Data $data)
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->helperFacebook = $helperFacebook;
        $this->data = $data;
    }

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string     $key
     * @param string|int $index
     * @return mixed
     */

    public function getData($key = '', $index = null)
    {
        return $this->data->getData($key, $index);
    }

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return \DevGenii\SocialConnect\Model\Facebook\Data
     */
    public function setData($key, $value = null)
    {
        $this->data->setData($key, $value);
        return $this->data;
    }

    /**
     * Unset data from the object.
     *
     * @param null|string|array $key
     * @return \DevGenii\SocialConnect\Model\Facebook\Data
     */
    public function unsetData($key = null)
    {
        $this->data->unsetData($key);
        return $this->data;
    }

    /**
     * If $key is empty, checks whether there's any data in the object
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')
    {
        return $this->data->hasData($key);
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        return $this->data->{$method}($args);
    }

    /**
     *
     * @param \StdClass $token Access token
     */
    public function setAccessToken(\StdClass $token)
    {
        $this->data->setAccessToken($token);
    }

    /**
     * Get Facebook client's access token
     *
     * @return \stdClass
     */
    public function getAccessToken()
    {
        return $this->data->getAccessToken();
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->data->setParams($params);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->data->getParams();
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->data->setTarget($target);
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->data->getTarget();
    }

    /**
     * * Load customer user data by customer id
     *
     * @param $customerId
     * @return $this
     * @throws \Exception
     */
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

        $this->data->setTarget($socialConnectFid);
        $this->data->setAccessToken($socialConnectFtoken);

        try{
            $this->data->load();
        } catch(\Exception $e) {
            $this->onException($e);
        }

        return $this;
    }

    /**
     * Load current session customer user data
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

        if(
            !($facebookId = $this->customer->getCustomAttribute(
                \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE
            )) ||
            !($facebookId = $facebookId->getValue()) ||
            !($facebookToken = $this->customer->getCustomAttribute(
                \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
            )) ||
            !($facebookToken = $facebookToken->getValue()) ||
            !($facebookToken = unserialize($facebookToken))
        ) {
            throw new \Exception(
                __('Could not retrieve token for current customer')
            );
        }

        $this->data->setAccessToken($facebookToken);

        try{
            $this->data->load();
        } catch(\Exception $e) {
            $this->onException($e);
        }

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

        throw $e;
    }

    /**
     * Disconnect customer data customer
     */
    public function disconnect()
    {
        try {
            $this->data->setAccessToken(unserialize($this->customer->getCustomAttribute(
                \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
                )->getValue())
            );
            $this->data->delete();
        } catch (\Exception $e) {
            // Best effort attempt to revoke permissions
        }

        $this->helperFacebook->disconnectByCustomer($this->customer);
    }
}