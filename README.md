BzlMail
========

Email set-up and sending utlities module for Zend Framework 2

* Transport settings via forms [DEVELOPMENT ONGOING]
* Email sending facade (easier HTML email composition, adding attachments, etc) [PENDING]
* Email queueing [PENDING]

###Mail Transport Selection & Configuration

Choosing and configuring the preferred transport to use in your application is easy -- once installed, simply head to http://yourappurl.com/email/settings

Available transports:

+ Sendmail
+ SMTP

###Retrieving the mail transport object

You can retrieve a pre-configured transport from the service locator, like so, `$serviceLocator->get('bzlmail.transport')`.

Alternatively, you can use the `bzlTransport` controller plugin.

    class SomeController
    {
        public function someAction()
        {
            $message = new \Zend\Mail\Message();
      
            ...
      
            $this->bzlTransport()->send($message);
        }
    }
