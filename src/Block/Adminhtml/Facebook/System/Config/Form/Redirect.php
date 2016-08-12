<?php

namespace DevGenii\SocialConnect\Block\Adminhtml\Facebook\System\Config\Form;

class Redirect extends \DevGenii\SocialConnect\Block\Adminhtml\System\Config\Form\Redirect
{
    protected function _construct()
    {
        parent::_construct();

        $this->setAuthProvider('facebook');
    }
}