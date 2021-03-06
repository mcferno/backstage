# Backstage

Backstage is a user portal web-application offering social-network-like features geared towards a small to medium group of friends.

![Dashboard Screenshot](https://mcferno.com/content/media/backstage-social-network.jpg "Dashboard Screenshot")

## Features
* Live group chat with notification system and media integration
  * YouTube & Vimeo video player embedding
  * Image embedding (post via web hotlink, or from saved images)
* Drag-and-drop image uploading, with optional cropping tool
  * Upload an image from a URL
* Image sharing and album creation
* Meme generator: seamless image-captioning tool
  * Caption contest for a chosen base image
* Activity log to track user content and interactions
  * Notifies all users of new content, comments, or unread chat messages
  * List users who most recently logged in
  * Active or idle status for online chat users
* Link sharing and cataloging system
* Commenting and tagging system for most content

## Browser support

Backstage was created to leverage modern browser capabilities, so it may run on older browsers, but the best experience will be with one of the following:

* Google Chrome (latest)
* Mozilla Firefox (latest)
* Internet Explorer 10+
* Safari 6+

Modern mobile web browsers are also supported.

## Server requirements

You should already know how to configure and maintain a basic PHP-based website

* Apache webserver v2.0+ (2.2+ is recommended) with mod_rewrite enabled
* PHP v5.6+ (latest 7.x is supported & recommended)
* SQL database with read & write-access
* GD image module
* Shell access
* [Composer][ComposerInstallation]

Database support is based on what [CakePHP supports][CakePHPDataSources], so you can use: MySQL, Postgres, SQLite or SQLServer

## Installation

Backstage is a CakePHP application, so it depends on the presence of the PHP framework. Additional notes on how to configure CakePHP installations can be found in the [CakePHP book][CakePHPBookInstallation]

### Directory Layout

For development and production-ready sites, I use the following directory layout:
```text
Backstage/   <-- The root of this Git repository
	Config/
	Console/
	...
	View/
	webroot/ <-- DocumentRoot for the website
		css/
		js/
		img/
		.htaccess
		index.php
```
This structure has a number of advantages:

1. Only static assets (css, js, images) are accessible via the public webroot
2. This layout equally works in development environments

### Configuring a new site

1. Ensure that all the server software requirements are installed (above).
2. Download and extract the latest Backstage application package.
3. Ensure that the following application directories and subdirectories are writable by Apache
	* `webroot/img/user/`
	* `tmp/`
4. Create an empty file `Config/bootstrap.env.php`, this will hold all your app configurations. Add the following settings:
```php
date_default_timezone_set('America/New_York'); // choose the most appropriate value for your installation

Configure::write("debug", 0); // disable debug mode
Configure::write("Cache.check", true); // enable view caching

// app security salts, keys
Configure::write("Security.salt", "REPLACE-WITH-LONG-UNIQUE-RANDOM-STRING");
Configure::write("Security.cipherSeed", "REPLACE-WITH-RANDOM-DIGIT-SERIES"); // digits only
Configure::write("Cookie.key", "REPLACE-WITH-LONG-UNIQUE-RANDOM-STRING");
```

5. Replace the strings above marked as "REPLACE" with unique [string][RandomStrings] and [digit][RandomDigits] sequences to secure your installation. If your server PHP timezone is not already set, choose the most appropriate [timezone][PHPTimezones] value.
6. Append your database credentials to the `bootstrap.env.php` file, example:
```php
class DATABASE_CONFIG
{
	public $default = array(
		"datasource" => "Database/Mysql",
		"persistent" => false,
		"host" => "localhost",
		"login" => "USERNAME",
		"password" => "PASSWORD",
		"database" => "DATABASE_NAME",
		"encoding" => "utf8"
	);
}
```
7. Run `composer install --no-dev` from the root directory of the Backstage project to install application dependencies.
8. Run `./Vendor/bin/cake Migrations.migration run up` to install the database and create your first administrator account.

If everything is set up correctly, _you're done_!

If you encounter errors, verify your Apache logs and the application's internal logs via `tmp/log/debug.log` and `tmp/log/error.log`

## App Configuration

Backstage supports a limited number of user-configurable options. To override a default configuration, you can modify your `Config/bootstrap.env.php` with the options provided below.

#### General Settings

* **Site name** : Change the name "Backstage" to anything you want, but best to keep it short.
```php
Configure::write("Site.name", 'My Hangout');
```
* **Remember-me Cookie** (optional, default: enabled): Allow an auto-login of returning users
```php
Configure::write("Site.Tracking.RememberMe.enabled", true);
Configure::write("Site.Tracking.RememberMe.expiry", '+3 months'); // validity period since the last login
```
* **Google Analytics User Tracking** (optional, default: disabled) : Gather visitor traffic stats via Google Analytics
```php
Configure::write("Site.Tracking.GoogleAnalytics.enabled", true);
Configure::write("Site.Tracking.GoogleAnalytics.portalAccountID", "UA-XXXXXXXXX-1");
```

#### Chat Settings

* **Chat Log Expiry** : How long until chat message expire in the group chat (time in seconds)
```php
Configure::write("Site.Chat.messageExpiry", 14400);
```
* **Chat Log Limit** : How many messages will appear when viewing the chat log
```php
Configure::write("Site.Chat.maxHistoryCount", 50);
```

#### Image Settings

* **Images per Page** : How many images are shown per page for desktop users
```php
Configure::write("Site.Images.perPage", 60);
```
* **Images per Page on Mobile**
```php
Configure::write("Site.Images.perPageMobile", 30);
```
* **Recent Albums** : How many of the most recent albums are listed
```php
Configure::write("Site.Images.recentAlbums", 2);
```
* **Album Preview** : How many album images to show in the preview card
```php
Configure::write("Site.Images.albumPreviews", 4);
```
* **Image Minimum Dimensions** : Minimum images size of user added images in pixel, not including cropped images
```php
Configure::write("Site.Images.minDimension", 640);
```
* **Image Maximum Dimensions** : Maximum pixel width or height when images are downscaled
```php
Configure::write("Site.Images.maxDimension", 1200);
```

## Motivation and future of the project

I built this web-app as a hobby project. I designed the bulk of the features around specific needs to share content with a group of friends. I eventually had enough features that I felt like others might be interested in hosting their own versions of Backstage.

I have no concrete plans to continue developing Backstage, which is why I've open-sourced it. Anyone interested is welcome to remix it, add to it, and make it your own. I preferred this approach over sitting on this much code, and having so few able to use it.

The current set of features are generally stable so you shouldn't have troubles running your own sites.

[CakePHPBookInstallation]: http://book.cakephp.org/2.0/en/installation.html
[CakePHPDataSources]: http://book.cakephp.org/2.0/en/models/datasources.html
[RandomStrings]: https://api.wordpress.org/secret-key/1.1/salt/
[RandomDigits]: https://www.random.org/strings/?num=20&len=20&digits=on&unique=on&format=plain
[ComposerInstallation]: https://getcomposer.org/doc/00-intro.md
[PHPTimezones]: https://www.php.net/manual/en/timezones.php