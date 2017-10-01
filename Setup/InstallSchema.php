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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('pmclain_twilio_log'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'ID'
            )
            ->addColumn(
                'sid',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Message SID'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Entity ID'
            )
            ->addColumn(
                'entity_type_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Entity Type ID'
            )
            ->addColumn(
                'recipient_phone',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Recipient Phone Number'
            )
            ->addColumn(
                'is_error',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Result Is Error'
            )
            ->addColumn(
                'result',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Result Text'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Entry Timestamp'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Updated Timestamp'
            )
            ->setComment('Pmclain Twilio Log');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
