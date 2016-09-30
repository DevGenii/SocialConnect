<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevGenii\SocialConnect\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GreetingCommand
 */
class Test extends Command
{
    /**
     * @var \DevGenii\SocialConnect\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Test constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\App\State $appState
     * @param \DevGenii\SocialConnect\Helper\Data $helperData
     * @param null $name
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\State $appState,
        \DevGenii\SocialConnect\Helper\Data $helperData,
        $name = null
    )
    {
        $this->customerRepository = $customerRepository;
        $this->helperData = $helperData;
        $this->appState = $appState;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('socialconnect:test')
            ->setDescription('DevGenii SocialConnect Test command');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('frontend');
//        $customer = $this->customerRepository->getById(1);
//        $customer->setCustomAttribute('devgenii_socialconnect_fid', 123);
//        $this->customerRepository->save($customer);
//        $test = $customer->getCustomAttributes();
//        $output->writeln('Hello!');

//        $customer = $this->helperData->connectByCreatingAccount(
//            'id1231',
//            'token1231',
//            'test2@gmail.com',
//            'Test2',
//            'Test2',
//            \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE,
//            \DevGenii\SocialConnect\Helper\Facebook::TOKEN_ATTRIBUTE
//        );

//        $customer = $this->customerRepository->get('test@gmail.com');
//        $customer->setCustomAttributes([
//            \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE => 'test'
//        ]);
        /** @var \Magento\Framework\Api\AttributeInterface $test */
//        $test = $customer->getCustomAttribute(\DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE);

        //$customer = $this->helperData->getCustomerById('id1231', \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE);

        $image = $this->helperData->getProperDimensionsImageUrl('test123',
            'https://pbs.twimg.com/profile_images/735123529667121153/wnwMgGdX_bigger.jpg',
            \DevGenii\SocialConnect\Helper\Facebook::ID_ATTRIBUTE);

        $test = 1;
    }
}
