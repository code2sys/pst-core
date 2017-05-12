Update May 11, 2017
===================

* Do not use !important CSS rules.
* Check your CSS on a narrow screen to simulate a mobile browser.

Update #2 May 10, 2017
======================

There is no need to check in backup, work-in-progress, or other files that aren't directly used. This just clutters up the repository and gets confusing when you do a search.


Update May 10, 2017
===================


First new rule as a general principle:

**Bad practices in the code should not be perpetuated because it was like that, so you figured doing it like that again was OK, even if you know it's a bad idea.**


Which has a specific version that approaches commandment level:

**Though Shalt Not Hardcode ID Numbers.**

Here's what goes wrong:

1. You add a value to a table in your development environment. You want this to be special.
2. The auto-incrementing serial number key on that table gives it an ID number.
3. You put that ID number in your code. 
4. Eventually, some day, there is a train wreck. It could be immediately upon launch - for example, if a customer already added something to that table, so the number is just plain wrong. It could be weeks down the line when some maintenance programmer has no idea what that ID number means. This creates a trap for the future. 

Exogenous vs. Endogenous Keys
------------------------------

First, the definitions:

Exogenous: relating to or developing from external factors.
Endogenous: having an internal cause or origin.

When you tell me a page is named "VTwin", that a part is produced by Tucker Rocky with part number 123-456, or that a user has email address bvojcek@powersporttechnologies.com, you are telling me something about an endogenous key.  

Back in the original days of databases, before RDBMs packages were good with indexes, you would look into the data to find an obvious key. First and last name, email address, government ID number, etc. Now, you might still do that, and make the key unique, but all databases anymore are made with tables having auto-incrementing serial number.

Quick, which tells you something about what you are looking at _if you can't see my database_:

* I am looking at a user named Brandt Vojcek
* I am looking at the third user in my user table

Which one lets you answer in any meaningful way, "Who are you looking at?"?

A Good Solution To This Problem
--------------------------------

A good way to solve this problem of wanting to get the ID number for a specific category, page, etc - is to name it and fetch it using a query. Yes, queries take time, and, yes, at some point, we will have to do a performance review of everything. However, correctness comes before performance, and we have a compelling need for maintainable, correct code in this software project. Therefore, the following:

<code>
$query = $this->db->query("Select user_id from user where email = ?", array("bvojcek@powersporttechnologies.com"));
$row = $query->result_array();
$user_id = $row[0];
</code>

Is superior to

<code>
$user_id = 5;
</code>

Because nobody will know what user ID 5 means after some time.


An OK Solution To This Problem
-------------------------------

Let's say you don't want to write the query; you are convinced that this number will change infrequently. Still, you should not be hard coding a magic constant. This is just bad practice. 

1. PHP has a mechanism for constants. Make a feature branch in the store repository, edit environment.dist.php, and add in your new constant there. Further, make a comment in front of it explaining where that constant comes from.
2. Reference that constant in your code.
3. Add a note at the top of CREATING_A_STORE.md that explains that this constant has to be created.
4. Send a message to the build master explaining that, when your code is deployed, all the stores need this new constant to be filled in.

It's going to make so much more sense to future programmers if they can see that it is VTWIN_CATEGORY_NUMBER instead of 12345 in the code.


Coding Standards
================


* Use git flow for branching.  
* Never hardcode a hsotname. There are a couple things at your disposal:
    * The WEBSITE_HOSTNAME symbolic constant.
    * The jsite_url() function. This is in helpers/jonathan_helper.php. Use it instead of writing code like this:
    
    <pre>
    $new_assets_url = ( isset($_SERVER['HTTPS']) ) ? ("https://" . WEBSITE_HOSTNAME . "/qatesting/newassets/") : ("http://" . WEBSITE_HOSTNAME . "/qatesting/newassets/");
    </pre>
    
    Isn't this cleaner?
    <pre>
    $new_assets_url = jsite_url("/qatestings/newassets/");
    </pre>
    
