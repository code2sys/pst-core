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



