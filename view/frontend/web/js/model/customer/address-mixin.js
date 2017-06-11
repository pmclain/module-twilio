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
  'mage/translate'
], function($, wrapper) {
  'use strict';

  return function (addressModel) {
    return wrapper.wrap(addressModel, function(originalAction) {
      var address = originalAction();

      if(address.customAttributes !== undefined) {
        if(address.customAttributes['sms_alert']) {
          var enabled = address.customAttributes['sms_alert'].value;
          address.customAttributes['sms_alert'].value = $.mage.__(enabled ? 'SMS Enabled' : 'SMS Disabled');
          address.customAttributes['sms_alert'].status = enabled ? 1 : 0;
        }
      }

      return address;
    });
  };
});