* Always provide a meaningful alternate tag for images. At worst, put in [Image]. Don't put your name or company name in there.
* Put composer requirements in core.
* If you keep doing something, put it in a helper. Don't copy and paste code. You can make functions in application/helper/jonathan_helper.php; you can also make your own function helper if you prefer. If you make your own, please use your name or something similar to prevent namespace collision. Make sure to add your helper to the application/config/autoload.php file to autoload so people can use your functions.
* Use 4 spaces instead of hard tabs. 
* I'm going to use PHPStorm for all coding. Please use it or use similar settings. Don't just reformat a file and check it in with the change being to every line because you changed the tab stops.
* Make sure that words like this don't show up to customers: MotoMonster, motomonster.com, MM, Cloverfield Communications, Benzaitens, or any similar variation.
* Make sure you don't hardcode PST or Power Sport Technologist or PowerSport or any similar variation.
* Only write files in the customer web directory to html/media.
* Do not make inline styles. Use the style sheets. There is a new stylesheet,  basebranding.css. Put stuff there for the default appearance.
* Do not change views just to change colors for a customer. Put in styles in custom.css. 
* If you have custom JavaScript for a customer, you can include it in custom.js, but know that it loads last.
* The customer logo should be located in html/logo.png in the customer directory. 


Branding Images
---------------

You may be able to get around custom CSS by just overriding these files.

Overrides
---------

While merging Clemmons and 6Gear, I saw that there were some changes to the structure of views. This isn't going to work - we can't have views that have subtle changes between stores, or else we don't have one piece of software and we can't make updates.

Instead, here's the idea I've come up with. You may have another idea; so long as you don't break the reusability to other customers, and you get new updates to core code, your solution may be fine.

Here's the gist of the difference:

<code>
--- a/application/views/master/master_v_front.php
+++ b/application/views/master/master_v_front.php
@@ -76,8 +76,7 @@ $new_assets_url = ( isset($_SERVER['HTTPS']) ) ? ("https://" . WEBSITE_HOSTNAME
 		<div class="container_b">
 			<p class="creditCar_b fltL_b">
 				<span>Ph : <?php echo $store_name['phone'];?></span>				
-				<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>
-				<img style="margin:-5px 0 0 6px;" src="<?php echo $new_assets_url; ?>images/szk01.png" alt="Suzuki" />
+				<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
 			</p>			
 			<div class="loginSec_b navbar-right">
 				<?php if(@$_SESSION['userRecord']): ?>
</code>


Observe that they've injected an image tag in there. There should be a symbolic constant, STORE_DIRECTORY, specified in the environment.php file. Under that, there is an overrides directory. For this one, we're going to make the "viewpiece" name master-master_v_front-1. Use a numeric counter for the viewpiece in the file. Then, use the joverride_viewpiece helper function to include it. It's a little barbaric, but you can pass in parameters with the $params argument. My expectation is that these will usually be hardcoded. So, in this example, that image tag will be hardcoded in there. Then, in that spot in the view:

<code>
				&lt;?php if (FALSE !== ($string = joverride_viewpiece("master-master_v_front-1"))) { echo $string; } ?&gt;

</code>


Git Flow
--------

Git flow is a popular technique for managing a project like this. We're going to use it. Here are some articles:

* http://nvie.com/posts/a-successful-git-branching-model/
* https://www.atlassian.com/git/tutorials/comparing-workflows
* https://github.com/nvie/gitflow

Here's the idea:

* master is the branch that is in use in production.
* develop is where you should start coding.
* You should create a feature branch (e.g., git flow feature start 20170409.001.JLBProject)
    * Use the date in YYYYMMDD
    * Include the numbers using a three digit serial number starting 001
    * Identify yourself, e.g., I always use JLB
    * Give your project some identifier so you can recognize what feature you were working on - e.g. eBay, Motorcycles, etc
    * Remember, you're never going to be putting customer-specific code in here. You are going to put that in custom.css, custom.js, an override, or something else just for that customer. This code is supposed to be reusable for every customer.
* Have Brandt review your feature branch in your development environment.
* Once that is OK, and it passes all his testing, you can finish the feature branch.
* Deploy changes to the develop website, develop.powersporttechnologies.com. Verify your functionality there that it works OK once merged into develop. If there is a train wreck there, you will need to alert everybody that develop is dirty, then you'll need to fix it.
* Don't merge to master. That is the job of the build master. Currently, I (JLB) am the build master. Brandt can appoint other people to be the build master.
* When things are good to go, let me know, and I'll merge to master and deploy to staging.powersporttechnologies.com. You'll probably need to verify functionality there, too.
* After that, we're going to send it to the stores. You can verify in demo.powersporttechnologies.com [once we get that converted to this] or in a particular customer store.
* Make sure Brandt knows how the code is moving and what feature is moving. 



