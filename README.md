## Checkers for Laravel

The Checkers game web service based on Laravel PHP web framework.

### Requirements

If you want install this project you should following packages and software:

* PHP 7.x
* PHP Composer 1.7.x
* NPM (NodeJs Package Manager) v8
* the SQL database (MySQL, PostgreSQL or any other compatible with Laravel)

Optionally, you can use:

* Apache HTTPD 2.4 server

Recommended web browser to run this web application is:

* Mozilla Firefox (modern, like 5x, 6x)
* Google Chrome
* Internet Explorer 11 or Edge

### Installation

After downloading repository or unpacking package you should run:

```
composer install    # install Laravel framework in this application
npm ci              # install javascript libraries and Laravel webmix

```

After that you should copy .env.example to .env and run:

```
php artisan key:generate    # generate Laravel keys for application
```

Create a database and its user for application like.
First, you should set correct URL where is this service works (URL) (in `.env` file):
```
APP_URL=http://localhost
```

In `.env` file the option `APP_DEBUG` controls whether application will be in debug mode (set to true) or not (set to false).
The `ADMIN_EMAIL` in `.env` file is administrator email.


Set up database in `.env` file (folowing scratch of this file):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

The `DB_CONNECTION` is type of database (mysql, pgsql),
the `DB_HOST` is host where is database,
the `DB_PORT` is port which will be used to connect to database,
the `DB_DATABASE` is name of database (might be `checkers`),
the `DB_USERNAME` is name of database user which can use this database,
the `DB_PASSWORD` is password, can be empty but it is recommended to fulfill.

Now, you should set up access to mailbox (in `.env` file):

```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

The `MAIL_DRIVER` is mail driver (can be `smtp`),
the `MAIL_HOST` is host where is mail server,
the `MAIL_PORT` is port which will be used to connect to mail server,
the `MAIL_USERNAME` is your mail box account to this server
the `MAIL_PASSWORD` is you mail account password,
the `MAIL_ENCRYPTION` is type of an encryption (like `tls`).

If you have troubles to set up a database or mail box access just loop up on Laravel
documentation.

Now you should get Google Recaptcha v2 site key and secret key and put them into `.env` file:

```
NOCAPTCHA_SECRET=secret-key
NOCAPTCHA_SITEKEY=site-key
```

Currently, an application accepts Google Recaptcha v2. These key can be retrieved from
[https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin). If you will be in this site, just choose Recaptcha v2 and 'checkbox' option.

After setting up database access you should populate tables on database:

```
php artisan migrate   # make database tables (apply all migrations)
php artisan db:seed   # put all needed data to database
```

NOTICE: the second command print important 'admin' random password:
you should remember it or store it in some safe place.

This should be done before calling `php artisan db:seed` command.

Before building the Javascript frontend you should set correct path to resources
available on server in `webpack.mix.js`:

```
mix.setResourceRoot('/checkers/public/');
```

In this example, frontend files and same application will be hosted on URL `/checkers/public/`. If you want to host this application under root URL (just `/`) you should comment this line (by preceding `//`).

Now, you can build a Javascript frontend:

```
npm run TYPE            # build main application
php artisan lang:js     # build translations for javascript
```

Where `TYPE` is type build: `dev` for development environment, `production` for production installations (for example on hosting).

### Running application

To run application on built-in Laravel development web-server you should run:

```
php artisan serve
```

or, just configure your HTTP web server with PHP support to host this application by setting
a document root, its permissions and other setup.

