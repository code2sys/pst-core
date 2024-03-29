<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:28 AM
 */

require_once(__DIR__ . "/customeradmin.php");

abstract class Productsbrandsadmin extends Customeradmin {

    protected function validateCoupon() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('couponCode', 'Coupon Code', 'required|xss_clean');
        $this->form_validation->set_rules('startDate', 'Start Date', 'callback__validateDate[start]|xss_clean');
        $this->form_validation->set_rules('endDate', 'End Date', 'callback__validateDate[end]|xss_clean');
        $this->form_validation->set_rules('totalUses', 'Total Uses', 'integer|xss_clean');
        $this->form_validation->set_rules('type', 'Percentage or Set Value', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
        $this->form_validation->set_rules('associatedProductSKU', 'Associated Product SKU', 'xss_clean');
        $this->load->model('coupons_m');
        $specialConstraints = $this->coupons_m->getSpecialConstraints();
        if ($specialConstraints) {
            foreach ($specialConstraints as $opt) {
                $this->form_validation->set_rules($opt['ruleName'], $opt['displayName'], 'xss_clean');
            }
        }
        return $this->form_validation->run();
    }

    protected function validateEditCategory() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('category_id', 'Category Id', 'xss_clean');
        $this->form_validation->set_rules('parent_category_id', 'Parent Category', 'required|xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'xss_clean');
        $this->form_validation->set_rules('title', 'Title', 'xss_clean');
        $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
        $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
        $this->form_validation->set_rules('mark-up', 'Mark-up', 'integer|xss_clean');
        $this->form_validation->set_rules('notice', 'Notice', 'xss_clean');
        return $this->form_validation->run();
    }


    protected function validateEditBrand() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('brand_id', 'Brand Id', 'xss_clean');
//        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
        $this->form_validation->set_rules('exclude_market_place', 'exclude_market_place', 'xss_clean');
        $this->form_validation->set_rules('closeout_market_place', 'closeout_market_place', 'xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('slug', 'Brand Url', 'callback_username_check');
        $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
        $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
        $this->form_validation->set_rules('mark-up', 'Mark-up', 'is_natural|xss_clean');
        $this->form_validation->set_rules('map_percent', 'MAP Pricing', 'integer|xss_clean');
        return $this->form_validation->run();
    }


    public function username_check($str) {
        if ($this->admin_m->checkBrandSlug($str, $this->input->post('brand_id'))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('username_check', 'Brand Slug should be unique');
            return FALSE;
        }
    }


    /*     * ************************** PRODUCT ******************************* */

    public function product() {
        header("Location: " . base_url("adminproduct/product"));
    }

    /*     * ************************** END PRODUCT ******************************* */

    /*     * ************************** CATEGORY *********************************** */

