PST Core
========

The idea of this repository is that there is a part of the store that doesn't change - that it is the same for all customers - and then, there is a part of the store that depends on customer-specific stuff. This is the reusable part. Configuration switches in the form of constants get loaded in environment.php. Then, CodeIgniter loads environment.php. Then, you have this. 