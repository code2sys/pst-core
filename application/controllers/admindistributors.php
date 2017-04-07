<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/10/17
 * Time: 10:07 PM
 */

require_once("admin.php");

class Admindistributors extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Distributormodel");
        if(!$this->checkValidAccess('distributors') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }
    }

    public function index() {
        $this->_mainData["distributors"] = $this->Distributormodel->getIndex();
        $this->_mainData["success"] = array_key_exists("success_admin_distributors", $_SESSION) ? $_SESSION["success_admin_distributors"] : "";
        $_SESSION["success_admin_distributors"] = "";
        $this->_mainData["error"] = array_key_exists("error_admin_distributors", $_SESSION) ? $_SESSION["error_admin_distributors"] : "";
        $_SESSION["error_admin_distributors"] = "";
        $this->setNav('admin/nav_v', 1);
        $this->renderMasterPage('admin/master_v', 'admin/distributors_v', $this->_mainData);
    }

    public function update_distributor($distributor_id) {
        $distributor = $this->Distributormodel->get($distributor_id);
        $data = array();
        $fields = array("dealer_number", "username", "password", "account_number");
        if ($distributor["customer_distributor"] > 0) {
            $fields[] = "name";
        }
        foreach ($fields as $f) {
            $data[$f] = array_key_exists($f, $_REQUEST) ? $_REQUEST[$f] : "";
        }
        if ($distributor["customer_distributor"] > 0) {
            $distributor["name"] = $data["name"];
        }
        $this->Distributormodel->update($distributor_id, $data);
        $_SESSION["success_admin_distributors"] = "Updated " . $distributor["name"] . " successfully.";
        redirect("admindistributors/index");
    }

    public function remove_distributor($distributor_id) {
        $distributor = $this->Distributormodel->get($distributor_id);
        if ($distributor["customer_distributor"] > 0) {
            $this->Distributormodel->remove($distributor_id);
            $_SESSION["success_admin_distributors"] = "Removed " . $distributor["name"] . " successfully.";
        } else {
            $_SESSION["error_admin_distributors"] = "Sorry, that distributor cannot be removed.";
        }
        redirect("admindistributors/index");
    }

    public function add_distributor() {
        // We are just looking for a name, really...
        $name = trim(array_key_exists("name", $_REQUEST) ? $_REQUEST["name"] : "");

        if ($name == "") {
            $_SESSION["error_admin_distributors"] = "Sorry, no name received.";
        } else if (array_key_exists("distributor_id", $this->Distributormodel->fetchByName($name))) {
            $_SESSION["error_admin_distributors"] = "Sorry, there is already a distributor by that name.";
        } else {
            // Really add it!
            $this->Distributormodel->add(array("name" => $name));
            $_SESSION["success_admin_distributors"] = "Added " . $name . " successfully.";
        }
        redirect("admindistributors/index");
    }
}