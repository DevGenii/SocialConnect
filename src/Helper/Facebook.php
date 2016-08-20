<?php

namespace DevGenii\SocialConnect\Helper;

class Facebook extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ID_ATTRIBUTE = 'devgenii_socialconnect_fid';
    const TOKEN_ATTRIBUTE = 'devgenii_socialconnect_ftoken';

    /** @var  \DevGenii\SocialConnect\Helper\Data */
    protected $dataHelper;

    /**
     * Facebook constructor.
     * @param Data $dataHelper
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \DevGenii\SocialConnect\Helper\Data $dataHelper,

        // Parent
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * @param $facebookId
     * @param \stdClass $facebookToken
     * @param $customerId
     */
    public function connectById(
        $facebookId,
        \stdClass $facebookToken,
        $customerId
    )
    {
        return $this->dataHelper->connectById(
            $facebookId,
            $facebookToken,
            $customerId,
            self::ID_ATTRIBUTE,
            self::TOKEN_ATTRIBUTE
        );
    }

    /**
     * @param $facebookId
     * @param $token
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    public function connectByCreatingAccount(
        $facebookId,
        $token,
        $email,
        $firstName,
        $lastName
    )
    {
        $this->dataHelper->connectByCreatingAccount(
            $facebookId,
            $token,
            $email,
            $firstName,
            $lastName,
            self::ID_ATTRIBUTE,
            self::TOKEN_ATTRIBUTE
        );
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCustomersById(
        $id
    )
    {
        return $this->dataHelper->getCustomersById(
            $id,
            self::ID_ATTRIBUTE
        );
    }

    /**
     * @param $id
     * @param $pictureUrl
     * @return null|string
     */
    public function getProperDimensionsPictureUrl(
        $id,
        $pictureUrl
    )
    {
        return $this->dataHelper->getProperDimensionsPictureUrl(
            $id,
            $pictureUrl,
            self::ID_ATTRIBUTE
        );
    }
}