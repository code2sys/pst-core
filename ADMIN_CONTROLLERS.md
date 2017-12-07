ADMIN Controllers
=================


December 7, 2017
----------------

The admin controller was way too big, but we have things hardcoded everywhere. CodeIgniter 2 will not support delegated controllers, so I have instead done the following through abstract classes:

There is a chain of abstract classes that live in the controllers/abstractadmin directory. This should prevent them from being called directly, and permit you to drive down to the admin file. Since we do PHP caching pretty aggressively, I don't expect too much of a performance hit.

There is a chain of inheritance (because PHP sucks in this way with single inheritance, where, ideally, we'd split this thing up with multiple inheritance) where the firstadmin gets it from MasterController and finaladmin is what admin.php gets it from.

Of course, that means you have to manage the chain. Here it is.


* Master Controller
* Firstadmin
* Motorcycleadmin
* Financeadmin
* Customeradmin
* Productsbrandsadmin
* Orderadmin
* Employeeadmin
* Individualpageadmin
* Finaladmin



If you have general-purpose functions, those can go in Firstadmin. If you have to make a new group of functionality, insert it as the penultimate item, extend the one in front of it in line, and then update Finaladmin.




