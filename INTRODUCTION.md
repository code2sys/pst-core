Introduction to Coding for PST
==============================

_1/3/19 Jonathan L Brown_

Overview
---------

The product is a reusable website for motorsport dealerships. There was an initial emphasis on parts - parts come from Tucker Rocky, Helmet House, Western Powersports, etc. PST has a central database of parts; these get synchronized daily to the stores. There are over 5,000 parts with over 12,000 unique SKUs as of this writing.

We've found in the last 18 months that the major units - motorcycles, ATVs, UTVs, 4x4s, V-Twin bikes, dirt bikes, snowmobiles, etc - are a bigger slice of the dealer pie. We can't replace the website for a dealer with major unit inventory with a parts store website. So, most of the 2018 development focused on major units.

Major Units
------------

Here are a bunch of random facts about major units:

* Major units can be entered by the admin, received from Lightspeed (a **D**ealer **M**anagement **S**ystem), received from Dealer Track (another DMS), or backloaded from a database of stock trims (CRS).
* We've taken pains to prioritize things first from the admin, then from the DMS, and then finally from the stock database.


Coding 
------

* The store was originally implemented as a CodeIgniter 2 stand-alone store called "MotoMonster". The PST Portal (sometimes called the MXPortal) was coded separately. These two agreed on some database tables - part, partvariation, machinetype, model, partnumber, etc.
* In April 2017, we made two new stores - 6Gear and Clemmons Motorcycles - and thus we were staring at code proliferation. Worse, we had three different contractors (all under the name "Benz"), who had varying degrees of coordination and code quality. They worked together, but they kept editing each other's files, and MotoMonster, the demo store, 6Gear, and Clemmons began drifting apart.
* In April 2017, Jonathan checked everything into git and sorted out the difference between those, establishing the repository structure.
* There are four repositories that matter to you in general: 
    * pst/core: This is the shared core functionality. Every store has a symbolic link to a checkout of this repository.
    * pst/store: This is the store-specific repository. Every store has a checkout of this repository. You'll see an environment.dist.php - that gets copied to environment.php (which is gitignored). You'll see lots of constants in that file that are used to turn on/off store functionality so the same core repository can deliver a parts store, a store with dealer stock in used bikes only coming from Lightspeed, and a full-featured store with new and used inventory and a feed from Dealer Track.
    * pst/database: This is used to track changes to the database schema. Ask Jonathan if you don't know what to do here.
    * pst/objectlib: This is used to factor out some functionality. The problem with the original store is that it is spaghetti code. I've tried to centralize as much as possible when making changes, and I put functionality here, so that it can be reused. Look for the PSTAPI global variable in the core repository.
* Everything is managed by git flow. Create merge requests in git lab once your code has been reviewed. See the other .md files for more information on this.

Cron
----

Unfortunately, the store still uses cron jobs for most things. I would like to switch to an event queue in 2019. The cron jobs are primarily in controllers/croncontrol. There is a minute, hour, day, and week, and month cron job. The minute cron job was split between emails and lightweight feeds. Heavyweight feeds are in the hour job. 


Data Feeds
----------

There are push and pull feeds:

* Lightspeed Units: This gets pulled once in the daily feed, and it can be requested by the admin from the hour feed. Look in models/lightspeed_m for details.
* eBay feed: This is currently broken.
* Cycle Trader: This involves generating a CSV file once per day in the daily feed. It can also be done on demand. For on-demand, it is done in the lightweight feeds on the minute.
* Google XML: This involves generating a large XML file once per day in the daily feed. It can also be done on demand. For on-demand, it is done in the lightweight feeds on the minute.
* Lightspeed Parts: This can be done daily, too. This one has withered on the vine.

The thing about all of these - whether push or pull - we initiate the action. 


FTP + API
---------

We had requests to receive files from Dealer Track, so we eventually had to make our own FTP server. This is usually done with a jailed account that is locked into an incoming folder. We then monitor the folder (using the minute cron job on the FTP server, since there are not that many), and we then post to the corresponding store API function. Here's an example of the cron job declaration:


<pre>
*	*	*	*	*	php /root/monitor_and_post_files.php --incoming_directory /home/LACH2INV/ftp/in --outgoing_directory /home/LACH2INV/ftp/out --tmp_directory /tmp --post_url https://www.valdostapowersports.com/internalapi/dealertrack 
*	*	*	*	*	php /root/monitor_and_post_files.php --incoming_directory /home/JHHPOWER/ftp/in --outgoing_directory /home/JHHPOWER/ftp/out --tmp_directory /tmp --post_url https://www.hondapowerstore.com/internalapi/dealertrack 
*	*	*	*	*	php /root/monitor_and_post_files.php --incoming_directory /home/dealermade/ftp/in --outgoing_directory /home/dealermade/ftp/out --tmp_directory /tmp --post_url https://www.hondapowerstore.com/internalapi/dealermade
</pre>


You can see what monitor_and_post_files.php looks like as it's in the tools directory. I had to copy it out of there to the FTP server, so realize that the one on the server is a snapshot in time.

* We make a CHROOT jail FTP account for each incoming input. So, you can see, username LACH2INV is stuck in /home/LACH2INV/ftp/in
* Dealertrack requires stupid, uppercase usernames and stupid, short passwords. Assume the whole FTP server is suspect at all times. I use different passwords on it than elsewhere.
* As you can see, when Valdosta receives its inventory from dealertrack, after a minute or so, it posts that to the dealertrack function on the internalapi controller.
* The Internalapi controller has an IP filter. As you can see, for development, you should override global $Permitted_InternalAPI_IPs in your store's environment.php - don't change my defaults in that code. 
* If it is easy enough - e.g., a CSV file of < 500 lines on average, like the dealertrack file - you can just process it in the Internal API controller.
* If it is more complicated - e.g., the dealermade image feed - we had to make a queueing system that would then get picked up by a corresponding cron job.


Templates
---------

There was a huge mess as it came to managing the UI. Starting in spring 2018, we began the process of splitting out the views into templates. The templates can be overridden on a specific store without changing any of the code in the core. Check the MUSTACHE_TEMPLATES.md for more information.

Coding Standards
----------------

Most of this was written in response to the errors and problems with the initial harmonization of the code. Please glance through it. It was not an easy time converting over to this process, but our processes have worked for us since then.



    
    




