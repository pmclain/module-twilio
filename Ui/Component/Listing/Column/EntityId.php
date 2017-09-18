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

namespace Pmclain\Twilio\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Sales\Model\Order\ShipmentRepository;

class EntityId extends Column
{
    const ORDER_ENTITY_TYPE_ID = 1;
    const INVOICE_ENTITY_TYPE_ID = 2;
    const SHIPMENT_ENTITY_TYPE_ID = 3;

    /** @var \Magento\Sales\Model\OrderRepository */
    protected $_orderRepository;

    /** @var \Magento\Sales\Model\Order\InvoiceRepository */
    protected $_invoiceRepository;

    /** @var \Magento\Sales\Model\Order\ShipmentRepository */
    protected $_shipmentRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        ShipmentRepository $shipmentRepository,
        array $components = [],
        array $data = []
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_shipmentRepository = $shipmentRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                $incrementId = $this->_getIncrementId(
                    $item['entity_id'],
                    $item['entity_type_id']
                );
                $item[$fieldName] = $incrementId;
            }
        }

        return $dataSource;
    }

    protected function _getIncrementId($entityId, $entityTypeId)
    {
        $incrementId = '';

        switch ($entityTypeId) {
            case self::ORDER_ENTITY_TYPE_ID:
                $incrementId = $this->_getOrderIncrementId($entityId);
                break;
            case self::INVOICE_ENTITY_TYPE_ID:
                $incrementId = $this->_getInvoiceIncrementId($entityId);
                break;
            case self::SHIPMENT_ENTITY_TYPE_ID:
                $incrementId = $this->_getShipmentIncrementId($entityId);
                break;
        }

        return $incrementId;
    }

    protected function _getOrderIncrementId($id)
    {
        $order = $this->_orderRepository->get($id);
        try {
            return $order->getIncrementId();
        } catch (\Exception $e) {
            return '';
        }
    }

    protected function _getInvoiceIncrementId($id)
    {
        $invoice = $this->_invoiceRepository->get($id);
        try {
            return $invoice->getIncrementId();
        } catch (\Exception $e) {
            return '';
        }
    }

    protected function _getShipmentIncrementId($id)
    {
        $shipment = $this->_shipmentRepository->get($id);
        try {
            return $shipment->getIncrementId();
        } catch (\Exception $e) {
            return '';
        }
    }
}
