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
            $scope = $this->getFormScope();

            return sprintf(
                '<pre style="%s">%s</pre>',
                self::STYLE,
                $this->url->getUrl(
                    sprintf('socialconnect/%s/connect', $this->getAuthProvider()),
                    [
                        '_scope' => $scope,
                        '_nosid' => true
                    ]
                )
            );
        }
    }
}