#Laravel 4 VisitorLog

A package for Laravel 4 to log all visitors

If anyone has any ideas on how to make this framework agnostic, please contact me or open a pull request.

##Installation

Add `jones/visitor-log` as a requirement to `composer.json`:

```javascript
{
    ...
    "require": {
        ...
        "jones/visitor-log": "dev-master"
        ...
    },
}
```

Update composer:

```
$ php composer.phar update
```

Add the provider to your `app/config/app.php`:

```php
'providers' => array(

    ...
    'Jones\VisitorLog\VisitorLogServiceProvider',

),
```

Add the alias to your `app/config/app.php`:

```php
'aliases' => array(

    ...
	'Visitor'		  => 'Jones\VisitorLog\Visitor',

),
```

(Optional) Publish package config:

```
$ php artisan config:publish jones/visitor-log
```

##Configuration

 * `onlinetime`: The time (in minutes) while a visitor is still saved
 * `usermodel`: 'auth' => You're using the laravel auth class; 'sentry' => You're using Sentry