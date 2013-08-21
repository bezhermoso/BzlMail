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
