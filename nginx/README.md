## Nginx configuration

In order to use ep-3 with a [nginx server](https://nginx.org) you have to add the two files to your local folder `/etc/nginx/conf.d/`

The first file defines the SSL part for `booking.example.com`. You most probably have to change the `root` directive and the `fastcgi_pass unix:` part.

The second file redirects all traffic for all domains from 80 to 443.
