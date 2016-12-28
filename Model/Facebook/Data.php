<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace DevGenii\SocialConnect\Model\Facebook;

class Data
{
    /**
     *
     * @var array
     */
    protected $params = [
        'first_name',
        'last_name',
        'email'
    ];

    /**
     * @var string
     */
    protected $target;

    /**
     * Facebook client model
     *
     * @var \DevGenii\SocialConnect\Model\Facebook\Client
     */
    protected $client;

    /**
     * Object attributes
     *
     * @var array
     */
    protected $data = [];

    /**
     * Data constructor.
     * @param Client $client
     * @param array $params
     * @param string $target
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\Client $client,
        array $params = [],
        $target = '/me')
    {
        // Constructor params
        $this->client = $client;
        $this->params += $params;
        $this->target = $target;
    }

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * @param string     $key
     * @return mixed
     */

    public function getData($key = '')
    {
        if ('' === $key) {
            return $this->data;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
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
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Unset data from the object.
     *
     * @param null|string|array $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->setData([]);
        } elseif (is_string($key)) {
            if (isset($this->data[$key]) || array_key_exists($key, $this->data)) {
                unset($this->data[$key]);
            }
        } elseif ($key === (array)$key) {
            foreach ($key as $element) {
                $this->unsetData($element);
            }
        }
        return $this;
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
        if (empty($key) || !is_string($key)) {
            return !empty($this->data);
        }
        return array_key_exists($key, $this->data);
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
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->_underscore(substr($method, 3));
                $index = isset($args[0]) ? $args[0] : null;
                return $this->getData($key, $index);
            case 'set':
                $key = $this->_underscore(substr($method, 3));
                $value = isset($args[0]) ? $args[0] : null;
                return $this->setData($key, $value);
            case 'uns':
                $key = $this->_underscore(substr($method, 3));
                return $this->unsetData($key);
            case 'has':
                $key = $this->_underscore(substr($method, 3));
                return isset($this->data[$key]);
        }

        throw new \Exception(sprintf('Invalid method %s::s', get_class($this), $method));
    }

    /**
     * Converts field names for setters and getters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        return $result;
    }

    /**
     *
     * @param \StdClass $token Access token
     */
    public function setAccessToken(\StdClass $token)
    {
        $this->client->setAccessToken($token);
    }

    /**
     * Get Facebook client's access token
     *
     * @return \stdClass
     */
    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param \StdClass $accessToken
     * @return $this
     */
    public function loadByAccessToken(\StdClass $accessToken)
    {
        $this->setAccessToken($accessToken);
        $this->load();

        return $this;
    }

    /**
     * @throws \DevGenii\SocialConnect\Model\Facebook\Client\Exception
     * @throws \Exception
     */
    public function load()
    {
        try{
            $response = $this->client->api(
                $this->target,
                'GET',
                $this->params
            );

            foreach ($response as $key => $value) {
                $this->setData($key, $value);
            }
        } catch(\DevGenii\SocialConnect\Model\Facebook\Client\Exception $e) {
            $this->onException($e);
        } catch(\Exception $e) {
            $this->onException($e);
        }
    }

    public function delete()
    {
        try{
            $this->client->api('/me/permissions', 'DELETE');
        } catch(\DevGenii\SocialConnect\Model\Facebook\Client\Exception $e) {
            $this->onException($e);
        } catch(\Exception $e) {
            $this->onException($e);
        }
    }

    /**
     *
     * @param \Exception $e
     * @throws \Exception
     */
    protected function onException(\Exception $e)
    {
        throw $e;
    }
}