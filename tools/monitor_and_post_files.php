<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/1/18
 * Time: 4:40 PM
 *
 * At the start, we can make this really cheaply. Basically, we need to have a couple inputs:


Incoming directory
Outgoing directory
Temp directory
URL to post this file to
Whether to delete the file from temp or not. (Let's not at the start).


So here's the algorithm for 2:


Incoming inputs
Validate the directories exist, or error
Create the output file
Validate that the file is a CSV file and it contains some number of headers; if any error, put out an appropriate message.
Move the file out of in and put it in /tmp
Post the file to the URL, and then read the output into our output file
 *
 *
 */

$incoming_directory = $outgoing_directory = $tmp_directory = $post_url = "";
$delete_file = false;

for ($i = 1; $i < count($argv); $i++) {
    switch($argv[$i]) {
        case "--incoming_directory":
            $i++;
            $incoming_directory = $argv[$i];
            break;
        case "--outgoing_directory":
            $i++;
            $outgoing_directory = $argv[$i];
            break;
        case "--tmp_directory ":
            $i++;
            $tmp_directory = $argv[$i];
            break;
        case "--post_url":
            $i++;
            $post_url = $argv[$i];
            break;
        case "--delete_file":
            $delete_file = true;
            break;
    }
}

if (!file_exists($incoming_directory) && is_dir($incoming_directory)) {
    print "Incoming directory not found: $incoming_directory \n";
    exit(-1);
}
if (!file_exists($outgoing_directory) && is_dir($outgoing_directory)) {
    print "Outgoing directory not found: $outgoing_directory \n";
    exit(-1);
}
if (!file_exists($tmp_directory) && is_dir($tmp_directory)) {
    print "Temp directory not found: $tmp_directory \n";
    exit(-1);
}
if ($post_url == "") {
    print "URL not defined.\n";
    exit(-1);
}

// OK, now it's time to poll the directory...
if ($in_dir = opendir($incoming_directory)) {
    // We have to iterate over the entries to this and extract files.
    while (false !== ($entry = readdir($in_dir))) {
        if ($entry != "." && $entry != "..") {
            // attempt to process the file..
            // generate the error output

            // Check that it is a CSV file

            // Move it to temp
            $target_file = tempnam($tmp_directory, "monitored_") . $entry;
            system("mv $incoming_directory/$entry $target_file");

            // Now, attempt to post and write to the output file

            // Remove the file, if the flag is set.
            if ($delete_file) {
                system("rm $target_file");
            }
        }
    }
} else {
    print "Could not open directory: $incoming_directory \n";
    exit(-2);
}



