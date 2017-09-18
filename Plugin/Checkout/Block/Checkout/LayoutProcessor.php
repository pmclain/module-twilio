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

namespace Pmclain\Twilio\Plugin\Checkout\Block\Checkout;

use Pmclain\Twilio\Helper\Data as Helper;

class LayoutProcessor
{
    /**
     * @var \Pmclain\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * LayoutProcessor constructor.
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->_helper->isTwilioEnabled()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['sms_alert'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox',
                'custom_entry' => null,
            ],
            'dataScope' => 'shippingAddress.custom_attributes.sms_alert',
            'label' => __('SMS Order Notifications'),
            'description' => __('Send SMS order notifications to the phone number above.'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'checked' => true,
            'validation' => [],
            'sortOrder' => 125,
            'custom_entry' => null,
        ];

        return $jsLayout;
    }
}
