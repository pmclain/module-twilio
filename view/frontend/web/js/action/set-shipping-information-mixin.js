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

      try {
        var smsStatus = shippingAddress.customAttributes['sms_alert'].status;
        var smsValue = shippingAddress.customAttributes['sms_alert'].value;

        //TODO: this smells. the check for status stems from an attempt to
        // non-invasively display a user friendly custom attribute value in
        // the customer address-mixin.
        if(smsStatus == true) {
          shippingAddress['extension_attributes']['sms_alert'] = smsStatus;
        }else if(smsValue == true) {
          shippingAddress['extension_attributes']['sms_alert'] = smsValue
        }else {
          shippingAddress['extension_attributes']['sms_alert'] = false;
        }
      }catch (e) {
        return originalAction();
      }

      return originalAction();
    });
  };
});