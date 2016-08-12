<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form;

class Link extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Base
{
    /**
     * @var null|string
     */
    protected $linkTitle = null;

    /**
     * @var null|string
     */
    protected $linkHref = null;


    /**
     * @return null|string
     */
    protected function getLinkTitle()
    {
        return $this->linkTitle;
    }

    /**
     * @return null|string
     */
    protected function getLinkHref()
    {
        return $this->linkHref;
    }

    /**
     * @param string $linkTitle
     */
    protected function setLinkTitle($linkTitle)
    {
        $this->linkTitle = $linkTitle;
    }

    /**
     * @param string $linkHref
     */
    protected function setLinkHref($linkHref)
    {
        $this->linkHref = $linkHref;
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return sprintf(
            '<div style="padding-top: 7px;"><a href="%s" target="_blank">%s</a></div>',
            $this->getLinkHref(),
            $this->getLinkTitle()
        );
    }
}