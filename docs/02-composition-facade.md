##Email Composition Facade - Compose emails easily

The composition facade is proxied by the the `bzlSend` controller plugin.

```php
<?php
    
class SomeController
{
    public function someAction()
    {
      
        /* ... */
      
        $content = new ViewModel();
        $content->setTemplate('emails/registration.phtml')
                ->setVariables(array(
                  'user' => $user,
                ));
    
        $this->bzlSend(array(
            'subject' => 'Confirm registration',
            /* 'content' can be a plain PHP string and will be delivered as non-HTML. */
            'content' => $content,
            'attachments' => array(
                /* Specifying a file in the filesystem: */
                array(
                    'file' => 'data/resources/Brandingv2.jpg', /* Resolves files relative to the application's chroot. */
                    'name' => 'logo.jpg', /* Optional. Defaults to the actual filename of attachment. */
                    'mime_type' => 'image/jpeg' /* Required only if fileinfo extension is not installed/enabled. */
                ),
                /* Specifying actual content of attachment: */
                array(
                    'content' => 'RAW FILE CONTENT HERE', 
                    'encoding' => Mime::ENCODING_BASE64, /* Optional. Defaults to Mime::ENCODING_8BIT. */
                    'name' => 'TermsAndConditions.pdf', /* Required. */
                    'mime_type' => 'application/pdf' /* Required. */
                ),
                /* Plain strings will be interpreted as a file in the file system. This will only work if fileinfo extension is installed/enabled. */
                'data/resources/Schedule.ical',
            )
        ));
    }
}
```
