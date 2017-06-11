# Magento 2 Twilio Integration
The Magento 2 Twilio module allows store owners to send SMS messages,  
via the Twilio API, when certain customer events occur. Current  
supported SMS triggers are:  

* New Order
* New Shipment
* New Invoice  

Each event message can be enabled/disabled independently and uses message  
templates defined in the module configuration. 

## Installation
In your Magento 2 root directory run:  
`composer require pmclain/module-twilio`  
`bin/magento setup:upgrade`

## Configuration
Module settings are found in the Magento 2 admin panel under  
Stores->Configuration->Sales->Sales SMS

## Customer Address Templates
If the system HTML Address Template has been edited the SMS notification value
will not appear in the customer dashboard address book. Add the following to 
Stores->Configuration->Customer->Customer Configuration->Address Templates->HTML
where you wish this data to appear:  
`{{depend sms_alert}}<br/>SMS Enabled: {{var sms_alert}}{{/depend}}`

## License
GNU GENERAL PUBLIC LICENSE Version 3