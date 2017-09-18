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
use Magento\Sales\Model\Order\Invoice as SalesInvoice;

class Invoice extends AdapterAbstract
{
    const ENTITY_TYPE_ID = 2;

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Pmclain\Twilio\Model\Adapter\Order\Invoice
     */
    public function sendOrderSms(SalesInvoice $invoice)
    {
        if (!$this->_helper->isInvoiceMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawInvoiceMessage(),
            $this->getInvoiceVariables($invoice)
        );

        $order = $invoice->getOrder();

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = '+1' . $order->getBillingAddress()->getTelephone();

        try {
            $result = $this->_sendSms();
            $this->_smsStatus = $result->status;
            $this->_hasError = false;
        } catch (\Exception $e) {
            $this->_logger->addCritical($e->getMessage());
            $this->_smsStatus = $e->getMessage();
            $this->_hasError = true;
        }

        $this->_logResult($order->getId(), self::ENTITY_TYPE_ID);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return array
     */
    protected function getInvoiceVariables($invoice)
    {
        $vars = [];

        $vars['invoice.qty'] = $invoice->getTotalQty();
        $vars['invoice.grandtotal'] = $invoice->getGrandTotal(); //TODO: not properly formatted
        $vars['invoice.increment_id'] = $invoice->getIncrementId();
        $vars['order.increment_id'] = $invoice->getOrder()->getIncrementId();
        $vars['order.qty'] = $invoice->getOrder()->getTotalQtyOrdered();
        $vars['billing.firstname'] = $invoice->getOrder()->getBillingAddress()->getFirstname();
        $vars['billing.lastname'] = $invoice->getOrder()->getBillingAddress()->getLastname();
        $vars['storename'] = $this->_storeManager->getWebsite(
            $this->_storeManager->getStore($invoice->getOrder()->getStoreId())->getWebsiteId()
        )->getName();

        return $vars;
    }
}
