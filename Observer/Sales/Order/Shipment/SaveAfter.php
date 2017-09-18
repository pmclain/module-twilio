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

namespace Pmclain\Twilio\Observer\Sales\Order\Shipment;

use Magento\Framework\Event\ObserverInterface;
use Pmclain\Twilio\Helper\Data as Helper;
use Pmclain\Twilio\Model\Adapter\Order\Shipment as ShipmentAdapter;

class SaveAfter implements ObserverInterface
{
    /**
     * @var \Pmclain\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Pmclain\Twilio\Model\Adapter\Order\Shipment
     */
    protected $_shipmentAdapter;

    public function __construct(
        Helper $helper,
        ShipmentAdapter $shipmentAdapter
    ) {
        $this->_helper = $helper;
        $this->_shipmentAdapter = $shipmentAdapter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @var \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isTwilioEnabled()) {
            return $observer;
        }

        $shipment = $observer->getShipment();
        $order = $shipment->getOrder();

        if (!$shippingAddress = $order->getShippingAddress()) {
            return $observer;
        }

        if ($shippingAddress->getSmsAlert()) {
            $this->_shipmentAdapter->sendOrderSms($shipment);
        }

        return $observer;
    }
}
