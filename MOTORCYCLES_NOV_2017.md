Motorcycles, November 2017
==============================

We hired a guy named Mike to redo the motorcycles. Mike had a big vision that required a significant rework of the database. Mike hit some technical challenges, like his schema caused the database to lock up on getting data from 8 tables because, through aliasing, he managed to get 62 different temporary tables in a SELECT query that had to run every time the website loaded. Mike bailed out on the eve of having to launch something. Therefore, we are left with my solution.

We have an existing table for motorcycles from what Benzaitens wrote. It's pretty crappy, but it's what we have, and so we soldier on, since the rewrite went up in a giant poof of smoke and failure. Here are the big ideas:

* CRS has a database of information about bikes.
* CRS has its own way of talking about bikes, but the most important thing to think about is trim. You need to know the trim ID of the bike in CRS to know all the related information.
* I made a separate project, pst/crsapi, that exists to serve up the information from CRS. 
* There is information coming in from lightspeed, too.

The giant hangup from Mike was his desire to track all changes, forever. This isn't really needed. Here's the basics of what I'm going to include for these major units:

1. We need to store the trim ID, if it exists.
2. We need to have specs. Basically, the specs are just going to be a table of data pulled from CRS, with caveats: They need to be able to edit it, hide it, or accept the original. 
3. Some of the specs go into specific spots on the motorcycle already, but, otherwise, they are just going into the special specs area.
4. We have a few images coming up from CRS. You have to be able to disable/exclude these and you have to be able to reorder them.

We're going to assume that some of this stuff will come in from lightspeed, but not worry too much. #1 goal is to be able to pair our regular add/edit to CRS, as well as to be able to specify a whole brand to pull in as active. 

Portal Change #1 - New Tables for "Product Lines"
-------------------------------------------------

Basically, we need to know the manufacturerproducttype to automatically populate in there. 

create table dealercrssubscription (
   dealercrssubscription_id int unsigned auto_increment primary key,
   created timestamp default current_timestamp,
   dealer_id int unsigned,
   foreign key (dealer_id) references dealer(dealer_id) on delete cascade,
   crs_make_id int unsigned,
   crs_machinetype_id int unsigned,
   is_current_only tinyint(1) default 0,
   current_year tinyint(1) default 1,
   next_year tinyint(1) default 1,
   previous_years tinyint(1) default 0
);

create table dealercrspopulation (
    dealercrspopulation_id int unsigned auto_increment primary key,
    dealersubscription_id int unsigned,
    foreign key (dealercrssubscription_id) references dealercrssubscription(dealercrssubscription_id) on delete cascade,
    created timestamp default current_timestamp,
    bikes_added int unsigned,
    bikes_removed int unsigned
);



Store DB Change #1 - Specification Table
----------------------------------------

So, I'm taking a very dim view of a specification. I don't really care what you stuff in there. Further, there's no magic. I'm just going to use one value, and I'm going to let you do an override...


create table motorcyclespec (
    motorcyclespec_id int unsigned auto_increment primary key,
    created timestamp default current_timestamp,
    version_number int unsigned,
    value text,
    feature_name varchar(128),
    attribute_name varchar(128),
    type varchar(32),
    external_package_id int unsigned,
    motorcycle_id int unsigned,
    foreign key (motorcycle_id) references motorcycle(motorcycle_id) on delete cascade,
    final_value text,
    override tinyint(1) default 1,
    source varchar(64) default "CRS",
    hidden tinyint(1) default 1
);

Store DB Change #2 - Motorcycle Images
--------------------------------------




Store DB Change #3 - Motorcycle Tracking
----------------------------------------

We will ignore lightspeed for just a moment and get some basic tracking in from CRS:

alter table motorcycle add column crs_imported tinyint(1) default 0;
alter table motorcycle add column crs_trim_id varchar(32) default "";
alter table motorcycle add column crs_version_number int unsigned;

We also need to track if we filled this thing in:

alter table motorcycle add column ext_dealercrssubscription_id int unsigned; 








