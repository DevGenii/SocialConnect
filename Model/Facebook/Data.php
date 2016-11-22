<?php

namespace DevGenii\SocialConnect\Model\Facebook;

class Data extends \Magento\Framework\DataObject
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
     * Data constructor.
     * @param Client $client
     * @param array $params
     * @param string $target
     * @param array $data
     */
    public function __construct(
        \DevGenii\SocialConnect\Model\Facebook\Client $client,
        array $params = [],
        $target = '/me',

        // Parent
        array $data = [])
    {
        // Constructor params
        $this->client = $client;
        $this->params += $params;
        $this->target = $target;

        parent::__construct($data);
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
    protected function load()
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