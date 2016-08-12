<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Origin extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Base
{
    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf(
            '<pre style="margin: 0 !important; padding-top: 7px;">%s</pre>',
            $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)
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