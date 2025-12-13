    BuoyReport.com
*   *   *   *   *   *
__/\__/\__/\__/\__/\__
----------------------
__/\__/\__/\__/\__/\__
----------------------
======================
       welcome!


local development
-----------------

`npm install`
`npm run start-dev`
`npm run build-dev`
load http://localhost:3000/


Deploying
---------
git push main



Digital Ocean
-------------
ssh root@137.184.127.160

1️⃣ PM2 Commands

Check status, logs, or restart your app:

pm2 list
pm2 logs buoyreport
pm2 restart buoyreport --update-env
pm2 show buoyreport
--update-env reloads environment variables from .env.

To start from scratch:
pm2 stop buoyreport
pm2 delete buoyreport
pm2 start /home/ubuntu/buoyreport/app.js --name buoyreport



2️⃣ Nginx

Config file: /etc/nginx/sites-available/buoyreport

Enabled via symlink: /etc/nginx/sites-enabled/buoyreport

Restart after changes:

sudo systemctl restart nginx
sudo nginx -t   # test config



3️⃣ SSL / Certbot

Check SSL renewal:

sudo certbot renew --dry-run


If using old ./certbot-auto, consider switching to system Certbot.

Ensure cron/systemd timer is active for automatic renewal.



4️⃣ MySQL

Start or check status:

sudo systemctl start mysql
systemctl status mysql.service


Login:

mysql -u root -p


502 errors are usually PM2/Node, not MySQL.
mysqld will log errors to /var/log/mysql/error.log



5️⃣ Common Errors

502 Bad Gateway: Node server isn’t running, usually due to missing dependencies.

Fix: npm install + pm2 restart buoyreport --update-env.



6️⃣ Deploy Workflow (Git + PM2)
