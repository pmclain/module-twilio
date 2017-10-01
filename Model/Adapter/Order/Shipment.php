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

use Magento\Store\Model\StoreManagerInterface;
use Pmclain\Twilio\Helper\Data as Helper;
use Pmclain\Twilio\Helper\MessageTemplateParser;
use Pmclain\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order\Shipment as SalesShipment;
use Magento\Shipping\Model\CarrierFactory;
use Psr\Log\LoggerInterface;
use Twilio\Rest\ClientFactory as TwilioClientFactory;

class Shipment extends AdapterAbstract
{
    /**
     * @var int
     */
    protected $entityTypeId = 3;

    /** @var CarrierFactory */
    protected $carrierFactory;

    public function __construct(
        Helper $helper,
        TwilioClientFactory $twilioClientFactory,
        LoggerInterface $logger,
        MessageTemplateParser $messageTemplateParser,
        StoreManagerInterface $storeManager,
        \Pmclain\Twilio\Model\LogRepository $logRepository,
        \Pmclain\Twilio\Model\LogFactory $logFactory,
        CarrierFactory $carrierFactory
    ) {
        parent::__construct(
            $helper,
            $twilioClientFactory,
            $logger,
            $messageTemplateParser,
            $storeManager,
            $logRepository,
            $logFactory
        );
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Pmclain\Twilio\Model\Adapter\Order\Shipment
     */
    public function sendOrderSms(SalesShipment $shipment)
    {
        if (!$this->_helper->isShipmentMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawShipmentMessage(),
            $this->getShipmentVariables($shipment)
        );

        $order = $shipment->getOrder();

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = '+1' . $order->getShippingAddress()->getTelephone();

        $this->entityId = $shipment->getId();
        $this->_sendSms();

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    protected function getShipmentVariables($shipment)
    {
        $vars = [];

        $vars['shipment.qty'] = $shipment->getTotalQty();
        $vars['shipment.trackingnumber'] = $this->getTrackingNumbersArray($shipment->getTracks());
        $vars['shipment.trackinglink'] = $this->getTrackingLinks($shipment->getTracks());
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
    protected function getTrackingNumbersArray($items)
    {
        $trackingNumbers = [];
        foreach ($items as $item) {
            $trackingNumbers[] = $item->getNumber();
        }

        return $trackingNumbers;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Track[] $tracks
     * @return array
     */
    protected function getTrackingLinks($tracks)
    {
        $links = [];
        foreach ($tracks as $track) {
            if ($url = $this->getTrackUrl($track)) {
                $links[] = $url;
            }
        }

        return $links;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Track $track
     * @return string|false
     */
    private function getTrackUrl($track)
    {
        $carrierInstance = $this->carrierFactory->create($track->getCarrierCode());
        if (!$carrierInstance) {
            return false;
        }
        $carrierInstance->setStore($track->getStore());

        $trackingInfo = $carrierInstance->getTrackingInfo($track->getNumber());
        if (!$trackingInfo || !$trackingInfo->getUrl()) {
            return false;
        }

        return $trackingInfo->getUrl();
    }
}
