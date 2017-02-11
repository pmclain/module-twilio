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

  return function (placeOrderAction) {
    return wrapper.wrap(placeOrderAction, function(originalAction) {
      var billingAddress = quote.billingAddress();
      if(billingAddress['extension_attributes'] === undefined) {
        billingAddress['extension_attributes'] = {};
      }

      billingAddress['extension_attributes']['sms_alert'] = billingAddress.customAttributes['sms_alert'] ? 1 : 0;

      return originalAction();
    });
  };
});