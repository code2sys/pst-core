Migrating Pages
===============

There was a task for the SS website to migrate service pages from Demo. Here's how I did that:

* First, you'll want to update the store checkout of pst/store, and make sure the ckeditor uploads directory is writeable as appropriate. I pulled in some images that are the images that were in the main service request page as of this writing.
* Second, you'll then want to run a script in the MXPortal:

<pre>
for id in 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 
do 
php html/index.php cron/migratePage/ss_v1/allambs_v1/$id
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
 for image in 1529423842.jpg 1529422190.jpg 1529422267.jpg 1529422328.jpg 1529422363.jpg 1529422399.jpg 1529422582.jpg 1529422622.jpg 1529422751.jpg 1529423075.jpg 1529423201.jpg 1529423260.jpg 1529423290.jpg 1529423327.jpg 1529423363.jpg 1529423396.jpg 1529423422.jpg 1529423469.jpg 1529423506.jpg 1529423537.jpg 1529423571.jpg 1529423605.jpg 1529423635.jpg 1529423667.jpg 1529423694.jpg 1529423725.jpg
 do
 wget https://www.s-smotorsports.com/media/$image
 done
</pre>

The sliders and the vault are the only ones with images you have to move. I haven't found a vault that needs to be moved; you may have to put it in a different directory.

* Be sure to fix ownership on those as well.
