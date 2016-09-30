<?php

namespace DevGenii\SocialConnect\Helper;

interface ProviderInterface
{
    /**
     * @param $id
     * @param \stdClass $token
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     */
    public function connectByCustomer(
        $id,
        \stdClass $token,
        $customer
    );

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
    );

    /**
     * @param $id
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerById(
        $id
    );
}