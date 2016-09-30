<?php

namespace DevGenii\SocialConnect\Helper;

class Facebook extends \Magento\Framework\App\Helper\AbstractHelper implements ProviderInterface
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
     * @param $id
     * @param \stdClass $token
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     */
    public function connectByCustomer(
        $id,
        \stdClass $token,
        $customer
    )
    {
        return $this->dataHelper->connectByCustomer(
            $id,
            $token,
            $customer,
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
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerById(
        $id
    )
    {
        return $this->dataHelper->getCustomerById(
            $id,
            self::ID_ATTRIBUTE
        );
    }
}