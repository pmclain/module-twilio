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

namespace Pmclain\Twilio\Model\Adapter;

use Pmclain\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order as SalesOrder;

class Order extends AdapterAbstract
{
    /**
     * @var int
     */
    protected $entityTypeId = 1;

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Pmclain\Twilio\Model\Adapter\Order
     */
    public function sendOrderSms(SalesOrder $order)
    {
        if (!$this->_helper->isOrderMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawOrderMessage(),
            $this->getOrderVariables($order)
        );

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = '+1' . $order->getBillingAddress()->getTelephone();

        $this->entityId = $order->getId();
        $this->_sendSms();

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function getOrderVariables($order)
    {
        $vars = [];

        $vars['order.increment_id'] = $order->getIncrementId();
        $vars['order.qty'] = $order->getTotalQtyOrdered();
        $vars['billing.firstname'] = $order->getBillingAddress()->getFirstname();
        $vars['billing.lastname'] = $order->getBillingAddress()->getLastname();
        $vars['order.grandtotal'] = $order->getGrandTotal(); //TODO: not properly formatted
        $vars['storename'] = $this->_storeManager->getWebsite(
            $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
        )->getName();

        return $vars;
    }
}
