Update July 2, 2017
===================

Escape from Programming 101
---------------------------

I had to redo the admin category code. I found this horrible code in the category_v.php view as well as in the category function that:

1. Presumed that there would only ever be 4 levels of categories.
2. Made explict iteration over these.

Can't we use a recursive function?

The view iterated over the top level, then the next level, then the next level, in one giant foreach loop. I know that they tell you to avoid recursion, but sometimes it's the right tool. Here:

<pre>
function printCategoryRow($category_id, $categories, $depth = "") {
	if (array_key_exists($category_id, $categories)) {
		$null_cat = is_null($category_id);

		foreach ($categories[$category_id] as $category) {
		    //... Here we output the TR
			printCategoryRow($category["category_id"], $categories, $depth . "&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	}
}

printCategoryRow(NULL, $categories);

</pre>

This allowed us to slip in a visual cue to indent the category name so we could observe the structure. Further, it meant that we didn't have four different rows on the page, with subtle variations, which caused an error in the 3rd and 4th one since there was a typo.

Here I used iteration instead of recursion to do the exact same thing.

First, the horrible code:

<pre>
foreach ($categories[$postData['category_id']] as $subCat) {

    $updateCategories[$counter]['parent_category_id'] = $subCat['parent_category_id'];
...
    $catArr[$subCat['category_id']] = $subCat['category_id'];

    if (@$categories[$subCat['category_id']]) {
        foreach ($categories[$subCat['category_id']] as $subsubCat) {

            $secondCounter = count($updateCategories);
            $updateCategories[$secondCounter]['parent_category_id'] = $subsubCat['parent_category_id'];
...
            $updateCategories[$secondCounter]['notice'] = $subsubCat['notice'];
            $catArr[$subsubCat['category_id']] = $subsubCat['category_id'];

            if (@$categories[$subsubCat['category_id']]) {
                foreach ($categories[$subsubCat['category_id']] as $subsubsubCat) {

                    $thirdCounter = count($updateCategories);
                    $updateCategories[$thirdCounter]['parent_category_id'] = $subsubsubCat['parent_category_id'];
...
                    $updateCategories[$thirdCounter]['notice'] = $subsubsubCat['notice'];
                    $catArr[$subsubsubCat['category_id']] = $subsubsubCat['category_id'];
                }
            }
        }
    }

    $counter++;
}
</pre>


The counters don't make sense; the code is almost exactly the same in each block with subtle differences - the exact same subtle differences that have caused problems throughout the code. cat, subCat, subsubCat, and subsubsubCat is a horrible naming convention.


Here's how to rewrite this without all the craziness:

<pre>

$parents = array($postData['category_id']);

while (count($parents) > 0) {
    $current = $parents;
    $parents = array();

    foreach ($current as $c_id) {
        if (array_key_exists($c_id, $categories)) {
            $subcats = $categories[$c_id];
            foreach ($subcats as $subcat) {
                $parents[] = $subcat["category_id"];
                $updateCategories[] = array(
                    "parent_category_id" => $subcat["parent_category_id"],
...
                    "ebay_category_num" => $subcat["ebay_category_num"],
                    "notice" => $subcat["notice"]
                );
            }
        }
    }
}
</pre>

A Minor Gripe
-------------

I observed that the field was called "mark_up", but the input was called "mark-up", which meant that, again and again, the field "mark_up" and "mark-up" kept having to be swapped. That's just going to cause bugs in the future when you don't use the same names for database columns and form inputs.



The Curse of @
--------------

In that same code, I found that, whenever there was a new category added, the system locked up. Why? Because of this:

<pre>
if (@$categories[$postData['category_id']]) {
</pre>

When you added a category, it treated it as the null case, hiding the warning about coersing an undefined value, and then it would reprocess every part in the system. 

Here are two simple facts:

   1. You don't have to reprocess parts for a new category since it *has no parts*.
   2. You don't ever reprocess the whole set because the top level category, NULL, is not a category.
   
So, with this observation, the correct way to write this code to make it process only an existing category with a non-null category ID is:

<pre>
if ($postData['category_id'] > 0 && array_key_exists($postData['category_id'], $categories)) {
</pre>



Too Many Inserts
----------------

In that same code, there was a query that looked like this:

a. Fetch all parts associated with a category from partcategory
b. Loop over that list of parts
c. Insert them one-by-one into queud_parts

This is *a horrible idea*. It will create a single insert for every part. If you can't figure out the query for this, you should bring this to the attention of a senior developer on the project.

Here's the horrible code:

<pre>
$this->db->select('part_id');
$records = $this->selectRecords('partcategory', $where);
if ($records) {
    foreach ($records as $rec) {
        $where = array('part_id' => $rec['part_id']);
        if (!$this->recordExists('queued_parts', $where)) {
            $data = array('part_id' => $rec['part_id'], 'recCreated' => time());
            $this->createRecord('queued_parts', $data, FALSE);
        }
    }
}
</pre>

Here's how I fixed it:

<pre>
$now = time(); // I don't want the query to somehow do multiples
$this->db->query("Insert into queued_parts (part_id, recCreated) select distinct partcategory.part_id, $now from partcategory LEFT OUTER JOIN queued_parts on partcategory.part_id = queued_parts.part_id where queued_parts.part_id is null and partcategory.category_id = ?", array($category_id));
</pre>


I observed that the site no longer locked up for 10 minutes on demo when adding a category. It just worked, which is what's needed. Too many inserts can be the death of performance.


Update June 24, 2017
====================


*Question*
For email sending can i use the default mail function of PHP or i need to use any other api like sendgrid/mailchimp or you have any other sir?

*Answer*

You can use the built-in PHP mail function for sending email. If you need to send something with attachments, we can add Swift Mailer, please let me know. You should use the SMTP server on the local host - postfix is installed and configured. You should set a fully-qualified From: address; FROM_EMAIL_ADDRESS is a symbolic constant defined for all environments that you can use.

[Note: We added Swift Mailer. Use that for HTML emails.]

Update June 13, 2017
====================

Don't do this:

<pre>
modified:   html/qatesting/bx_custom_assets/img/VAULT.png
modified:   html/qatesting/bx_custom_assets/img/Vault.png
</pre>

You see, the small image and the big image had the same name, differing only by case. Windows and Mac OS have varying degrees of case-insensitive file systems. This is a bad approach.


Update June 11, 2017
====================

On the Use of Meaningful Names and Encapsulation
----------------------------------------------

There is an excessive habit of using meaningless names for variables and functions, and for shoving new functionality in a higgledy-piggledy manner wherever it fits without making a spot for it. Examples of this include:

- Just sticking things on the Welcome controller for front end functionality. The motorcycle code was a good example of this.
- Just sticking things on the Admin controller. There are too many examples of this.
- The page for listing motorcycles was called "benz_product". We don't need to have memorialized for all time that this was done by Benzaitens Group; it would have been great to indicate that it was the motorcycles, since there are other products, and they had nothing to do with this.
- In the Point of Sale interface, there was a variable that clearly represented the quantity of parts to take from dealer inventory. Rather than something like $dealer_inv_qty, it was called $abcd1. It took forever to find a logic error based on this variable. 

I think that so many errors that appear to arise out of obvious misunderstanding. For example, here was the logic error:

<pre>
 if ($distRec['quantity_available'] >= $product['qty']) {
     $qtyLft = 0;
     $abcd1 = $product['qty'];
 } else {
     $qtyLft = $qtyLft - $distRec['quantity_available'];
     $abcd1 = $qtyLft - $distRec['quantity_available'];
 }
</pre>

It took forever to realize that $abcd1 was mis-identified because it is *not obvious* from this usage what this is supposed to be.

The project has a large number of errors that are minor logic errors like this. It clearly confuses both the original developer and any follow-on developers as to what is really gone when code isn't encapsulated and function and variable names do not have a meaningful value.


Update June 4, 2017
===================


Some feedback on the motorcycle code; I don't know who did all of this, so please consider this as general advice:

1. The motorcycle functionality was added to the welcome controller. Then, the routes config was changed to handle the different URLs. When there is a major new area of functionality - make a new controller for it. The current code in develop now uses motorcycle_ci.php as the controller.
2. Next, there were about 10 instances in the code where you broke out the condition, brands, years, and categories. These had subtle differences:
    
    * Sometimes, you didn't want one of the fields - e.g., brands, years, categories
    * Sometimes, it came from $_POST instead of $_GET
    * Sometimes, you expected something to be an array, other times you expected it to be a string. 
    * Sometimes, you used the where_in function (which I think prevents SQL injection), and other times, you built a string (which looked ripe for being exploited)

So, I made two functions in motorcycle_m:

* assembleFilterFromRequest - this exists to create the original $filter array based on inputs
* buildWhere - this exists to factor out handling the $filter array from six functions in motorcycle_m.

There were three more occurrences, but I removed those functions. I could not find them called anywhere in the code, and having getMotorcycles and getFilterMotorcyclces in the same model, with almost the same code, except you were passing in an offset and calling it $limit in one of them, was really confusing.

Don't these functions seem shorter and easier to make correct?

<pre>
    public function getFilterTotal( $filter ) {
        $where = $this->buildWhere($filter);
        $this->db->select('count(id)');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    public function getMotorcycleMake($filter = array()) {
        $where = $this->buildWhere($filter);
        $this->db->select('make');
        $this->db->group_by('make');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }
</pre>

Before, they all had about 30-50 lines of duplicated code with subtle variations. Now, there are just three optional parameters to buildWhere to turn them on and off:

<pre>
    public function getMotorcycleYear($filter = array()) {
        $where = $this->buildWhere($filter, true);
        $this->db->select('year');
        $this->db->group_by('year');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }
</pre>

This fixes several things discovered in the motorcycle code when testing for BX and it applied to all stores, including that the page numbers were not reliably calculated correctly (even though you computed the bikes to show correctly - showing again the problem of having copy-and-pasted the code with subtle variations....)  and that the filters were not being applied when you changed pages - e.g., if you applied a filter that limited it to, say, two pages, and switched to page 2, suddenly, the filter would fall off the results but shown checked on the page.



Update May 22, 2017
===================

Changing Text
-------------

Eventually, this site needs an override of all end-user-visible language so that it can be edited at will.  In the short term, we will use constant overrides.

I had to change the wording from SCHEDULE TEST DRIVE to SCHEDULE A DEMO for Six Gear. Once again - we use wording overrides. Be sure to follow the naming convention and to give your new constant a unique name.

Addition to environment.php - only as appropriate:

<pre>
// wording override
define('WORDING_SCHEDULE_TEST_DRIVE', 'SCHEDULE A DEMO');
</pre>

Change to the view to use the constant only if defined:

<pre>
<li style="margin-right:10px;" data-toggle="modal" data-target="#myModal"><a href="#"><?php if (defined('WORDING_SCHEDULE_TEST_DRIVE')) { echo WORDING_SCHEDULE_TEST_DRIVE; } else { ?>SCHEDULE TEST DRIVE<?php } ?></a></li>
</pre>


Update May 14, 2017
===================

When there is a file like the customer's logo, do not upload a PST logo and then reference that directly. The logo file is /logo.png.


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



