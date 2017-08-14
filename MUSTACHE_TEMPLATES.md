Converting PST Websites to Mustache Templates
=============================================

_Jonathan L Brown (jonathan@orbro.com)_

_August 14, 2017_

Overview of the Problem
-----------------------

The main problem is:

*	The PST websites use a common PHP core.
*	The original Motomonster.com store from which all PST websites are derived is a classic CodeIgniter 2 website, using an MVC pattern, where the views are PHP files.
*	That means that the views are code – PHP intermingled with HTML + CSS – and so are cookie-cutter


A myriad of other little sins:
*	There is a “qatesting” directory that was supposed to be a staging area, but at leat one of the front end developers who did a considerable amount of work started linking things directly to the qatesting directory, so I cannot tell you which files are used without doing a full survey.
*	The sites use seven master files – mostly – as page frames. These master files were extracted haphazardly from the main code, and they included different style sheet files, different JavaScript, in different order. I’ve attempted to standardize common parts of these – e.g. tracking, header common elements like META description.
*	The motorcycle area uses its own separate “benz header” and “benz footer”, and so does not use the master files. 
*	Copy-and-paste ruled the day on making most of these files.
*	The original styles can be seen too many places – if just for a second. Nobody likes a blue button that quickly turns red because custom.css finally loads.

Proposal
--------

Ideally, there would be a one-two punch of templates and CSS as follows:

*	I would like a core set of mustache templates that can be overridden (but don’t have to be) per store.
*	I would like a core set of CSS files with a build script – ideally, we would factor it out into LESS so that the source files have variables instead of so many overrides.

Then, there would be a build script that runs, per store, to generate the store-specific CSS files. 

This is going to focus on the change to Mustache templates.

Why Mustache Templates?
-----------------------

Mustache templates are a straightforward, fast templating system. They have limited support for logic, but they are not completely logic-free. I was able to convert a similar project over to these and have had great success in a mass-personalization scenario. 

Where will the templates be?
----------------------------

The main templates will go in core/mtemplates.

Where will the store-specific templates be?
-------------------------------------------

Store-specific templates will go in a /override_templates directory under the store folder. 

How will it load the correct template?
--------------------------------------

We will use a wrapper function on load that exists to check first in the store directory, and, failing that, use the one from core. So, there will be a little performance hit checking whether one file exists, but it will probably be minor unless we have hundreds of templates.

How will templates be put into the existing system?
----------------------------------------------------

I suspect we can migrate the views without too much trouble. The views are mostly large chunks of HTML. I am going to migrate a few choice views over, and then somebody else can carry on in a similar fashion until all views are migrated.

Pointers and Pitfalls
---------------------

* Remember that Mustache assumes that things are escaped, non-HTML content by default. You have to use the {{{ }}} triple escaping instead of double if you need HTML content preserved.

* If there are defaults all templates could use, there is a spot in mustache_helper where you can put those.

Examples
---------



