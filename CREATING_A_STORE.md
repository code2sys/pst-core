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


Boostrap The Database Commands
-------------------------------


Add in the Symlinks 
--------------------

<pre>
ln -s ../core/adminer.php .
ln -s ../core/application .
ln -s ../core/Checkout/ .
ln -s ../core/lib/ .
ln -s ../core/qatesting_application/ .
ln -s ../core/simpleimage.php  .
ln -s ../core/system/ .
ln -s ../core/vendor/ .
cd html
ln -s ../../core/html/assets/ .
ln -s ../../core/html/basebranding.css  .
ln -s ../../core/html/qatesting/ .
</pre>


Virtual Host Configuration
---------------------------




