<?php

namespace DevGenii\SocialConnect\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
   /**
    * EAV setup factory
    *
    * @var EavSetupFactory
    */
    private $eavSetupFactory;

    /**
     * Required attributes
     *
     * @var array
     */
    protected $customerAttributes = [
        'devgenii_socialconnect_fid' => [
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'system' => false   // Must be non system, else customer service can not update it
        ],
        'devgenii_socialconnect_ftoken' => [
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'system' => false   // Must be non system, else customer service can not update it
        ]
    ];

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(
            [
                'setup' => $setup
            ]
        );

        foreach($this->customerAttributes as $customerAttributeCode => $customerAttributeData) {
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                $customerAttributeCode,
                $customerAttributeData
            );
        }
    }
}
