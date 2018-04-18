Update July 6, 2017 JLB
=======================

Pardy added support for banners. The banners have a few sharp edges. 

First, you need the banners, or at least you need a directory called html/bannerlibrary. This should be a symlink.

I simplified the rest so that's all you have to do for deployment.


Update May 10, 2017 JLB
=======================

We added fields to the environment.dist.php for symbolic constants for the categories that are special. You will need to verify that these numbers are correct in your environment.


Creating a Store
================


1. Check out the three repositories in a single directory.
2. Create the database.
3. Create the environment.php file.
4. Bootstrap the database.
5. Run "composer install" in your core directory.
6. Add in the symlinks.
7. Create an apache virtual host configuration.
8. Set up the cron jobs.
9. Put the site name in html/site.txt.
10. You have to run croncontrol/migratePagesIssue80 on the store.

Boostrap The Database Commands
-------------------------------

<pre>
mysql parveen --user=root -p < required/20170407.001.JLBSchema.sql 
mysql parveen --user=root -p < required/20170407.002.JLBEbaySchema.sql 
mysql parveen --user=root -p < required/20170407.003.JLBBasicData.sql 
mysql parveen --user=root -p < required/20170407.004.JLBStore.sql 
php database_update.php --config_file ../store/environment.php  --directories required/ --record_only
</pre>

Thereafter, you should be able to just use: php database_update.php --config_file ../store/environment.php  --directories required/


Add in the Symlinks 
--------------------

<pre>
ln -s /var/www/core/adminer.php .
ln -s /var/www/core/application .
ln -s /var/www/core/Checkout/ .
ln -s /var/www/core/lib/ .
ln -s /var/www/core/qatesting_application/ .
ln -s /var/www/core/simpleimage.php  .
ln -s /var/www/core/system/ .
ln -s /var/www/core/mcache/ .
ln -s /var/www/core/mtemplates/ .
ln -s /var/www/core/vendor/ .
cd html
ln -s /var/www/core/html/assets/ .
ln -s /var/www/core/html/basebranding.css  .
ln -s /var/www/core/html/qatesting/ .
</pre>

You also need to link the banner library to html/bannerlibrary.
ln -s /var/www/productionbanners html/bannerlibrary

And fix ownership
chown -R customer1ftp.customer1ftp .
chmod -R g+w .

Get a dev cert if you need one:
certbot certonly --webroot --webroot-path=/var/www/stores/pdi/html -d sierranevadapowersport.com

Virtual Host Configuration
---------------------------




Cron Jobs
---------

Make sure to change to your directory...

<pre>
*	*	*	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/emails
*	*	*	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/feeds
15	*	*	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/hourly
0	2	*	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/daily
0	22	*	*	6	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/weeklys
0	0	1	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/monthly
*	*	*	*	*	cd /var/www/pardy.powersporttechnologies.com/store && php html/index.php croncontrol/processparts
</pre>


Requesting a Development SSL Certificate
-----------------------------------------

You don't have to live with an SSL certificate error in your development environment; use a free one as follows:

For the JBDev directory, which was muffsmotorsports.powersporttechnologies.com in /var/www/stores/muffsmotorsports/html

certbot certonly --webroot --webroot-path=/var/www/stores/CMCycles/html -d cmcycles.powersporttechnologies.com


