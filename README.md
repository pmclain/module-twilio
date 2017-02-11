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

## License
GNU GENERAL PUBLIC LICENSE Version 3