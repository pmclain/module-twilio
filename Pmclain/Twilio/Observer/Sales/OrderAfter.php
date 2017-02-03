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

namespace Pmclain\Twilio\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Pmclain\Twilio\Helper\Data as Helper;
use Pmclain\Twilio\Model\Adapter\Order as OrderAdapter;

class OrderAfter implements ObserverInterface
{
  /**
   * @var \Pmclain\Twilio\Helper\Data
   */
  protected $_helper;

  /**
   * @var \Pmclain\Twilio\Model\Adapter\Order
   */
  protected $_orderAdapter;

  public function __construct(
    Helper $helper,
    OrderAdapter $orderAdapter
  ) {
    $this->_helper = $helper;
    $this->_orderAdapter = $orderAdapter;
  }

  /**
   * @param \Magento\Framework\Event\Observer $observer
   * @var \Magento\Sales\Model\Order $order
   * @return \Magento\Framework\Event\Observer
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if(!$this->_helper->isTwilioEnabled()) { return $observer; }

    $order = $observer->getOrder();

    if(!$shippingAddress = $order->getShippingAddress()) { return $observer; }

    if($shippingAddress->getSmsAlert()) {
      $this->_orderAdapter->sendOrderSms($order);
    }

    return $observer;
  }
}