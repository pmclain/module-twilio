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
  'mage/utils/wrapper'
], function($, wrapper) {
  'use strict';

  return function (createShippingAddressAction) {
    return wrapper.wrap(createShippingAddressAction, function(originalAction, addressData) {
      if (addressData.custom_attributes === undefined) {
        return originalAction();
      }

      if (addressData.custom_attributes['sms_alert']) {
        addressData.custom_attributes['sms_alert'] = {
          'attribute_code': 'sms_alert',
          'value': 'SMS Enabled',
          'status': 1
        }
      }

      return originalAction();
    });
  };
});