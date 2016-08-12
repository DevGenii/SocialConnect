<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Base extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @inheritdoc
     */
    protected function _isInheritCheckboxRequired(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }
}