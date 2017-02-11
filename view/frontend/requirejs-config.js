var config = {
  config: {
    mixins: {
      'Magento_Checkout/js/action/set-shipping-information': {
        'Pmclain_Twilio/js/action/set-shipping-information-mixin': true
      },
      'Magento_Checkout/js/action/create-billing-address': {
        'Pmclain_Twilio/js/action/create-billing-address-mixin': true
      },
      'Magento_Checkout/js/action/place-order': {
        'Pmclain_Twilio/js/action/place-order-mixin': true
      }
    }
  }
};