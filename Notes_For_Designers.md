Notes for Designers
===================


Section-Specific Functionality
------------------------------

September 17, 2018

While doing the Stuart website, Rohit had two tasks:

4. Remove the major unit icons from all of the Online shopping pages. Aka, this should only appear on the home page, major unit list pages, and major unit detail pages.
6. On Mobile, the Search Parts and Accessories search bar needs to be removed from the major unit list page and major unit detail pages. 

This can be done with the master-specific classes.  There are around seven "master files" that form the main page frame, as well as a special one for motorcycles/major units.

* benz_views/header.php - which is only for motorcycles/major units
* master_v
* master_v_bikefront
* master_v_brand
* master_v_brand_list
* master_v_front
* master_v_new
* s_master_v

For these, I've added classes to the BODY tag to identify which one you are in. Thus, if you don't want to show the major units everywhere, just on home and the major units area, you could do something like:

<pre>
.major_units_section_custom_label_in_mainheader {
    display: none;
}
body.master_v_front .major_units_section_custom_label_in_mainheader,
body.benz_views_header .major_units_section_custom_label_in_mainheader {
    display: block;
}

</pre>



