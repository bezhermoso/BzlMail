BzlMail
========

Email set-up and sending utlities module for Zend Framework 2

* Transport settings via forms [WORKING]
* Email sending facade (easier HTML email composition, adding attachments, etc) [PENDING]
* Email queueing [PENDING]

###Mail Transport Selection & Configuration

Choosing and configuring the preferred transport to use in your application is easy -- once installed, simply head to http://yourappurl.com/email/settings

Available transports:

+ Sendmail
+ SMTP

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
