<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Base extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * TODO: replace inline CSS
     */
    const STYLE = 'margin: 0 !important; padding-top: 7px;';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Url $url,
        array $data = []
    )
    {
        $this->url = $url;
        parent::__construct($context, $data);
    }

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

    /**
     * Get scope as selected in Store View dropdown
     *
     * @return \Magento\Store\Model\Store
     */
    protected function getFormScope()
    {
        /** @var \Magento\Config\Block\System\Config\Form $form */
        $form = $this->getForm();

        $scope = $form->getScope();
        $scopeId = $form->getScopeId();

        /** @var \Magento\Store\Model\Store $store */
        switch ($scope) {
            case $form::SCOPE_WEBSITES;
                /** @var \Magento\Store\Model\Website $website */
                $website = $this->_storeManager->getWebsite($scopeId);
                $store = $website->getDefaultStore();
                break;
            case $form::SCOPE_STORES:
                $store = $this->_storeManager->getStore($scopeId);
                break;
            case $form::SCOPE_DEFAULT:
            default:
                $store = $this->_storeManager->getDefaultStoreView();
        }

        return $store;
    }
}