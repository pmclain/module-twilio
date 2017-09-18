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

namespace Pmclain\Twilio\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();

        if ($shippingAddress->getExtensionAttributes()) {
            $shippingAddress->setSmsAlert((int)$shippingAddress->getExtensionAttributes()->getSmsAlert());
        } else {
            $shippingAddress->setSmsAlert(0);
        }

        if ($billingAddress->getExtensionAttributes()) {
            $billingAddress->setSmsAlert((int)$billingAddress->getExtensionAttributes()->getSmsAlert());
        } else {
            $billingAddress->setSmsAlert(0);
        }
    }
}