    public function category() {
        if (!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $mainCategoryList = $this->admin_m->getCategories(FALSE);
        if ($mainCategoryList) {
            foreach ($mainCategoryList as $cat) {
                $this->_mainData['categories'][$cat['parent_category_id']][] = $cat;
            }
        }

        $this->_mainData['parent_categories'] = $this->admin_m->getCategories(TRUE);



        if ($this->validateEditCategory() !== FALSE && !empty($_POST)) { // Display Form
            $catArr = array();
            $categories = $this->_mainData['categories'];
            $postData = $this->input->post();

            $updateCategories = array();
            $updateCategories[0]['parent_category_id'] = $postData['parent_category_id'];
            $updateCategories[0]['category_id'] = $postData['category_id'];
            $updateCategories[0]['featured'] = $postData['featured'] == 1 ? 1 : 0;
            $updateCategories[0]['name'] = $postData['name'];
            $updateCategories[0]['title'] = $postData['title'];
            $updateCategories[0]['meta_tag'] = $postData['meta_tag'];
            $updateCategories[0]['keywords'] = $postData['keywords'];
            $updateCategories[0]['mark-up'] = $postData['mark-up'];
            $updateCategories[0]['google_category_num'] = $postData['google_category_num'];
            $updateCategories[0]['ebay_category_num'] = $postData['ebay_category_num'];
            $updateCategories[0]['notice'] = $postData['notice'];
            $catArr[$postData['category_id']] = $postData['category_id'];

            //!empty($postData['google_category_num']) &&
            if ($postData['category_id'] > 0 && array_key_exists($postData['category_id'], $categories)) {
                // JLB 07-02-17 This was written in the crappiest way possible. It assumed that there were only so many levels.
                // Why can't they make a loop?
                $parents = array($postData['category_id']);

                while (count($parents) > 0) {
                    $current = $parents;
                    $parents = array();

                    foreach ($current as $c_id) {
                        if (array_key_exists($c_id, $categories)) {
                            $subcats = $categories[$c_id];
                            foreach ($subcats as $subcat) {
                                $parents[] = $subcat["category_id"];
                                $updateCategories[] = array(
                                    "parent_category_id" => $subcat["parent_category_id"],
                                    "category_id" => $subcat["category_id"],
                                    "featured" => $subcat["featured"] == 1 ? 1 : 0,
                                    "active" => $subcat["active"],
                                    "name" => $subcat["name"],
                                    "title" => $subcat["title"],
                                    "meta_tag" => $subcat["meta_tag"],
                                    "keywords" => $subcat["keywords"],
                                    "mark-up" => $subcat["mark_up"], // JLB - this sort of thing is just annoying. Why would you do this?
                                    "google_category_num" => $subcat["google_category_num"],
                                    "ebay_category_num" => $subcat["ebay_category_num"],
                                    "notice" => $subcat["notice"]
                                );
                            }
                        }
                    }
                }

// JLB 07-02-17
// Please never write code like this. It looks like first semester, first year programming. Who told you things are only four deep?
//                foreach ($categories[$postData['category_id']] as $subCat) {
//
//                    $updateCategories[$counter]['parent_category_id'] = $subCat['parent_category_id'];
//                    $updateCategories[$counter]['category_id'] = $subCat['category_id'];
//                    $updateCategories[$counter]['featured'] = $subCat['featured'] == 1 ? 1 : 0;
//                    $updateCategories[$counter]['active'] = $subCat['active'];
//                    $updateCategories[$counter]['name'] = $subCat['name'];
//                    $updateCategories[$counter]['title'] = $subCat['title'];
//                    $updateCategories[$counter]['meta_tag'] = $subCat['meta_tag'];
//                    $updateCategories[$counter]['keywords'] = $subCat['keywords'];
//                    $updateCategories[$counter]['mark-up'] = $subCat['mark_up'];
//                    $updateCategories[$counter]['google_category_num'] = $subCat['google_category_num'];
//                    $updateCategories[$counter]['ebay_category_num'] = $subCat['ebay_category_num'];
//                    $updateCategories[$counter]['notice'] = $subCat['notice'];
//                    $catArr[$subCat['category_id']] = $subCat['category_id'];
//
//                    if (@$categories[$subCat['category_id']]) {
//                        foreach ($categories[$subCat['category_id']] as $subsubCat) {
//
//                            $secondCounter = count($updateCategories);
//                            $updateCategories[$secondCounter]['parent_category_id'] = $subsubCat['parent_category_id'];
//                            $updateCategories[$secondCounter]['category_id'] = $subsubCat['category_id'];
//                            $updateCategories[$secondCounter]['featured'] = $subsubCat['featured'] == 1 ? 1 : 0;
//                            $updateCategories[$secondCounter]['active'] = $subsubCat['active'];
//                            $updateCategories[$secondCounter]['name'] = $subsubCat['name'];
//                            $updateCategories[$secondCounter]['title'] = $subsubCat['title'];
//                            $updateCategories[$secondCounter]['meta_tag'] = $subsubCat['meta_tag'];
//                            $updateCategories[$secondCounter]['keywords'] = $subsubCat['keywords'];
//                            $updateCategories[$secondCounter]['mark-up'] = $subsubCat['mark_up'];
//                            $updateCategories[$secondCounter]['google_category_num'] = $subsubCat['google_category_num'];
//                            $updateCategories[$secondCounter]['ebay_category_num'] = $subsubCat['ebay_category_num'];
//                            $updateCategories[$secondCounter]['notice'] = $subsubCat['notice'];
//                            $catArr[$subsubCat['category_id']] = $subsubCat['category_id'];
//
//                            if (@$categories[$subsubCat['category_id']]) {
//                                foreach ($categories[$subsubCat['category_id']] as $subsubsubCat) {
//
//                                    $thirdCounter = count($updateCategories);
//                                    $updateCategories[$thirdCounter]['parent_category_id'] = $subsubsubCat['parent_category_id'];
//                                    $updateCategories[$thirdCounter]['category_id'] = $subsubsubCat['category_id'];
//                                    $updateCategories[$thirdCounter]['featured'] = $subsubsubCat['featured'] == 1 ? 1 : 0;
//                                    $updateCategories[$thirdCounter]['active'] = $subsubsubCat['active'];
//                                    $updateCategories[$thirdCounter]['name'] = $subsubsubCat['name'];
//                                    $updateCategories[$thirdCounter]['title'] = $subsubsubCat['title'];
//                                    $updateCategories[$thirdCounter]['meta_tag'] = $subsubsubCat['meta_tag'];
//                                    $updateCategories[$thirdCounter]['keywords'] = $subsubsubCat['keywords'];
//                                    $updateCategories[$thirdCounter]['mark-up'] = $subsubsubCat['mark_up'];
//                                    $updateCategories[$thirdCounter]['google_category_num'] = $subsubsubCat['google_category_num'];
//                                    $updateCategories[$thirdCounter]['ebay_category_num'] = $subsubsubCat['ebay_category_num'];
//                                    $updateCategories[$thirdCounter]['notice'] = $subsubsubCat['notice'];
//                                    $catArr[$subsubsubCat['category_id']] = $subsubsubCat['category_id'];
//                                }
//                            }
//                        }
//                    }
//
//                    $counter++;
//                }
            }

//            echo "<pre>";
//            print_r($catArr);
//            print_r($updateCategories);
//            echo "</pre>";
//             exit;
            foreach ($updateCategories as $category) {
                $this->admin_m->updateCategory($category);
            }

            redirect('admin/category');
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_v', $this->_mainData);
    }

    public function category_edit($id = NULL) {
        if (!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/category');
        } else {
            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
        }

        $this->_mainData['parent_categories'] = $this->admin_m->getCategories(TRUE);



        if ($this->validateEditCategory() !== FALSE && !empty($_POST)) { // Display Form
            $catArr = array();
            $categories = $this->_mainData['categories'];
            $postData = $this->input->post();

            $updateCategories = array();
            $updateCategories[0]['parent_category_id'] = $postData['parent_category_id'];
            $updateCategories[0]['category_id'] = $postData['category_id'];
            $updateCategories[0]['featured'] = $postData['featured'] == 1 ? 1 : 0;
            $updateCategories[0]['name'] = $postData['name'];
            $updateCategories[0]['title'] = $postData['title'];
            $updateCategories[0]['meta_tag'] = $postData['meta_tag'];
            $updateCategories[0]['keywords'] = $postData['keywords'];
            $updateCategories[0]['mark-up'] = $postData['mark-up'];
            $updateCategories[0]['google_category_num'] = $postData['google_category_num'];
            $updateCategories[0]['ebay_category_num'] = $postData['ebay_category_num'];
            $updateCategories[0]['notice'] = $postData['notice'];
            $catArr[$postData['category_id']] = $postData['category_id'];

            $counter = 1;
            //!empty($postData['google_category_num']) &&
            if (@$categories[$postData['category_id']]) {
                foreach ($categories[$postData['category_id']] as $subCat) {

                    $updateCategories[$counter]['parent_category_id'] = $subCat['parent_category_id'];
                    $updateCategories[$counter]['category_id'] = $subCat['category_id'];
                    $updateCategories[$counter]['featured'] = $subCat['featured'] == 1 ? 1 : 0;
                    $updateCategories[$counter]['name'] = $subCat['name'];
                    $updateCategories[$counter]['title'] = $subCat['title'];
                    $updateCategories[$counter]['meta_tag'] = $subCat['meta_tag'];
                    $updateCategories[$counter]['keywords'] = $subCat['keywords'];
                    $updateCategories[$counter]['mark-up'] = $subCat['mark_up'];
                    $updateCategories[$counter]['google_category_num'] = $subCat['google_category_num'];
                    $updateCategories[$counter]['ebay_category_num'] = $subCat['ebay_category_num'];
                    $updateCategories[$counter]['notice'] = $subCat['notice'];
                    $catArr[$subCat['category_id']] = $subCat['category_id'];

                    if (@$categories[$subCat['category_id']]) {
                        foreach ($categories[$subCat['category_id']] as $subsubCat) {

                            $secondCounter = count($updateCategories);
                            $updateCategories[$secondCounter]['parent_category_id'] = $subsubCat['parent_category_id'];
                            $updateCategories[$secondCounter]['category_id'] = $subsubCat['category_id'];
                            $updateCategories[$secondCounter]['featured'] = $subsubCat['featured'] == 1 ? 1 : 0;
                            $updateCategories[$secondCounter]['name'] = $subsubCat['name'];
                            $updateCategories[$secondCounter]['title'] = $subsubCat['title'];
                            $updateCategories[$secondCounter]['meta_tag'] = $subsubCat['meta_tag'];
                            $updateCategories[$secondCounter]['keywords'] = $subsubCat['keywords'];
                            $updateCategories[$secondCounter]['mark-up'] = $subsubCat['mark_up'];
                            $updateCategories[$secondCounter]['google_category_num'] = $subsubCat['google_category_num'];
                            $updateCategories[$secondCounter]['ebay_category_num'] = $subsubCat['ebay_category_num'];
                            $updateCategories[$secondCounter]['notice'] = $subsubCat['notice'];
                            $catArr[$subsubCat['category_id']] = $subsubCat['category_id'];

                            if (@$categories[$subsubCat['category_id']]) {
                                foreach ($categories[$subsubCat['category_id']] as $subsubsubCat) {

                                    $thirdCounter = count($updateCategories);
                                    $updateCategories[$thirdCounter]['parent_category_id'] = $subsubsubCat['parent_category_id'];
                                    $updateCategories[$thirdCounter]['category_id'] = $subsubsubCat['category_id'];
                                    $updateCategories[$thirdCounter]['featured'] = $subsubsubCat['featured'] == 1 ? 1 : 0;
                                    $updateCategories[$thirdCounter]['name'] = $subsubsubCat['name'];
                                    $updateCategories[$thirdCounter]['title'] = $subsubsubCat['title'];
                                    $updateCategories[$thirdCounter]['meta_tag'] = $subsubsubCat['meta_tag'];
                                    $updateCategories[$thirdCounter]['keywords'] = $subsubsubCat['keywords'];
                                    $updateCategories[$thirdCounter]['mark-up'] = $subsubsubCat['mark_up'];
                                    $updateCategories[$thirdCounter]['google_category_num'] = $subsubsubCat['google_category_num'];
                                    $updateCategories[$thirdCounter]['ebay_category_num'] = $subsubsubCat['ebay_category_num'];
                                    $updateCategories[$thirdCounter]['notice'] = $subsubsubCat['notice'];
                                    $catArr[$subsubsubCat['category_id']] = $subsubsubCat['category_id'];
                                }
                            }
                        }
                    }

                    $counter++;
                }
            }

//             echo "<pre>";
//             print_r($catArr);
//             print_r($updateCategories);
//             echo "</pre>";
//             exit;
            foreach ($updateCategories as $category) {
                $this->admin_m->updateCategory($category);
            }
            redirect('admin/category');
        }
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_edit_v', $this->_mainData);
    }



    public function category_image($id = NULL) {
        if(!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/category');
        } else {

            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
        }

        if (@$_FILES['image']['name']) {
            $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
            $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $categoryData['name'])));
            $this->load->model('file_handling_m');
            $data = $this->file_handling_m->add_new_file_category('image', $config);
            if (@$data['error'])
                $this->_mainData['errors'] = $data['the_errors'];
            else {
                $categoryData['image'] = $data['file_name'];
                $this->admin_m->updateCategoryImage($categoryData);
            }

            // just get it again
            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_images_v', $this->_mainData);
    }

