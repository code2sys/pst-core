<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class productUpdate extends Master_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('products_m');
    	$this->load->config('sitesettings');
    }
    
    public function extract($fileIndicator = '')
    {
        try 
        {
            $extractFileName = array();
            $extractFileName[0] = "dataextract/product-".date("Ymd").$fileIndicator.".sql";
            $fp = fopen($extractFileName[0],'wb');
            $productExtract = '';
            $data = $this->products_m->getProducts();
            foreach ($data AS $key => $val) {
                $productExtract = $this->packageData($val);
                fwrite($fp,$productExtract);
            }
            fclose($fp);
            
            $extractFileName[1] = "dataextract/category-".date("Ymd").$fileIndicator.".sql";
            $fp = fopen($extractFileName[1],'wb');
            $categoryExtract = '';
            $data = $this->products_m->getCategories();
            foreach ($data AS $key => $val) {
                $categoryExtract = $this->packageData($val);
                fwrite($fp,$categoryExtract);
            }
            fclose($fp);
            
            $extractFileName[2] = "dataextract/product_category-".date("Ymd").$fileIndicator.".sql";
            $fp = fopen($extractFileName[2],'wb');
            $productCategoryExtract = '';
            $data = $this->products_m->getProductCategories();
            foreach ($data AS $key => $val) {
                $productCategoryExtract = $this->packageData($val);
                fwrite($fp,$productCategoryExtract);
            }
            fclose($fp);
            
            if ($fileIndicator == '') {
                $url = 'http://cf/productUpdate/acceptUpdateFiles';
                foreach ($extractFileName AS $key => $val) {
                    $file_name_with_full_path = realpath($val);
                    $post = array('file_contents'=>'@'.$file_name_with_full_path);
                    $results = $this->callCurl($url, $post);
                    
                    echo 'RESULTS:'.$results;
                    echo '<br><br><br>';
                }
            } else {
                return true;
            }
            exit;
        } catch (Exception $e) {
        }
        
//        redirect('alert/failure/'.$results['code']);
        exit();
    }   
    
    
    public function acceptUpdateFiles()
    {
        $uploaddir = "dataextract/";
        $uploadfile = $uploaddir . basename($_FILES['file_contents']['name']);
        if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
            echo true;
        } else {
            echo false;
        }
        exit;
    }
    
    
    public function processUpdate()
    {
        $results = $this->extract('-backup');
        
        $dir = 'dataextract/';
        $files = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') {
                    } else {
                        if (stripos($file,'backup') == 0) {
                            $files[] = $file;
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        $x=0;
        $ts = date("U");
        foreach ($files AS $key => $val) {
            $tableName = '';
            $fileData  = '';
            $fileParts = explode('-',$val);
            if (count($fileParts) == 2) {
                $tableName = $fileParts[0];
                $fileData  = $fileParts[1];
                $execresult = $this->products_m->processSQLFile('DELETE FROM '.$tableName.' WHERE beinfo > 0');
                $fp = fopen('dataextract/'.$val,'rb');
                while(!feof($fp)) {
                    $sql = fgets($fp);
                    if (trim($sql) != '') {
                        $execresult = $this->products_m->processSQLFile($sql);
                    }
                }
                fclose($fp);
                $execresult = $this->products_m->processSQLFile('UPDATE '.$tableName." set beinfo='".$ts."' WHERE beinfo = 9");
            }
        }
        
        
        print_r($files);
        exit;
        
        
        
        
        
        
        
        
        
        
        echo 'results'.$results;
        exit;
        
    }
    
    
    public function packageData($val)
    {
        $productExtract = '';
        $productKeys = '';
        $productVals = '';
        foreach ($val AS $key2 => $val2) {
            $productKeys .= $key2.",";
            $productVals .= "'".$val2."',";
        }
        $productKeys = substr($productKeys,0,(strlen($productKeys)-1));
        $productVals = substr($productVals,0,(strlen($productVals)-1));
        $productExtract = "insert into products (".$productKeys.") values (".$productVals.");".PHP_EOL;
        return $productExtract;
    }
    
    public function callCurl($url, $post)
    {
        $results = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $results = curl_exec ($ch);
        curl_close ($ch);
        return $results;
    }
    
    
  
  
}