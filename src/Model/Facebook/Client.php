<?php

namespace DevGenii\SocialConnect\Model\Facebook;

class Client extends \Magento\Framework\DataObject
{
    const REDIRECT_URI_ROUTE = 'socialconnect/facebook/connect';

    const OAUTH2_SERVICE_URI = 'https://graph.facebook.com';
    const OAUTH2_AUTH_URI = 'https://graph.facebook.com/oauth/authorize';
    const OAUTH2_TOKEN_URI = 'https://graph.facebook.com/oauth/access_token';

    /**
     *
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     *
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @var string
     */
    protected $clientId = null;


    /**
     * @var mixed
     */
    protected $clientSecret = null;

    /**
     *
     * @var string
     */
    protected $redirectUri = null;

    /**
     *
     * @var string
     */
    protected $state = null;

    /**
     *
     * @var array
     */
    protected $scope = [
        'public_profile',
        'email',
        'user_birthday'
    ];

    /**
     *
     * @var \StdClass
     */
    protected $token;


    /**
     *
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \DevGenii\SocialConnect\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
            \Magento\Framework\UrlInterface $url,
            \DevGenii\SocialConnect\Helper\Data $helperData,

            // Parent
            array $data = [])
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->url = $url;
        $this->redirectUri = $this->url->sessionUrlVar(
            $this->url->getUrl(self::REDIRECT_URI_ROUTE)
        );
        $this->helperData = $helperData;
        $this->clientId = $this->getClientId();
        $this->clientSecret = $this->getClientSecret();

        parent::__construct($data);
    }

    /**
     * @return mixed|string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return mixed|string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param \StdClass $token
     * @throws \Exception
     */
    public function setAccessToken(\StdClass $token)
    {
        $this->token = $token;

        $this->extendAccessToken();
    }

    /**
     * @return \StdClass
     */
    public function getAccessToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        $url =
        self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                [
                    'client_id' => $this->getClientId(),
                    'redirect_uri' => $this->getRedirectUri(),
                    'state' => $this->getState(),
                    'scope' => implode(',', $this->getScope())
                ]
            );
        return $url;
    }

    /**
     * @param $endpoint
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     * @throws \Exception
     */
    public function api($endpoint, $method = 'GET', $params = [])
    {
        $accessToken = $this->getAccessToken();

        if(!$accessToken) {
            throw new \Exception(
                __('Unable to retrieve access token.')
            );
        }

        $url = self::OAUTH2_SERVICE_URI.$endpoint;

        $params = array_merge(
            [
            'access_token' => $accessToken->access_token
            ],
            [
            'fields' => implode(',', $params)
            ]
        );

        $response = $this->httpRequest($url, strtoupper($method), $params);

        return $response;
    }

    /**
     * @param null $code
     * @return \StdClass
     * @throws Exception
     * @throws \Exception
     */
    protected function fetchAccessToken($code = null)
    {
        if(!$code) {
            throw new \Exception(
                __('Unable to retrieve access code.')
            );
        }

        $response = $this->httpRequest(
            self::OAUTH2_TOKEN_URI,
            'POST',
            [
                'code' => $code,
                'redirect_uri' => $this->getRedirectUri(),
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'grant_type' => 'authorization_code'
            ]
        );

        $this->setAccessToken($response);

        return $this->getAccessToken();
    }


    /**
     * @return mixed
     * @throws Exception
     * @throws \Exception
     */
    public function extendAccessToken()
    {
        $accessToken = $this->getAccessToken();

        if(!$accessToken) {
        throw new \Exception(
                __('Unable to retrieve access token.')
            );
        }

        // Expires over two hours means long lived token
        if($accessToken->expires > 7200) {
            // Long lived token, no need to extend
            return $this->getAccessToken();
        }

        $response = $this->httpRequest(
            self::OAUTH2_TOKEN_URI,
            'GET',
            [
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'fb_exchange_token' => $this->getAccessToken()->access_token,
                'grant_type' => 'fb_exchange_token'
            ]
        );

        $this->setAccessToken($response);

        return $this->getAccessToken();
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     * @throws \Exception
     * @throws \Zend_Http_Client_Exception
     */
    protected function httpRequest($url, $method = 'GET', $params = [])
    {
        $client = $this->httpClientFactory->create();

        $client->setUri($url);

        switch ($method) {
            case 'GET':
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setParameterGet($params);
                break;
            default:
                throw new \Exception(
                    __('Required HTTP method is not supported.')
                );
        }

        $response = $client->request($method);

        $this->helperData->log($response->getStatus().' - '. $response->getBody());

        $decodedResponse = json_decode($response->getBody());

        /*
         * Per http://tools.ietf.org/html/draft-ietf-oauth-v2-27#section-5.1
         * Facebook should return data using the "application/json" mime type.
         * Facebook violates OAuth2 specification and returns string. If this
         * ever gets fixed, following condition will stop being used.
         */
        if(empty($decodedResponse)) {
            $parsedResponse = [];
            parse_str($response->getBody(), $parsedResponse);

            $decodedResponse = json_decode(json_encode($parsedResponse));
        }

        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401)) {
                if(isset($decodedResponse->error->message)) {
                    $message = $decodedResponse->error->message;
                } else {
                    $message = __('Unspecified OAuth error occurred.');
                }

                throw new \Exception($message);
            } else {
                $message = sprintf(
                    __('HTTP error %d occurred while issuing request.'),
                    $status
                );

                throw new \Exception($message);
            }
        }

        return $decodedResponse;
    }
}