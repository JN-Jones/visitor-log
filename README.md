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

Publish migration:

```
$ php artisan migrate --package=jones/visitor-log
```

(Optional) Publish package config:

```
$ php artisan config:publish jones/visitor-log
```

##Configuration

 * `onlinetime`: The time (in minutes) while a visitor is still saved
 * `usermodel`: Set this to the Auth Provider you're using:
 	* `Laravel`: Visitor-Log will try to get the User with Laravel's Auth Class
 	* `Sentry`: Visitor-Log will try to get the User with Sentry
 * `ignore`: This is an array of pages that will be ignored by Visitor-Log. Example "admin/online"

##The Visitor Class
The Visitor Class is an Eloquent Class but has a few extra methods, which will be explained here.
 * `static isOnline($id)`: Will check whether the user with `$id` is online
 * `static getCurrent()`: Will get the current Visitor (based on their sid)
 * `static clear()`: **Buggy!!** This removes all old visitors from the Database. You normally don't need to call this as this happens on every request.
 * `static loggedIn()`: Will get all logged in Users
 * `static guests()`: Will get all guests
 * `static findUser($id)`: Will get the User provided by `$id` (if online)
 * `static findIp($ip)`: Will get all visitors with `$ip`
 * `isUser()`: Checks whether this visitor is a User
 * `isGuest()`: Checks whether this visitor is a Guest

There are a few methods that wraps the specific Useragent method:
 * `is_browser()`: Checks whether this visitor uses a browser
 * `is_robot()`: Checks whether this visitor is a bot
 * `is_mobile()`: Checks whether this visitor uses a mobile client
 * `is_referral()`: Checks whether this visitor is a referral

The Visitor Class also provides some attributes:
 * `sid`: A random String which is used to identicate the visitor
 * `ip`: The IP of the visitor
 * `page`: The Page where the visitor is
 * `useragent`: The useragent of the visitor
 * `user`: The UserID of the visitor
 * The timestamps (`created_at`, `updated_at`)
 * `agent`: Returns the useragent Instance used for this visitor
 * `agents`: Returns the modified Agentstring (eg Opera 12.16)
 * `platform`: Returns the operating System
 * `browser`: Returns the Browser which is used
 * `version`: Returns the Browser version which is used
 * `robot`: Returns the Botname which is browsing your site
 * `mobile`: Returns the mobile which is used
 * `referrer`: Returns the referrer
 
##What's with an "Who's online page"?
It's really easy to add one by yourself but Visitor-Log provides you already a very simple one: simply add `@include("visitor-log::online")` to your blade template where you want. And the best: It's ready to look nice with bootstrap