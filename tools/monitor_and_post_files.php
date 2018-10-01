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
            $output_file = $outgoing_directory . "/" . time() . "_" . $entry . ".output.txt";

            // Check that it is a CSV file
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ("text/plain" != finfo_file($finfo, $incoming_directory . "/" . $entry)) {
                file_put_contents($output_file, "Invalid file format received; please upload a CSV file.");
                continue;
            }

            // Move it to temp
            $target_file = tempnam($tmp_directory, "monitored_") . $entry;
            system("mv $incoming_directory/$entry $target_file");

            // Now, attempt to post and write to the output file
            // https://blog.derakkilgo.com/2009/06/07/send-a-file-via-post-with-curl-and-php/
            /* curl will accept an array here too.
             * Many examples I found showed a url-encoded string instead.
             * Take note that the 'key' in the array will be the key that shows up in the
             * $_FILES array of the accept script. and the at sign '@' is required before the
             * file name.
             */
            $file_name_with_full_path = realpath($incoming_directory . "/" . $entry);
            $post = array('upload'=>'@'.$file_name_with_full_path);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$post_url);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec ($ch);
            curl_close ($ch);
            file_put_contents($output_file, $result);

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



