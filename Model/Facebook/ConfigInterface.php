<?php
/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */

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