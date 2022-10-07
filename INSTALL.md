# Installation of the ep-3 Bookingsystem

## Requirements

Make sure that your system meets the system requirements:

- Apache HTTP Server 2+ 
   - With `mod_rewrite`
- **PHP 8.1+**
   - With `intl` extension
- MySQL 5+  
  (or equivalent MariaDB version)


## Preparations

This section is only relevant if you have cloned the repository directly from GitHub!  
If you have downloaded a pre-made package from our website, continue with the installation.

0. Install dependencies via Composer  
   (for the time being, we need to enforce it with `composer install --ignore-platform-reqs`)


## Installation

1. Setup the local configuration:
   - Rename   `config/init.php.dist`   to   `init.php`
   - Optionally edit and customize the   `init.php`   values
     <br><br>
   - Rename   `config/autoload/local.php.dist`   to   `local.php`
   - Edit the   `local.php`   and insert your database credentials
     <br><br>
   - Rename   `public/.htaccess_original`   to   `.htaccess`  
     (if you experience webserver problems, try instead renaming   `public/.htacess_alternative`   to   `.htaccess`)

2. Enable UNIX write permission for
   - `data/cache/`
   - `data/log/`
   - `data/session/`
   - `public/docs-client/upload/`
   - `public/imgs-client/upload/`

3. Setup the database by calling the `setup.php`

4. Delete the setup tool
   - `public/setup.php`

5. Delete any files in the following directory:
   - `data/cache/`

6. Optionally customize public files:
   - `css-client/default.css` for custom CSS
   - `imgs-client/icons/fav.ico`
   - `imgs-client/layout/logo.png` (75x75)


## Issues

If you run into any issues: Many problems have already been discussed and solved in the
[GitHub issue section](https://github.com/tkrebs/ep3-bs/issues).


## Deployment

Once you are satisfied with the system and want to use it in the wild, 🚨
please make sure to set the **Apache document root directly to the `public` directory**
so that your domain will read like this:

`https://example.com/`

And not like this:

`https://example.com/public/`

The latter is a huge security threat, that is only acceptable while testing the system.

You may also consider to use a subdomain, like this:

`https://bookings.example.com/`


## Custom modules

Simply copy custom or third-party modules into the `modulex` directory and they will be loaded automatically.
