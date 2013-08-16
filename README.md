BzlMail
========

Email set-up and sending utlities module for Zend Framework 2

* Mail transport selection and configuration via forms **[WORKING]**
* Facade for composing and transmitting emails (easier HTML email composition, adding attachments, etc) [PENDING]
* Email scheduling & queueing [PENDING]

##Installation

Install via Composer 
```sh
composer require bez\bzl-mail:dev-master
```

Create the directory, `data/BzlMail`, and make sure it is writeable. A JSON file will be saved in this location containing the configuration data. This storage mechanism can be overridden by implementing `BzlMail\Settings\Storage\Adapter\AdapterInterface` (more info will follow)

##Mail Transport Selection & Configuration

Choosing and configuring the preferred transport to use in your application is easy -- once installed, simply head to http://yourappurl.com/email/settings

Available transports:

+ Sendmail
+ SMTP
+ Gmail (pre-configured SMTP for convenience)

Upcoming transports:

+ Mandrill SMTP
+ SendGrid SMTP
+ Mandrill API
+ Sendgrid API

###Retrieving the Mail Transport Object

You can retrieve the configured transport from the service locator, like so, `$serviceLocator->get('bzlmail.transport')`. This will return a transport object that is configured according to the preferred configurations.

Alternatively, you can use the `bzlTransport` controller plugin.

```php
<?php
    
class SomeController
{
    public function someAction()
    {
        $message = new \Zend\Mail\Message();
  
        /* ... */
  
        $this->bzlTransport()->send($message);
    }
}
```
