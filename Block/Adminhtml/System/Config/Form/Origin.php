<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Origin extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Base
{
    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $scope = $this->getFormScope();

        return sprintf(
            '<pre style="%s">%s</pre>',
            self::STYLE,
            $this->url->getBaseUrl(
                [
                    '_scope' => $scope,
                    '_nosid' => true
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    protected function _isInheritCheckboxRequired(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return false;
    }
}