    public function category_video($id = NULL) {
        if (!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/category');
        } else {
            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
            $categoryVideo = $this->admin_m->getCategoryVideos($id);
            $this->_mainData['category_video'] = $categoryVideo;
        }

        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('video_url') as $k => $v) {
                if ($v != '') {
                    $url = $v;
                    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                    //$my_array_of_vars['v'];
                    $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'category_id' => $this->input->post('category_id'), 'title' => $this->input->post('title')[$k]);
                }
            }
            $this->admin_m->updateCategoryVideos($this->input->post('category_id'), $arr);
            redirect('admin/category_video/' . $this->input->post('category_id'));
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_videos_v', $this->_mainData);
    }

    public function category_delete($id) {
        if (is_numeric($id)) {
            $this->admin_m->deleteCategory($id);
        }
        redirect('admin/category');
    }

    public function load_category_rec($id) {
        if (is_numeric($id)) {
            $record = $this->admin_m->getCategory($id);
            if (is_null($record['title'])) {
                $record['title'] = str_replace(' > ', ', ', $record['long_name']);
                //$record['title'] = $record['long_name'];
            }
            echo json_encode($record);
        }
        exit();
    }

    /*     * ******************************* END CATEGORY ************************************* */


    /*     * ******************************** BRAND ********************************************** */

    public function brand() {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->helper('async');
        if ($this->validateEditBrand() !== FALSE) { // Display Form
            $this->admin_m->updateBrand($this->input->post());
            //redirect('admin/brand?update=1');
        }
        $this->_mainData['brands'] = $this->admin_m->getBrands(FALSE);
        $this->_mainData['parent_brands'] = $this->admin_m->getBrands(TRUE);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_v', $this->_mainData);
//        curl_request_async();
    }

    public function brand_image($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {

            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['id'] = $id;
        }

        if (@$_FILES['image']['name']) {
            $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
            $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $brandData['name'])));
            $this->load->model('file_handling_m');
            $data = $this->file_handling_m->add_new_file_brand('image', $config);
            if (@$data['error'])
                $this->_mainData['errors'] = $data['the_errors'];
            else {
                $brandData['image'] = $data['file_name'];
                $this->admin_m->updateBrand($brandData);
            }

            // just get it again
            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['id'] = $id;
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_images_v', $this->_mainData);
    }

    public function brand_video($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {
            $brandData = $this->admin_m->getBrand($id);
            $brandVideo = $this->admin_m->getBrandVideos($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['brand_video'] = $brandVideo;
            $this->_mainData['id'] = $id;
        }

        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('video_url') as $k => $v) {
                if ($v != '') {
                    $url = $v;
                    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                    //$my_array_of_vars['v'];
                    $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'brand_id' => $this->input->post('brand_id'), 'title' => $this->input->post('title')[$k]);
                }
            }
            $this->admin_m->updateBrandVideos($this->input->post('brand_id'), $arr);
            redirect('admin/brand_video/' . $this->input->post('brand_id'));
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_videos_v', $this->_mainData);
    }

    public function brand_sizechart($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {
            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            //$this->_mainData['categories'] = $this->admin_m->getCategories();
            $this->load->model('parts_m');
            $this->_mainData['age_gender'] = $this->parts_m->age_gender();
            $listParameters = array('brand' => $brandData['brand_id']);
            $this->_mainData['categories'] = $this->parts_m->newGetSearchCategoriesBrand($brandData['brand_id']);
            $this->_mainData['sizechart'] = $this->admin_m->getSizeChart($id);
            $this->_mainData['id'] = $id;
        }

        if ($this->input->post()) {
            if (@$_FILES['image']['name']) {
                $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
                $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $_FILES['image']['name'])));
                $this->load->model('file_handling_m');
                $data = $this->file_handling_m->add_new_file_brandSizeChart('image', $config);
                if (@$data['error'])
                    $this->_mainData['errors'] = $data['the_errors'];
                else {
                    $image = $data['file_name'];
                }
            }

            if ($this->input->post('savebrand')) {
                $brandArr = array('sizechart_url' => $this->input->post('size_url'));
                $brandArr['size_chart_status'] = $this->input->post('active') == 1 ? 1 : 0;
                $this->admin_m->updateBrandSizeChart($id, $brandArr);
            }
            // if ($this->input->post('save')) {
            // $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'image' => $image, 'categories' => implode(',', $this->input->post('categories')), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
            // if( $this->input->post('partquestion_id') != '' ) {
            // $arr['partquestion_id'] = $this->input->post('partquestion_id');
            // }
            // $this->admin_m->insertSizeChart($arr);
            // }
            // if ($this->input->post('update')) {
            // $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'image' => $image, 'categories' => implode(',', $this->input->post('categories')), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
            // if( $this->input->post('partquestion_id') != '' ) {
            // $arr['partquestion_id'] = $this->input->post('partquestion_id');
            // }
            // $this->admin_m->updateSizeChart($this->input->post('id'), $arr);
            // }
            if ($this->input->post('save')) {
                $cat = $this->input->post('categories');
                $ctrgs = $this->admin_m->getAllRelatedCategories($cat);
                $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'categories' => implode(',', $ctrgs), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
                if (@$_FILES['image']['name']) {
                    $arr['image'] = $image;
                }
                if ($this->input->post('partquestion_id') != '') {
                    $arr['partquestion_id'] = implode(',', $this->input->post('partquestion_id'));
                }
                $this->admin_m->insertSizeChart($arr);
            }
            if ($this->input->post('update')) {
                $cat = $this->input->post('categories');
                $ctrgs = $this->admin_m->getAllRelatedCategories($cat);
                $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'categories' => implode(',', $ctrgs), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
                if (@$_FILES['image']['name']) {
                    $arr['image'] = $image;
                }
                if ($this->input->post('partquestion_id') != '') {
                    $arr['partquestion_id'] = implode(',', $this->input->post('partquestion_id'));
                }
                $this->admin_m->updateSizeChart($this->input->post('id'), $arr);
            }
            if ($this->input->post('delete')) {
                $this->admin_m->deleteSizeChart($this->input->post('id'));
            }
            //$this->admin_m->updateBrandVideos($this->input->post('brand_id'), $arr);
            redirect('admin/brand_sizechart/' . $id);
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_sizechart_v', $this->_mainData);
    }

    public function brand_delete($id) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->admin_m->deleteBrand($id);
        }
        redirect('admin/brand');
    }

    public function load_brand_rec($id) {
        if (is_numeric($id)) {
            $record = $this->admin_m->getBrand($id);
            echo json_encode($record);
        }
        exit();
    }

    /*     * ********************************** END BRAND ******************************** */


    /*     * ************************************************ PRODUCT RECEIVING **************************************** */

    public function product_receiving() {
        if (!$this->checkValidAccess('product_receiving') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['notfound'] = array();
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('partnumber') as $k => $v) {
                if ($v != '') {
                    $arr1 = array('partnumber' => $v, 'distributor_id' => $this->input->post('distributor_id'), 'quantity' => $this->input->post('quantity')[$k] );
                    if( $this->input->post('cost')[$k] > 0 ) {
                        $arr1['cost'] = $this->input->post('cost')[$k];
                    }
                    $arr[] = $arr1;
                }
            }
            if (!empty($arr)) {
                $err = $this->admin_m->updateDistributorInventory($arr);
                //$err = $this->admin_m->updateDistributorInventory( $arr );
                if (empty($err)) {
                    redirect('admin/product_receiving');
                } else {
                    $this->_maindata['lightspeed'] = $err['lightspeed'];
                    $this->_mainData['notfound'] = $err['error'];
                    $this->_mainData['found'] = $err['success'];
                }
            }
        }

        if (count($this->_maindata['lightspeed']) > 0) {
            if (!array_key_exists("errors", $this->_mainData)) {
                $this->_mainData["errors"] = array();
            }
            $this->_mainData["errors"][] = "Sorry, " . (count($this->_maindata['lightspeed']) > 1 ? "these parts are" : "this part is") . " supplied by your Lightspeed Feed integration. Please update your inventory in lightspeed and it will automatically sync to your site within 24 hours. <ul> <li>" . implode("</li><li>", $this->_maindata['lightspeed']) . "</li></ul>";
        }
        if (!empty($this->_mainData['notfound'])) {
            if (!array_key_exists("errors", $this->_mainData)) {
                $this->_mainData["errors"] = array();
            }
            $this->_mainData['errors'][] = 'These part numbers were not found in the database:';
        }

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['distributors'] = $this->admin_m->getDistributorForProductReceiving();
        $this->renderMasterPage('admin/master_v', 'admin/product_receiving', $this->_mainData);
        //echo 'Product Receiving';
    }

    /*     * *********************************************** END PRODUCT RECEIVING ******************************************** */

    /*     * ************************************************ Closeout Repring Rule **************************************** */

    public function closeout_rules() {
        // if( $this->input->post() ) {
        // $arr = array();
        // foreach( $this->input->post('days') as $k => $v ) {
        // $mark_up = $this->input->post('mark_up')[$k] == 1 ? 1 : 0;
        // $arr[] = array('days' => $v, 'percentage' => $this->input->post('percentage')[$k], 'status' => 1, 'mark_up' => $mark_up, 'id' => $k );
        // }
        // if( !empty( $arr ) ) {
        // $this->admin_m->updateCloseoutRules( $arr );
        // redirect('admin/closeout_rules');
        // }
        // }
        // $this->setNav('admin/nav_v', 2);
        // $this->_mainData['closeout_rules'] = $this->admin_m->getAllCloseoutRepringRule();
        // $this->renderMasterPage('admin/master_v', 'admin/closeout_rule', $this->_mainData);
    }

    /*     * *********************************************** END Closeout Repring Rule ******************************************** */

    /*     * ************************************************ Brand Closeout Repring Rule **************************************** */

    public function brand_rule($brand_id = null) {
        if ($brand_id == null) {
            redirect('admin/brand');
        }
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('days') as $k => $v) {
                $status = 1;
                $mark_up = $this->input->post('mark_up')[$k] == 1 ? 1 : 0;
                $arr[] = array('days' => $v, 'percentage' => $this->input->post('percentage')[$k], 'status' => $status, 'id' => $k, 'brand_id' => $brand_id, 'mark_up' => $mark_up );
            }
            if (!empty($arr)) {
                $this->admin_m->updateCloseoutRules($arr);
                redirect('admin/brand_rule/' . $brand_id);
            }
        }

        $brandData = $this->admin_m->getBrand($brand_id);
        $this->_mainData['brands'] = array($brandData);
        $this->_mainData['id'] = $brand_id;

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['closeout_rules'] = $this->admin_m->getAllCloseoutRepringRule($brand_id);
        $this->renderMasterPage('admin/master_v', 'admin/brand/closeout_rule', $this->_mainData);
    }

    /*     * *********************************************** END Brand Closeout Repring Rule ******************************************** */

    public function deleteRule() {
        if ($this->input->post()) {
            $deletedRule = $this->admin_m->deleteCloseoutRepringRule($this->input->post('id'));
            echo 1;
        } else {
            echo 0;
        }
    }

    public function test_closeout() {
        $this->load->model('parts_m');
        $this->parts_m->closeoutReprisingSchedule();
    }



    /*     * ************************************************ COUPON **************************************** */

    public function coupon() {
        if (!$this->checkValidAccess('coupons') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('coupons_m');

        if ($this->validateCoupon() === TRUE) {
            $success = $this->coupons_m->createCoupon($this->input->post());
        }

        $this->_mainData['specialConstraintsDD'] = $this->coupons_m->getSpecialConstraintsDD();
        $this->_mainData['specialConstraints'] = $this->coupons_m->getSpecialConstraints();
        $this->_mainData['brands_list'] = $this->admin_m->getBrands(TRUE);

        $this->_mainData['coupons'] = $this->coupons_m->getCoupons();
        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v', 'admin/coupon_v', $this->_mainData);
    }

    public function coupon_delete($id) {
        if (!$this->checkValidAccess('coupons') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->load->model('coupons_m');
            $record = $this->coupons_m->deleteCoupon($id);
        }
        redirect('admin/coupon');
    }

    public function load_coupon($id) {
        if (is_numeric($id)) {
            $this->load->model('coupons_m');
            $record = $this->coupons_m->getCouponById($id);
            echo json_encode($record);
        }
    }

    /*     * *********************************************** END COUPON ******************************************** */


    public function removeZeroInventory() {
        $this->load->model('parts_m');
        $this->parts_m->removeFinishedInventory();
    }


    /*
     * JLB 01-23-18
     * These are for lightspeed.
     */

    public function products_lightspeedpart() {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/lightspeedpart_v', $this->_mainData);
    }



    public function products_lightspeed_suppliercodes_ajax()
    {
        if (!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $columns = array(
            "lightspeed_suppliercode.supplier_code",
            "lightspeed_suppliercode.type",
            "distributor.name",
            "brand.name"
        );

        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by lightspeed_suppliercode.supplier_code asc ";

        if (array_key_exists("order", $_REQUEST) && is_array($_REQUEST["order"]) && count($_REQUEST["order"]) > 0) {
            // OK, there's a separate order string...
            $order_string = "order by ";
            $orderings = $_REQUEST["order"];
            if (count($orderings) == 0) {
                $order_string .= " lightspeed_suppliercode.supplier_code asc";
            } else {
                for ($i = 0; $i < count($orderings); $i++) {
                    if ($i > 0) {
                        $order_string .= ", ";
                    }

                    $field = $columns[$orderings[$i]["column"]];
                    $order_string .=  $field . " " . $orderings[$i]["dir"];
                }
            }
        }

        // How do we shove through the restrictor from the upper right?

        $this->load->helper("jonathan");

        $where = jonathan_generate_likes($columns, $s = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : ""), "WHERE");

        // get total count
        $query = $this->db->query("Select count(*) as cnt from lightspeed_suppliercode  left join brand using (brand_id) left join distributor using (distributor_id) ");
        $total_count = 0;
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        $query = $this->db->query("Select count(*) as cnt from lightspeed_suppliercode  left join brand using (brand_id) left join distributor using (distributor_id)  $where");
        $filtered_count = 0;
        foreach ($query->result_array() as $row) {
            $filtered_count = $row['cnt'];
        }

        $query = $this->db->query("Select lightspeed_suppliercode.*, brand.name as brand_name, distributor.name as distributor_name from lightspeed_suppliercode left join brand using (brand_id) left join distributor using (distributor_id) $where $order_string limit $length offset $start  ");
        $rows = $query->result_array();

        // Now, order them...
        $output_rows = array();
        foreach ($rows as $p) {
            $output_rows[] = array(
                $p["supplier_code"], $p['type'], $p["distributor_name"], $p["brand_name"],
                "<span class='nowrap'><a href='/admin/products_lightspeed_suppliercodes_edit/" . $p["lightspeed_suppliercode_id"] . "' class='view-button' data-lightspeed-suppliercode-id='" . $row["lightspeed_suppliercode_id"] . "'><i class='fa fa-search'></i>&nbsp;Edit</a></span><br/> "
            );
        }

        print json_encode(array(
            "data" => $output_rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => $s
        ));
    }

    // get it and stuff it into a view
    public function products_lightspeed_suppliercodes_edit($lightspeed_suppliercode_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $this->load->model("Lightspeedsuppliercode_m");

        $query = $this->db->query("Select * From lightspeed_suppliercode where lightspeed_suppliercode_id = ?", array($lightspeed_suppliercode_id));
        $row = $query->result_array();

        if (count($row) > 0) {
            $row = $row[0];
            $this->setNav('admin/nav_v', 2);
            $this->_mainData["row"] = $row;
            $this->_mainData["brands"] = $this->Lightspeedsuppliercode_m->getBrands();
            $this->_mainData["distributors"] = $this->Lightspeedsuppliercode_m->getDistributors();

            // get the distributors

            // get the brands...
            $this->renderMasterPage('admin/master_v', 'admin/product/lightspeed_suppliercodes_edit_v', $this->_mainData);
        } else {
            // redirect on error...
            $this->session->set_flashdata("error", "Sorry, supplier code not found.");

            header("Location: /admin/products_lightspeed_suppliercodes");
        }
    }

    // check that things are valid
    public function products_lightspeed_suppliercodes_save($lightspeed_suppliercode_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $type = $_REQUEST["type"];
        $distributor_id = $_REQUEST["distributor_id"];
        $brand_id = $_REQUEST["brand_id"];

        if ($distributor_id == 0) {
            $distributor_id = null;
        }

        if ($brand_id == 0) {
            $brand_id = null;
        }


        switch ($type) {
            case "Distributor":
                if (is_null($distributor_id)) {
                    $type = "Unmatched";
                }
                $brand_id = null;
                break;

            case "Brand":
                if (is_null($brand_id)) {
                    $type = "Unmatched";
                }
                $distributor_id = null;
                break;

            case "Unmatched":
                $distributor_id = null;
                $brand_id = null;
                break;
        }


        $this->db->query("Update lightspeed_suppliercode set type = ?, distributor_id = ?, brand_id = ? where lightspeed_suppliercode_id = ? limit 1", array($type, $distributor_id, $brand_id, $lightspeed_suppliercode_id));

        // now, set some sort of success flag...
        $this->session->set_flashdata("success", "Supplier code updated successfully.");

        // redirect..
        header("Location: /admin/products_lightspeed_suppliercodes");
    }

    public function products_lightspeed_suppliercodes() {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        // We need some settings - e.g., we have to have a lightspeed login set, lightspeed has to be enabled, and then we need a little control for the mode when lightspeed parts come in as active or inactive by default....

        global $PSTAPI;
        initializePSTAPI();
        $this->_mainData['lightspeed_feed_username'] = $PSTAPI->config()->getKeyValue('lightspeed_feed_username', '');
        $this->_mainData['lightspeed_feed_password'] = $PSTAPI->config()->getKeyValue('lightspeed_feed_password', '');

        $this->load->model("Lightspeedsuppliercode_m");
        $this->Lightspeedsuppliercode_m->registerMissingSuppliers();
        $this->_mainData['supplier_code_list'] = $this->Lightspeedsuppliercode_m->getAll();
        $this->_mainData["brands"] = $this->Lightspeedsuppliercode_m->getBrands();
        $this->_mainData["distributors"] = $this->Lightspeedsuppliercode_m->getDistributors();
        $this->_mainData["success"] = $this->session->flashdata("success");
        $this->_mainData["error"] = $this->session->flashdata("error");

        // JLB 12-04-18
        // We have to add a dealership ID filter

        $this->_mainData['lightspeed_new_unit_dealership_list'] = $PSTAPI->config()->getKeyValue('lightspeed_new_unit_dealership_list', '');
        $this->_mainData['lightspeed_used_unit_dealership_list'] = $PSTAPI->config()->getKeyValue('lightspeed_used_unit_dealership_list', '');

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/products_lightspeed_suppliercodes_v', $this->_mainData);
    }

    public function save_products_lightspeed_settings() {
        if (!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        // There is exactly one setting right now...
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->setActiveOnAdd($_REQUEST["lightspeed_active_load"]);
        $this->Lightspeed_m->setDestinationOnAdd($_REQUEST["lightspeed_default_destination_charge"]);
        $this->Lightspeed_m->setUnitCycleTraderDefault($_REQUEST["unitCycleTraderDefault"]);
        $this->Lightspeed_m->setLightSpeedPartPricingRule($_REQUEST["lightSpeedPartPricingRule"]);
        global $PSTAPI;
        initializePSTAPI();


        // JLB 04-19-18
        // We have new settings for the lead generation transfer to CDK
        // Observe that we are transitioning styles in the code to the new object library...
        // JLB 12-04-18
        // Adding in the lightspeed filters, too.
        $cdk_lead_transfer_keys = array("forward_leads_to_cdk", "vsept_dealership_id", "lightspeed_new_unit_dealership_list", "lightspeed_used_unit_dealership_list");
        foreach ($cdk_lead_transfer_keys as $k)  {
            $$k = $val = $_REQUEST[$k];
            $PSTAPI->config()->setKeyValue($k, $val);
        }

        $this->admin_m->updateAdminShippingProfile($this->input->post());

        $this->session->Set_flashdata("success", "Settings updated successfully.");

        // Redirect it...
        header("Location: /admin/products_lightspeed_suppliercodes");
    }

    public function save_products_lightspeed_suppliercodes()
    {
        if (!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $this->load->model("Lightspeedsuppliercode_m");
        $current_codes = $this->Lightspeedsuppliercode_m->getAll();

        // Just loop and update them...
        foreach ($current_codes as $c) {
            $type = $_REQUEST["type_" . $c["lightspeed_suppliercode_id"]];
            $brand_id = $_REQUEST["brand_id_" . $c["lightspeed_suppliercode_id"]];
            $distributor_id = $_REQUEST["distributor_id_" . $c["lightspeed_suppliercode_id"]];

            if ($brand_id == 0) {
                $brand_id = null;
            }
            if ($distributor_id == 0) {
                $distributor_id = null;
            }

            if (is_null($brand_id) && is_null($distributor_id)) {
                $type = "Unmatched";
            } else if ($type == "Brand" && is_null($brand_id)) {
                $type = "Unmatched";
            } else if ($type == "Distributor" && is_null($distributor_id)) {
                $type = "Unmatched";
            }

            $this->db->query("Update lightspeed_suppliercode set type = ?, brand_id = ?, distributor_id = ? where lightspeed_suppliercode_id = ? limit 1", array($type, $brand_id, $distributor_id, $c["lightspeed_suppliercode_id"]));


        }

        $this->session->set_flashdata("success", "Supplier codes updated successfully.");

        // Redirect it...
        header("Location: /admin/products_lightspeed_suppliercodes");

    }


    /*
     *                     <th><b>Part Number</b></th>
                    <th><b>Supplier Code</b></th>
                    <th><b>Description</b></th>
                    <th><b># Available</b></th>
                    <th><b>Price</b></th>
                    <th><b>Cost</b></th>
                    <th><b>Last Seen</b></th>
                    <th><b>Store Product Match</b></th>
                    <th><b>Distributor Part Match</b></th>
     */

    public function products_lightspeedpart_ajax() {
        $columns = array(
            "part_number",
            "supplier_code",
            "description",
            "available",
            "current_active_price",
            "cost",
            "lightspeed_last_seen",
            "partvariation_id",
            "eternalpartvariation_id"
        );

        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by part_number asc ";

        if (array_key_exists("order", $_REQUEST) && is_array($_REQUEST["order"]) && count($_REQUEST["order"]) > 0) {
            // OK, there's a separate order string...
            $order_string = "order by ";
            $orderings = $_REQUEST["order"];
            if (count($orderings) == 0) {
                $order_string .= " part_number asc";
            } else {
                for ($i = 0; $i < count($orderings); $i++) {
                    if ($i > 0) {
                        $order_string .= ", ";
                    }

                    $field = $columns[$orderings[$i]["column"]];
                    $order_string .=  $field . " " . $orderings[$i]["dir"];
                }
            }
        }

        // Now, we need to do a similar thing for searching lightspeed part table...
        $filter = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : "");

        $this->load->helper("jonathan");

        $where = jonathan_generate_likes(array("part_number",
            "supplier_code",
            "description",
            "available",
            "current_active_price",
            "cost"), $filter, "WHERE");

        $total_count = 0;
        $query = $this->db->query("Select count(*) as cnt from lightspeedpart");
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        // Now, is there a filter?
        $filtered_count = $total_count;
        if ($where != "") {
            $query = $this->db->query("Select count(*) as cnt from lightspeedpart $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        }

        // Finally, run it!
        $query = $this->db->query("Select * from lightspeedpart  $where $order_string limit $length offset $start ");
        $rows = $query->result_array();

        // Now, order them...
        $new_rows = array();
        foreach ($rows as $p) {
            $new_rows[] = array(
                $p["part_number"], $p["supplier_code"], $p["description"], $p["available"], $p["current_active_price"],
                $p["cost"], date("m/d/Y g:i a T", strtotime($p["lightspeed_last_seen"])), $p["partvariation_id"] > 0 ? "Yes" : "No",
                $p["partvariation_id"] > 0 || $p["eternalpartvariation_id"] > 0 ? "Yes" : "No"
            );
        }

        print json_encode(array(
            "data" => $new_rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => $s
        ));
    }
}