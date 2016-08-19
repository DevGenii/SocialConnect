<?php

namespace DevGenii\SocialConnect\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Math\Random $random
     *
     * * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Math\Random $random,

        // Parent
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_appState = $appState;
        $this->random = $random;
        parent::__construct($context);
    }

    /**
     * @param $message
     * @param int $level
     */
    public function log($message, $level = \Zend_Log::DEBUG)
    {
        if($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            $this->_logger->log($message, $level);
        }
    }

    /**
     * Generate random string to be used as CSRF token
     *
     * @return string
     */
    public function generateCsrfToken()
    {
        return $this->random->getRandomString(32);
    }
}