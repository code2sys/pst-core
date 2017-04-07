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

