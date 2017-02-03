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

namespace Pmclain\Twilio\Model\Adapter\Order;

use Pmclain\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order\Shipment as SalesShipment;

class Shipment extends AdapterAbstract
{
  /**
   * @param \Magento\Sales\Model\Order\Shipment $shipment
   * @return \Pmclain\Twilio\Model\Adapter\Order\Shipment
   */
  public function sendOrderSms(SalesShipment $shipment) {
    if(!$this->_helper->isShipmentMessageEnabled()) { return $this; }

    $this->_message = $this->_messageTemplateParser->parseTemplate(
      $this->_helper->getRawShipmentMessage(),
      $this->getShipmentVariables($shipment)
    );

    $order = $shipment->getOrder();

    //TODO: something needs to verify the phone number
    //      and add country code
    $this->_recipientPhone = '+1' . $order->getShippingAddress()->getTelephone();

    try {
      $this->_smsStatus = $this->_sendSms();
    }catch (\Exception $e) {
      $this->_logger->addCritical($e->getMessage());
    }

    return $this;
  }

  /**
   * @param \Magento\Sales\Model\Order\Shipment $shipment
   * @return array
   */
  protected function getShipmentVariables($shipment) {
    $vars = [];

    $vars['shipment.qty'] = $shipment->getTotalQty();
    $vars['shipment.trackingnumber'] = $this->getTrackingNumbersArray($shipment->getTracks());
    $vars['shipment.increment_id'] = $shipment->getIncrementId();
    $vars['order.increment_id'] = $shipment->getOrder()->getIncrementId();
    $vars['order.qty'] = $shipment->getOrder()->getTotalQtyOrdered();
    $vars['shipment.firstname'] = $shipment->getShippingAddress()->getLastname();
    $vars['shipment.lastname'] = $shipment->getShippingAddress()->getLastname();
    $vars['storename'] = $this->_storeManager->getWebsite(
        $this->_storeManager->getStore($shipment->getOrder()->getStoreId())->getWebsiteId()
      )->getName();

    return $vars;
  }

  /**
   * @param \Magento\Sales\Model\Order\Shipment\Track $items
   * @return array
   */
  protected function getTrackingNumbersArray($items) {
    $trackingNumbers = [];
    foreach ($items as $item) {
      $trackingNumbers[] = $item->getNumber();
    }

    return $trackingNumbers;
  }
}