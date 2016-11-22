<?php

namespace DevGenii\SocialConnect\Model\Facebook;

interface ConfigInterface
{
    /**
     * @return bool
     */
    public function isReadyToUse();

    /**
     * @return string|null
     */
    public function isEnabled();

    /**
     * @return string|null
     */
    public function getClientId();

    /**
     * @return string|null
     */
    public function getClientSecret();

    /**
     * @return array
     */
    public function getScope();
}