# Backstage

Backstage is a user portal web-application offering social-network-like features geared towards a small to medium group of friends.

## Features
* Live group chat with notification system and media integration
* Drag-and-drop image uploading, with optional cropping tool
* Image sharing and album creation
* Meme generator: image-captioning tool
* Link sharing and cataloging system
* Activity log to track user content and interactions
* Commenting and tagging system for most content

## Browser support

Backstage was created using some modern browser capabilities, so it may run on older browsers, but the best experience will be with one of the following:

* Google Chrome (latest)
* Mozilla Firefox (latest)
* Internet Explorer 10+
* Safari 6+

Modern mobile web browsers are also supported.

## Server requirements

You should already know how to configure and maintain a basic PHP-based website

* Apache webserver v2.0+ (2.2+ is recommended) with mod_rewrite enabled
* PHP v5.3+ (latest 5.4.x is supported & recommended)
* SQL database with read & write-access
* GD image module

Database support is based on what [CakePHP supports][CakePHPDataSources], so you can use: MySQL, Postgres, SQLite or SQLServer

## Installation

Backstage is a CakePHP application, so it depends on the presence of the PHP framework. Additional notes on how to configure CakePHP installations can be found in the [CakePHP book][CakePHPBookInstallation]

### Directory Layout

For production-ready sites, I use the following directory layout:
```text
	CakePHP2.4/
		lib/
		plugins/
		vendors/
	Backstage/
		Config/
		Console/
		...
		View/
		webroot/ <-- VirtualHost DocumentRoot
			css/
			js/
			img/
			.htaccess
			index.php
```
This structure has a number of advantages:

1. CakePHP is decoupled from the application, allowing easy point-release updates (2.4.x) to be deployed
2. Only static assets (css, js, images) are accessible via the public webroot
3. This layout equally works in development environments

### Configuring a new site

1. Download and extract the latest CakePHP 2.4.x
2. Download and extract the latest Backstage application package.
3. Ensure that the following application directories and subdirectories are writeable by Apache
	* webroot/img/user/
	* tmp/
4. Execute the SQL queries in `Config/Schema/schema.sql` in an empty database
5. Create an empty file `Config/bootstrap.env.php`, this will hold all your app configurations. Add the following settings:

	Configure::write("debug", 0); // disable debug mode
	Configure::write("Cache.check", true); // enable view caching

	// app security salts, keys
	Configure::write("Security.salt", "REPLACE-WITH-LONG-UNIQUE-RANDOM-STRING");
	Configure::write("Security.cipherSeed", "REPLACE-WITH-RANDOM-DIGIT-SERIES"); // digits only
	Configure::write("Cookie.key", "REPLACE-WITH-LONG-UNIQUE-RANDOM-STRING");

6. Replace the strings aboved marked as "REPLACE" with unique [string][RandomStrings] and [digit][RandomDigits] sequences to secure your installation.
7. Append your database credentials to the `bootstrap.env.php` file, example:

	class DATABASE_CONFIG {
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

8. Temporarily add this line to the `boostrap.env.php`.

	Configure::write("setup", true);

9. Visit the `/setup` URL for this site in your browser to configure the first administrator user (example.com/setup).
10. Remove the line added in #8 once your administator account is set up.

If everything is set up correctly, _you're done_!

If you encounter errors, verify your Apache logs and the application's internal logs via `tmp/log/debug.log` and `tmp/log/error.log`

### Developer-mode set up

If you're looking to touch some code relating to Backstage, or just want to have a look around, here's the steps to organize your dev environment.
```sh
	# DIR=/path/to/your/webroot/

	git clone https://github.com/cakephp/cakephp.git CakePHP2.4
	cd CakePHP2.4
	git checkout 2.4.7   # or latest 2.4.x
	cd ..
	git clone [Backstage Repo URL] backstage
	cd backstage
	git submodule init
	git submodule update
	chmod 777 -R tmp/ webroot/img/user
```
Now continue the installation steps above, starting with step #4 if you haven't done this already.

Once up and running, you can update your local dev copies quickly
```sh
	# DIR=/path/to/your/webroot/backstage/

	git remote update && git rebase origin/master
	git submodule init
	git submodule update
```
The `git submodule` commands don't need to be ran constantly, you can skip these when updating frequently.

## App Configuration

Backstage supports a limited number of user-configurable options. To override a default configuration, you can modify your `Config/bootstrap.env.php` with the options provided below.

#### General Settings

* **Site name** : Change the name "Backstage" to anything you want, but best to keep it short.
```php
	Configure::write("Site.name", 'My Hangout');
```
* **Remember-me Cookie** : Duration of the user remember-me cookie
```php
	Configure::write("Site.rememberMeExpiry", '+3 months');
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
