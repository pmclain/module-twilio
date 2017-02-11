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

  return function (createBillingAddressAction) {
    return wrapper.wrap(createBillingAddressAction, function(originalAction, addressData) {
      var result = originalAction();
      if(result['customAttributes'] === undefined) {
        result['customAttributes'] = {};
      }
      result.customAttributes['sms_alert'] = addressData.sms_alert;

      return result;
    });
  };
});