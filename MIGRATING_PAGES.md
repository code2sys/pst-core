Migrating Pages
===============

There was a task for the SS website to migrate service pages from Demo. Here's how I did that:

* First, you'll want to update the store checkout of pst/store, and make sure the ckeditor uploads directory is writeable as appropriate. I pulled in some images that are the images that were in the main service request page as of this writing.
* Second, you'll then want to run a script in the MXPortal:

<pre>
for id in 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 
do 
php html/index.php cron/migratePage/demo_v1/jbdev_v1/$id
done
</pre>

Of course, replace jbdev_v1 by the appropriate database handle.

* You will get output that looks like this:

<pre>
Slider image to move: 
1524681942.jpg
Slider image to move: 
1524682191.jpg
Slider image to move: 
1524682420.jpg
Slider image to move: 
1524682573.jpg
Slider image to move: 
</pre>

* Go to your media directory in your store and run something like this:

<pre>
 for image in 1524670194.jpg  1524670748.jpg  1524671100.jpg  1524671450.jpg  1524672114.jpg  1524672593.jpg  1524672962.jpg  1524673320.jpg  1524673523.jpg  1524680042.jpg  1524680351.jpg  1524680539.jpg  1524680761.jpg  1524680955.jpg  1524681125.jpg  1524681457.jpg  1524681795.jpg  1524681942.jpg  1524682191.jpg  1524682420.jpg  1524682573.jpg  1524682789.jpg  1524682979.jpg  1524683291.jpg  1524683495.jpg 
 do
 wget http://demo.powersporttechnologies.com/media/$image
 done
</pre>

The sliders and the vault are the only ones with images you have to move. I haven't found a vault that needs to be moved; you may have to put it in a different directory.

* Be sure to fix ownership on those as well.
