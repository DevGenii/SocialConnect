<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Link;

class Support extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Link
{
    const CONTACT_URL_HREF = 'https://devgenii.com';
    const CONTACT_URL_TITLE = 'DevGenii - Awesome Magento e-commerce Solutions';

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        parent::_construct();

        $this->setLinkHref(self::CONTACT_URL_HREF);
        $this->setLinkTitle(self::CONTACT_URL_TITLE);
    }
}