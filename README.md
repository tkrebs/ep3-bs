# ep-3 Bookingsystem

The ep-3 Bookingsystem is an open source (MIT licensed) web application to enable users to check and book free places of
an arbitrary facility easily online via one huge calendar.

It was initially developed to enable booking free squares of a covered court for a tennis club, improved along some
versions, tried to offer commercially as a SaaS - and finally released as open source software.

Among its primary features are extensive customization capabilities (thus making it interesting even outside the tennis
branch), multilingualism (currently shipped with english and german), an interactive, easy-to-use calendar, an
easy-to-use and easy-to-understand backend, a consistent and clear visual design and a fully responsive layout (thus
looking nice on mobile devices as well).

More features may be explored via our website (http://bs.hbsys.de/) or simply by downloading and trying the system
yourself.

## Documentation

- Installation instructions can be found in [INSTALL.md](https://github.com/tkrebs/ep3-bs/blob/master/INSTALL.md)
- Update instructions can be found in [UPDATE.md](https://github.com/tkrebs/ep3-bs/blob/master/UPDATE.md)

Further documentation and technical details can be found in the following directory:

```
data/docs/
```

## Architecture

The system is based on the well-known LAMP stack (Linux, Apache 2, MySQL 5+, PHP 8.1+) and the powerful
[Zend Framework 2](http://framework.zend.com/) (2.5).

As of version 1.9.0, it requires at least PHP 8.1 and is compatible and tested with up to PHP 8.4. 

Dependencies are managed with [Composer](https://getcomposer.org/).

The source code is version controlled with [Git](http://git-scm.com/) and hosted at [GitHub](https://github.com/).

The link to the GitHub repository is

```
https://github.com/tkrebs/ep3-bs
```

where you can find stable and (latest) development releases.

## Versions

The current version is 1.9.0 from May 2025.

Version 1.9.0 applied IDE inspections for PHP 8.4 compatibility.

Version 1.8.1 fixes an email sending related bug.

Version 1.8.0 provides compatibility with PHP 8.1 by overriding and fixing the essential Zend Framework 2 components.
It also fixes some bugs, added a file-storage-only mail option and removes some legacy code (mainly, the file manager).

Version 1.7.0 provides compatibility with PHP 7.4 by overriding and fixing some of the Zend Framework 2 components.

Version 1.6.4 introduced some features required during the COVID-19 pandemic, including limits to active concurrent bookings and minimum booking ranges. It also includes minor bug fixes and improvements.

Version 1.6.3 introduced some GDPR compliance based changes and requested features.

Version 1.6.2 changed the configuration behaviour and requires some manual changes (see data/docs/update.txt). Otherwise, the update will not work.

Version 1.6 introduced some requested features and fixed quite some bugs. It also introduced better support for custom translations and modules.

Version 1.5 introduced some requested features (billing administration, custom billing statuses and colors) and fixed some bugs.

Version 1.4 introduced some requested features and the latest third party libraries and frameworks.

## Bug reports, feature requests, ideas ...

We use the GitHub Issue Tracker for such things:

https://github.com/tkrebs/ep3-bs/issues
