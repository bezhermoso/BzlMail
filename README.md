BzlMail
========

Email set-up and sending utlities module for Zend Framework 2

* Mail transport selection and configuration via forms **[WORKING]**
* Facade for composing and transmitting emails (easier HTML email composition, adding attachments, etc) [DEVELOPMENT ONGOING]
* Email scheduling & queueing [PENDING]

##Installation

Install via Composer 
```sh
composer require bez/bzl-mail:dev-master
```
Add `"BzlMail"` to the list of modules to load in `config/application.config.php`

Create the directory, `data/BzlMail`, and make sure it is writeable. A JSON file will be saved in this location containing the configuration data. This storage mechanism can be overridden by implementing `BzlMail\Settings\Storage\Adapter\AdapterInterface` (more info will follow)

##Documentations

* [Mail Transport Options & Configuration](https://github.com/bezhermoso/BzlMail/blob/master/docs/01-transport-options.md)
