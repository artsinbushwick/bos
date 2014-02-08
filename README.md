Bushwick Open Studios website
===

This is the source code repository for the WordPress site artsinbushwick.org. This document describes how to set up artsinbushwick.org to run on a local development environment at the hostname dev.artsinbushwick.org.

#### Requirements

These instructions assume you're using a Mac. I'm running OS X 10.8.5 running the Apple-provided Apache, and MySQL installed via [Homebrew](http://brew.sh/). Maybe [MAMP](http://www.mamp.info/) is the thing you prefer? Hopefully these instructions will still make sense. I'm going to skip over setting up the [WordPress prerequisites](http://wordpress.org/about/requirements/), and just assume that either you have your own way of doing things that you prefer, or that if you have questions you can [open a ticket](https://github.com/artsinbushwick/bos/issues/new) and I can field questions as they arise.

You will need to run some commands with `sudo` and will need a working knowledge of a text editor like `vim` or `nano`. Basically I'm going to assume you know what you're doing. But, again, I'm happy to expand the docs if you're left wondering how something works.

#### Apache VirtualHost

Start by adding the following VirtualHost entry in your Apache configuration, found in most cases in /etc/apache2. Obviously you'll want to replace the beginning of the `DocumentRoot` with whatever path you're hosting the site from. I prefer to put everything in my user's Sites folder, but it'll work just as well hosted from another path.

```conf
<VirtualHost *:80>
  DocumentRoot "/Users/username/Sites/artsinbushwick.org/public"
  ServerName dev.artsinbushwick.org
</VirtualHost>
```

Why the 'public' subfolder? This is just a good habit to get into, I think. I'll describe how I use symlinks  to make file permissions a little more straightforward.

#### Edit /etc/hosts

Add an entry to your /etc/hosts file so that your computer will know to load dev.artsinbushwick.org from our own local environment.

```hosts
127.0.0.1       dev.artsinbushwick.org
```

#### Test the server config

Make sure the path you specified for your DocumentRoot actually exists on your file system (i.e., in the Finder) and restart Apache. Then load up http://dev.artsinbushwick.org/ in your browser and see what you get.

* If you get a 404 error, then you may have a missing DocumentRoot folder, or maybe misconfigured it
* If you get a 403 error, then everything is working fine—Apache just wants there to be an index.html in the DocumentRoot
* If you see an empty directory listing, then everything is fine, and Apache doesn't care that you don't have an index.html
* If you get some other error, then something else is wrong

#### Set up MySQL

Create a new database for the site. I like to use [Sequel Pro](http://www.sequelpro.com/) for this kind of thing. What I do is connect to localhost with user root, then choose 'Add Database' from the 'Choose Database' drop-down, and then call it 'artsinbushwick'. You can also do it from the command line if you prefer not to use Sequel Pro: `mysqladmin -u root -p create artsinbushwick`

#### Install WordPress

Grab the latest version of WordPress from http://wordpress.org/latest.zip and unzip it into the DocumentRoot folder. Now you should be able to load up http://dev.artsinbushwick.org/ and see an error like 'Database connection error' or maybe something about wp-config.php. That's good, that means everything is working as expected. Note that you don't want to put the 'wordpress' folder *into* the DocumentRoot, but rather place the contents of the 'wordpress' folder into the DocumentRoot.

Rename wp-config-sample.php to wp-config.php and add the following configuration:

```php
/** The name of the database for WordPress */
define('DB_NAME', 'artsinbushwick');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');
```

Try loading up http://dev.artsinbushwick.org/ again and you should get the WordPress Installation screen.

#### Install WordPress

I'm going to assume you can plug the right stuff into here. Since this is a local dev environment, things like the site title and search engine setting don't have much bearing on anything. The username and password are the only really important thing, since you'll need that to login to the WordPress admin.

After you click the 'Install' button, login with your username and password and you should see the WordPress dashboard.

#### Clone `bos` repository

Rename the 'wp-content' folder to 'wp-content.orig' and clone the bos repository to create a new 'wp-content' directory.

```
cd ~/Sites/artsinbushwick.org/public
mv wp-content wp-content.orig
git clone git@github.com:artsinbushwick/bos.git wp-content
```

#### Avoiding file permissions problems

This is totally optional, but maybe helpful. File permissions can sometimes be tricky doing WordPress development because the Apache user wants to be able to modify things like .htaccess and the uploads folder, but the theme folder you want to edit yourself. So what I normally do is put the theme folder right under 'artsinbushwick.org', and then symlink it into wp-content.

```
cd ~/Sites/artsinbushwick.org
mv public/wp-content/themes/bos ./bos
cd public/wp-content/themes
ln -s ../../../bos ./bos
```

Then you can run `chown -R www:www public` and let Apache manage everything *except* your theme folder.

That's it, happy developing!
