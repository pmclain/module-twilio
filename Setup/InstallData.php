<?php
/**
 * Pmclain_Twilio extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GPL v3 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl.txt
 *
 * @category       Pmclain
 * @package        Twilio
 * @copyright      Copyright (c) 2017
 * @license        https://www.gnu.org/licenses/gpl.txt GPL v3 License
 */

namespace Pmclain\Twilio\Setup;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    protected function getAddressSmsAlert()
    {
        return [
            'label' => 'SMS Notifications',
            'type' => 'int',
            'input' => 'boolean',
            'required' => false,
            'sort_order' => 125,
            'position' => 125,
            'system' => false,
            'is_user_defined',
            'visible' => true,
        ];
    }

    protected function addAttributeToAllForm($attributeId)
    {
        foreach ([
                     'adminhtml_customer_address',
                     'customer_address_edit',
                     'customer_register_address'
                 ] as $formCode) {
            $this->setup->getConnection()
                ->insertMultiple(
                    $this->setup->getTable('customer_form_attribute'),
                    ['form_code' => $formCode, 'attribute_id' => $attributeId]
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->setup = $setup;
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            'sms_alert',
            $this->getAddressSmsAlert()
        );
        $this->quoteSetupFactory->create()->addAttribute(
            'quote_address',
            'sms_alert',
            ['type' => Table::TYPE_INTEGER]
        );
        $this->salesSetupFactory->create()->addAttribute(
            'order_address',
            'sms_alert',
            ['type' => Table::TYPE_INTEGER]
        );

        $this->addAttributeToAllForm($eavSetup->getAttributeId(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            'sms_alert'
        ));
    }
}
