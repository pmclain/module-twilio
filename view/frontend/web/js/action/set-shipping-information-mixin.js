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

define([
  'jquery',
  'mage/utils/wrapper',
  'Magento_Checkout/js/model/quote'
], function($, wrapper, quote) {
  'use strict';

  return function (setShippingInformationAction) {
    return wrapper.wrap(setShippingInformationAction, function(originalAction) {
      var shippingAddress = quote.shippingAddress();

      if(shippingAddress.customAttributes === undefined) {
        shippingAddress.customAttributes = {};
      }

      if(shippingAddress['extension_attributes'] === undefined) {
        shippingAddress['extension_attributes'] = {};
      }

      shippingAddress['extension_attributes']['sms_alert'] = shippingAddress.customAttributes['sms_alert'] ? 1 : 0;

      return originalAction();
    });
  };
});