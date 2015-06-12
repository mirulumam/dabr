# Installation #

## Step 1: Get the code ##

Instructions are available on the [Source checkout page](http://code.google.com/p/dabr/source/checkout) for how to download a copy of the code. There are also zipped up copies of the source on the [Downloads tab](http://code.google.com/p/dabr/downloads/list) - please be aware those might be out of date though.

## Step 2: Get a web server ##

  * Dabr has been designed to work on Apache, with PHP 5.2+ (because it uses the `json_decode()` function).
  * A great package for running a test server on a Windows box is [XAMPP](http://www.apachefriends.org/en/xampp-windows.html) (or XAMPP Lite)
  * You need to copy all the files onto the server for it to work.
  * No database is needed.

### Server Requirements ###

  * PHP 5.2+
  * curl PHP module
  * mcrypt PHP module
  * mod\_rewrite apache module
  * Your server must be able to access Twitter.com

## Step 3: Create a Twitter Application ##

  1. Visit https://dev.twitter.com/apps and register your Application
  1. Ensure you have the "Read, write, and direct messages" rights
  1. Make a note of your access token

## Step 4: Configure dabr ##

  1. Rename `config.sample.php` to `config.php`
  1. Change `ENCRYPTION_KEY` in `config.php` to a random string of gibberish, maximum 52 characters.
  1. With your access tokens obtained in Step 3 set `OAUTH_CONSUMER_KEY` = Access token and `OAUTH_CONSUMER_SECRET` = Access token secret
  1. `BASE_URL` is worked out automatically, but can be hard coded if you prefer.
  1. `FLICKR_API_KEY` is optional. If filled in, it enables Flickr thumbnails.
  1. If you want to secure your installation, consider making it [invite only](http://code.google.com/p/dabr/source/browse/branches/davidcarrington/config_invite_only.php)

## Step 5: Try it! ##

Dabr should now be up and running. There's no other configuration to do.