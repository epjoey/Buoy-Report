PHP:
`brew services start php`

Nginx:
`sudo nginx -s reload`

Config:
`/usr/local/etc/nginx/nginx.conf`
This file must contain:
`include /var/www/buoyreport.com/nginx.conf;`

Logs:
`/usr/local/var/log/nginx/error.log`

--------
Apache:

Make sure you apache document root is "/Users/jhodara/Sites/Buoy-Report":
`sudo nano /etc/apache2/httpd.conf`

Restart apache:
`sudo apachectl restart`
or:
`sudo apachectl start`

To access mysql:
`mysql -u root -p`