<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/22/17
 * Time: 11:41 AM
 */

require_once("admin.php");

class Adminproductuploader extends Admin
{

    protected $columns;

    protected $productupload;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Portalmodel");
        $this->load->model("Productuploadermodel");
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }

        $this->columns = $this->Productuploadermodel->getPossibleColumns();
    }

    /*
     * the purpose of this is to show the column list, the entry form, and to give them the ability to see their old ones and to resume them
     */
    public function index() {

        $this->_mainData["uploads"] = $this->Productuploadermodel->getList();
        $this->_mainData["errors"] = array_key_exists("adminproductupload_index_error", $_SESSION) ? $_SESSION["adminproductupload_index_error"] : "";
        $_SESSION["adminproductupload_index_error"] = "";
        $this->_mainData["columns"] = $this->columns;

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/productupload/index_v', $this->_mainData);
    }

    public function resume($productupload_id) {
        $data = $this->Productuploadermodel->get($productupload_id);

        switch($data["status"]) {
            case "Uploaded":
                header("Location: " . base_url("adminproductuploader/matchcolumns/$productupload_id"));
                break;

            case "Columns Assigned":
                header("Location: " . base_url("adminproductuploader/applyMapping/$productupload_id"));
                break;

            case "Mapped":
                header("Location: " . base_url("adminproductuploader/confirm/$productupload_id"));
                break;

            case "Approved":
                header("Location: " . base_url("adminproductuploader/process/$productupload_id"));
                break;

            default:
                $_SESSION["adminproductupload_index_error"] = "Invalid status to resume.";
                header("Location: " . base_url("adminproductuploader/index"));
        }

    }

    public function download($productupload_id, $mode = "all") {
        $this->_validateProductUploadID($productupload_id);
        $this->Productuploadermodel->download($productupload_id, $mode);
    }

    protected function _dieOnError($error) {
        $_SESSION["adminproductupload_index_error"] = $error;
        redirect("adminproductuploader/index");
        exit();
    }

    public function upload() {
        if (array_key_exists("upload", $_FILES) && $_FILES["upload"]["size"] > 0) {
            $productupload_id = $this->Productuploadermodel->add($_FILES["upload"]);

            if ($productupload_id > 0) {
                $_SESSION["adminproductupload_upload_success"] = "File upload received successfully; please match the columns.";
                redirect("adminproductuploader/matchcolumns/$productupload_id");
            } else {
                $this->_dieOnError("Please upload a CSV format file only.");
            }

        } else {
            $this->_dieOnError("Sorry, no CSV file detected.");
        }
    }

    protected function _validateProductUploadID($productupload_id) {
        $this->productupload = $this->Productuploadermodel->get($productupload_id);
        if (!is_array($this->productupload) || !array_key_exists("productupload_id", $this->productupload) || $this->productupload["productupload_id"] != $productupload_id) {
            $this->_dieOnError("Sorry, that is not a valid upload file.");
        }
    }

    public function matchcolumns($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        // We just need to show them header and first row...

        $this->_mainData["errors"] = array_key_exists("adminproductupload_matchcolumns_error", $_SESSION) ? $_SESSION["adminproductupload_matchcolumns_error"] : "";
        $_SESSION["adminproductupload_matchcolumns_error"] = "";
        $this->_mainData["success"] = array_key_exists("adminproductupload_upload_success", $_SESSION) ? $_SESSION["adminproductupload_upload_success"] : "";
        $_SESSION["adminproductupload_upload_success"] = "";

        $this->_mainData["header"] = $this->Productuploadermodel->getHeader($productupload_id);
        $this->_mainData["firstrow"] = $this->Productuploadermodel->getFirstRow($productupload_id);
        $this->_mainData["existing_mapping"] = $this->Productuploadermodel->getColumnMapping($productupload_id);
        $this->_mainData["productupload"] = $this->productupload;
        $this->_mainData["productupload_id"] = $productupload_id;
        $this->_mainData["columns"] = $this->columns;

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/productupload/matchcolumns_v', $this->_mainData);
    }

    public function save_matchcolumns($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        $error = $this->Productuploadermodel->saveColumnMapping($productupload_id);

        if ($error != "") {
            $_SESSION["adminproductupload_matchcolumns_error"] = $error;
            redirect("adminproductuploader/matchcolumns/$productupload_id");
        } else {
            // Things are good. Let's send them to the confirmation screen...
            $this->Productuploadermodel->clearMappingCount($productupload_id);
            redirect("adminproductuploader/applyMapping/$productupload_id");
        }
    }

    /*
     * This is a pause...It should reload itself...
     */
    public function applyMapping($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        $count = $this->Productuploadermodel->getUnmappedRowCount($productupload_id);

        if ($count > 0) {
            $this->Productuploadermodel->applyMapping($productupload_id);
            $this->_mainData["productupload_id"] = $productupload_id;
            $this->_mainData["productupload"] = $this->productupload;
            $this->_mainData["total_rows"] = $this->Productuploadermodel->getRowCount($productupload_id);
            $this->_mainData["mapped_rows"] = $this->Productuploadermodel->getMappedRowCount($productupload_id);
            $this->setNav('admin/nav_v', 2);
            $this->renderMasterPage('admin/master_v', 'admin/productupload/apply_mapping_v', $this->_mainData);
        } else {
            redirect("adminproductuploader/confirm/$productupload_id");
        }
    }

    public function confirm($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        $this->_mainData["new"] = $this->Productuploadermodel->getStatusCounts($productupload_id, "New");
        $this->_mainData["update"] = $this->Productuploadermodel->getStatusCounts($productupload_id, "Update");
        $this->_mainData["reject"] = $this->Productuploadermodel->getStatusCounts($productupload_id, "Reject");
        $this->_mainData["productupload_id"] = $productupload_id;
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/productupload/confirm_v', $this->_mainData);
    }

    public function save_confirm($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        $this->Productuploadermodel->confirm($productupload_id);
        redirect("adminproductuploader/process/$productupload_id");
    }

    public function process($productupload_id) {
        $this->_validateProductUploadID($productupload_id);
        $count = $this->Productuploadermodel->getUnprocessedRowCount($productupload_id);

        if ($count > 0) {
            $this->Productuploadermodel->process($productupload_id);
            $this->_mainData["productupload_id"] = $productupload_id;
            $this->setNav('admin/nav_v', 2);
            $this->renderMasterPage('admin/master_v', 'admin/productupload/process_v', $this->_mainData);
        } else {
            $_SESSION["jonathan_product_message"] = "Upload completed successfully.";
            redirect("adminproduct/product");
        }

    }
}