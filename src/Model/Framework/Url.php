<?php

namespace DevGenii\SocialConnect\Model\Framework;
use Magento\Framework\UrlInterface;

class Url extends \Magento\Framework\Url
{
    /**
     * Retrieve is secure mode URL
     *
     * Works around Magento 2 issue #6175
     *
     * @return bool
     */
    protected function _isSecure()
    {
        /*
         * Magento 2 issue #6175 "Unable to generate unsecure URL if current URL is secure"
         * https://github.com/magento/magento2/issues/6175
         */
//        if ($this->_request->isSecure()) {
//            return true;
//        }

        if ($this->getRouteParamsResolver()->hasData('secure_is_forced')) {
            return (bool) $this->getRouteParamsResolver()->getData('secure');
        }

        if (!$this->_getScope()->isUrlSecure()) {
            return false;
        }

        if (!$this->getRouteParamsResolver()->hasData('secure')) {
            if ($this->_getType() == UrlInterface::URL_TYPE_LINK) {
                $pathSecure = $this->_urlSecurityInfo->isSecure('/' . $this->_getActionPath());
                $this->getRouteParamsResolver()->setData('secure', $pathSecure);
            } elseif ($this->_getType() == UrlInterface::URL_TYPE_STATIC) {
                $isRequestSecure = $this->_getRequest()->isSecure();
                $this->getRouteParamsResolver()->setData('secure', $isRequestSecure);
            } else {
                $this->getRouteParamsResolver()->setData('secure', true);
            }
        }

        return $this->getRouteParamsResolver()->getData('secure');
    }
}