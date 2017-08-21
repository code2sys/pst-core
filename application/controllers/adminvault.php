<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 8/21/17
 * Time: 12:18 PM
 */

require_once("admin.php");

class Adminvault extends Admin
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->checkValidAccess('vault') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }

        if (!defined('ENABLE_VAULT') || !ENABLE_VAULT) {
            redirect("");
            exit();
        }
    }

    public function index() {
        $this->vault_images();
    }

    public function vault_images( $updated = null ) {
        if( $updated != null ) {
            $this->_mainData['success'] = TRUE;
        }

        if ($this->input->post()) {
            if( isset($_POST['update']) ) {
                $arr = array();
                $mid = null;
                foreach( $_POST['description'] as $k => $description ) {
                    $arr['description'] = $description;
                    $id = $k;
                }
                $this->admin_m->updateVaultImageDescription($id, $arr);
            }elseif(isset($_POST['orderSubmit'])){
                $arr = explode(",",$this->input->post('order'));
                foreach($arr as $k=>$v)
                {
                    $rr[] = explode("=",$v);
                }
                foreach($rr as $k=>$v){
                    $img = $v[0];
                    $ord = $v[1];
                    $this->admin_m->updateVaultImageOrder($img, $ord);
                }
                // echo "<pre>";
                // print_r($rr);
                // echo "</pre>";
                // exit;
            } else {
                $res['img'] = $this->admin_m->getVaultImages();
                $ord = end($res['img']);
                $prt = $ord['priority_number'];
                // echo "<pre>";
                // print_r($ord['priority_number']);
                // echo "</pre>";exit;
                foreach ($_FILES['file']['name'] as $key => $val) {
                    if($prt==""){
                        $prt = 0;
                    }else{
                        $prt = $prt + 1;
                    }
                    $arr = array();
                    $img = time().'_'.str_replace(' ','_',$val);
                    $dir = STORE_DIRECTORY.'/html/media/'.$img;
                    move_uploaded_file($_FILES["file"]["tmp_name"][$key], $dir);
                    $arr['description'] = $_POST['description'];
                    $arr['image_name'] = $img;
                    $arr['priority_number'] = $prt;
                    $this->admin_m->updateVaultImage($arr);
                    $prt++;
                }
            }
            redirect('admin/vault_images/updated');
        }

        $this->_mainData['image'] = $this->admin_m->getVaultImages();

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/vault/vault_images', $this->_mainData);
    }

    public function deleteVaultImage( $id = null ) {
        if( $id != null ) {
            $this->admin_m->deleteVaultImage($id);
        }
        redirect('adminvault/vault_images/');
    }

}