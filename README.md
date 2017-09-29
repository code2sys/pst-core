PST Core
========

The idea of this repository is that there is a part of the store that doesn't change - that it is the same for all customers - and then, there is a part of the store that depends on customer-specific stuff. This is the reusable part. Configuration switches in the form of constants get loaded in environment.php. Then, CodeIgniter loads environment.php. Then, you have this. 

Enabling the Vault
------------------

1. Run the query to create the vault page.
2. Add the ENABLE_VAULT and TOP_LEVEL_PAGE_ID_VAULT to environment.php.

Server IPs
----------

If you change the server IP, you have to go add the IP to the whitelist in the Portal for receiving.