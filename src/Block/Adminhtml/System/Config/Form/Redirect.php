<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Redirect extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Base
{
    /**
     * @var null|string
     */
    protected $authProvider = null;

    /**
     * @return null|string
     */
    protected function getAuthProvider()
    {
        return $this->authProvider;
    }

    /**
     * @param string $authProvider
     */
    protected function setAuthProvider($authProvider)
    {
        $this->authProvider = $authProvider;
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if($this->getAuthProvider()) {
            return sprintf(
                '<pre style="margin: 0 !important; padding-top: 7px;">%ssocialconnect/%s/connect/</pre>',
                $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB),
                $this->getAuthProvider()
            );
        }
    }
}