    BuoyReport.com
*   *   *   *   *   *
__/\__/\__/\__/\__/\__
----------------------
__/\__/\__/\__/\__/\__
----------------------
======================
       welcome!


local development

`npm install`
`npm run start-dev`
`npm run build-dev`
load http://localhost:3000/

Mysql 
-----
login: `pm2 startup`
start: `sudo systemctl start mysql`
status: `systemctl status mysql.service


Build
-----
npm run build


Deploying
---------
git remote add live joey@45.55.23.155:/var/repo/buoyreport.git
git push live master


502 bad gateway
---------------
ssh joey@45.55.23.155
pm2 status
cd /var/www/buoyreport.com/
pm2 start app.js
