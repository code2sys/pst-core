<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 1/6/19
 * Time: 8:07 PM
 */

class Ftpusers extends CI_Model {


    public function setUsernamePassword($username, $password, $tag = "Dealer Custom FTP", $store = STORE_NAME) {
        $username = trim($username);

        // Step #1: If the name is in a disallowed name, reject it...
        if (in_array($username, array("ftp")) || "." == substr($username, 0, 1)) {
            error_log("Request for a bad username: $username");
            return false; // bad username.
        }

        $vsftp_database = $this->load->db("vsftpd");

        // Step #2: Check the datbase for this store. If it already exists, and if the username is the same, it's not a change. It may just be a password update.
        $query = $vsftp_database->query("Select * from accounts where store = ? and tag = ?", array($store, $tag));
        $existing_account = null;

        foreach ($query->result_array() as $row) {
            $existing_account = $row;
        }

        $create_new_one = false;
        $remove_old_one = false;
        $old_one_name = "";

        if (!is_null($existing_account)) {
            if ($existing_account["username"] != $username) {
                $create_new_one = true;
                $old_one_name = $existing_account["username"];
                $remove_old_one = true;
            }

            // update it, regardless.
            $vsftp_database->query("Update accounts set username = ?, pass = password(?) where store = ? and tag = ?", array($username, $password, $store, $tag));
        } else {
            $create_new_one = true;
            $vsftp_database->query("Insert into accounts (username, pass, store, tag) values (?, password(?), ?, ?)", array($username, $password, $store, $tag));
        }


        // Step #3: If the username is changed, you have to update it and remove the old one
        if ($remove_old_one && $old_one_name != "") {
            system("ssh vsftp@ftp.powersporttechnologies.com rm -rf " . escapeshellarg("/home/vsftp/" . $old_one_name));
        }

        // Step #4: If the username is not present, you have to create it.
        if ($create_new_one) {
            system("ssh vsftp@ftp.powersporttechnologies.com mkdir " . escapeshellarg("/home/vsftp/" . $username));
        }


        return true; // success
    }

}