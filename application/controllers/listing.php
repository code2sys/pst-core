<?php

class ControllerEbayeaxeListing extends Controller {

    public $headers = array();
    public $cred = array();
    public $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
    private $compatibility_level = 849;
    private $call;
    private $siteID = 0;
    private $all = array();
    private $store_url = '';
    private $boundary;
    private $related_images = array();
    private $check_header_type_image = false; //check for header type
    public $call_from_cron = false;
    public $item_id;
    public $current_product_id;
    private $product_data = array();

    function __construct($registry) {
        parent::__construct($registry);
        $this->store_url = $this->config->get('config_url');
    }

    /**
     * Function to display all products available in the database to select and
     * upload on eBay.
     * @author Anik Goel
     * @access public
     */
    public function index() {
        $this->language->load('ebayeaxe/amazon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->template = 'ebayeaxe/ebay.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->data['token'] = $this->session->data['token'];
        $this->data['heading'] = $this->language->get('heading_title_ebay');
        $this->data['text_product_listing'] = $this->language->get('text_product_listing');
        $this->data['text_submission_logs'] = $this->language->get('text_submission_logs');
        $this->data['text_show_products'] = $this->language->get('text_show_products');
        $this->data['text_button_filter'] = $this->language->get('text_button_filter');
        $this->data['text_button_proceed'] = $this->language->get('text_button_proceed');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->getList();

        $this->response->setOutput($this->render());
    }

    /**
     * Function to process all the products fetched from product selection page.
     * This function save all product ids into session for further use.
     * @param type $product_id
     * @author Anik Goel
     * @access public
     */
    public function processBulkUpload($product_id = NULL) {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') || $product_id != NULL) {
            $this->template = 'ebayeaxe/product_info.tpl';
            if ($product_id == NULL) {
                $this->document->setTitle($this->language->get('heading_title'));
                $this->children = array(
                    'common/header',
                    'common/footer'
                );
                $this->data['product_page'] = '0';
            } else {
                $this->data['product_page'] = '1';
            }
            $this->language->load('ebayeaxe/amazon');
            $this->load->model('ebayeaxe/setting');
            $this->load->model('ebayeaxe/product');
            $this->data['payment_options'] = $this->model_ebayeaxe_setting->allListingPaymentOptions();
            $this->data['shipping_options'] = $this->model_ebayeaxe_setting->allListingShippingOptions();
            $this->data['shipping_group'] = $this->model_ebayeaxe_setting->allListingShippingGroup();
            $this->setLanguageForProductEditingPage();
            $this->data['token'] = $this->session->data['token'];
            $this->load->model('ebayeaxe/category');
            $cat_array = $this->model_ebayeaxe_category->getParentCategories();
            $this->data['categories'] = $cat_array;
            $this->data['action'] = $this->url->link('ebayeaxe/listing/saveEbayDataAndUpload', 'token=' . $this->session->data['token'], 'SSL');
            if ($product_id == NULL) {
                $this->session->data['upload_products'] = $this->request->post['selected'];
                $this->getAllProductDetails();
            } else {
                $this->session->data['upload_products'] = array($product_id);
                $this->getAllProductDetails($product_id);
                $this->data['ebayDetails'] = $this->getEbayProductDetails($product_id);
            }
            $this->response->setOutput($this->render());
        } else {
            $this->redirect($this->url->link('ebayeaxe/listing', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }

    /**
     * Function to save Data in eBay database.
     * This function save general, category, shipping and payment data
     * @author Anik Goel
     * @access public
     */
    public function saveEbayDataAndUpload($product_data = null) {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') || ($this->call_from_cron)) {
            $ebayDetails = array();
            if ($product_data == NULL) {
                $data = $this->request->post;
                $this->load->model('ebayeaxe/product');
                $this->load->model('ebayeaxe/ebay_product');
                $this->load->model('catalog/product');
                $this->load->model('catalog/attribute');
                $this->load->model('localisation/weight_class');
                $this->load->model('localisation/length_class');
                $all_product_ids = $this->session->data['upload_products'];
            } else {
                $this->model_ebayeaxe_product = new ModelEbayeaxeProduct($this->registry);
                $this->model_ebayeaxe_ebay_product = new ModelEbayeaxeEbayProduct($this->registry);
                $this->model_localisation_weight_class = new ModelLocalisationWeightClass($this->registry);
                $this->model_localisation_length_class = new modelLocalisationLengthClass($this->registry);
                $this->model_catalog_attribute = new ModelCatalogAttribute($this->registry);
                $this->model_ebayeaxe_setting = new ModelEbayeaxeSetting($this->registry);
                $data = $product_data;
                $all_product_ids[] = $this->current_product_id;
//            die;
            }

            foreach ($all_product_ids as $product_id) {
                $sdata = array();
                $sdata['name'] = $data['product_description'][$product_id]['name'];
                $sdata['description'] = $data['product_description'][$product_id]['description'];
                $sdata['price'] = $data['product_description'][$product_id]['price'];
                $sdata['quantity'] = $data['product_description'][$product_id]['quantity'];
                if (isset($data['shipping_method'][$product_id])) {
                    foreach ($data['shipping_method'][$product_id] as $shipping_type) {
                        if ($data['shipping_type'][$product_id] == 'flat') {
                            $sdata['shipping'][$shipping_type]['value'] = $data['flat_rate_value'][$product_id][$shipping_type];
                            $sdata['shipping'][$shipping_type]['free_shipping'] = (isset($data['free_shipping'][$product_id][$shipping_type])) ? $data['free_shipping'][$product_id][$shipping_type] : 0;
                        } else {
                            $sdata['shipping'][$shipping_type]['value'] = $data['shipping_type'][$product_id];
                            $sdata['shipping'][$shipping_type]['free_shipping'] = (isset($data['free_shipping'][$product_id][$shipping_type])) ? $data['free_shipping'][$product_id][$shipping_type] : 0;
                        }
                    }
                }
                if (isset($data['payment_method'][$product_id])) {
                    $sdata['payment_method'] = $data['payment_method'][$product_id];
                }
                end($data['ebay_category'][$product_id]);
                $category_key = key($data['ebay_category'][$product_id]);
                $sdata['category'] = $data['ebay_category'][$product_id][$category_key];
                if (isset($data['product_description'][$product_id]['upload_option'])) {
                    $sdata['upload_option'] = $data['product_description'][$product_id]['upload_option'];
                }
                $this->model_ebayeaxe_product->updateEbayData($sdata, $product_id);
                $product_data = $this->model_ebayeaxe_product->getProductDataForEbayUpload($product_id);
                $product_data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
                $product_data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
                $product_data['extra_images'] = $this->model_ebayeaxe_product->getProductImages($product_id);
                $product_attributes = $this->model_catalog_product->getProductAttributes($product_id);
                foreach ($product_attributes as $product_attribute) {
                    $attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);
                    if ($attribute_info) {
                        $product_data['product_attribute'][] = array(
                            'attribute_id' => $product_attribute['attribute_id'],
                            'name' => $attribute_info['name'],
                            'product_attribute_description' => $product_attribute['product_attribute_description']
                        );
                    }
                }
                $this->_setHeader('UploadSiteHostedPictures', true);
                $product_data['extra_images'][] = array('image' => $product_data['product_info']['image']);
                if ($this->call_from_cron) {
                    $requested_data['ItemID'] = $this->item_id;
                } else {
                    $requested_data = $this->request->post;
                }
                if ($requested_data['ItemID'] == '') {
                    $ebay_eps = $this->buildImagesXMLUpload($product_data['extra_images'], $product_id, $product_data['product_info']['id']);
                    $this->_saveRelatedImagesToDatabase($product_id);
                } else {
                    $this->_setRelatedImages($product_id);
                }
                $this->_setHeader($data['call'], false);
                $ebay_response = $this->buildXmlAndHitEbay($product_data);
                $this->model_ebayeaxe_ebay_product->saveEbayProduct($ebay_response, $product_id, $product_data['product_info']['id']);
                if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    $ebayDetails = $this->getEbayProductDetails($product_id);
                }
            }
            if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                echo json_encode(array('status' => 1, 'msg' => $ebayDetails));
                exit();
            }
        }
        if ($this->call_from_cron) {
            return TRUE;
        } else {
            $this->redirect($this->url->link('ebayeaxe/listing', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }

    /**
     * Function to get all product detauls
     * @param int $product_id_refer
     * @author Anik Goel
     * @access private
     */
    private function getAllProductDetails($product_id_refer = NULL) {
        $this->language->load('catalog/product');
        $this->load->model('catalog/product');
        $this->setLanguageForProductDetails();
        if ($product_id_refer == NULL) {
            $products = $this->request->post['selected'];
            foreach ($products as $product_id) {
                $this->setProductDetails($product_id);
            }
        } else {
            $this->setProductDetails($product_id_refer);
        }
    }

    /**
     * Function to set product details in db
     * This function first check whether the product exists in ebay database, if
     * the data doesnt exists then it saves the data in ebay DB.
     * @param int $product_id
     * @access public
     * @author Anik Goel
     */
    public function setProductDetails($product_id) {
        $this->load->model('ebayeaxe/product');
        $product_from_ebay_db = $this->model_ebayeaxe_product->checkIfProductExistsInEbayProductByProductId($product_id);
        if (!$product_from_ebay_db) {
            $this->saveProductInEbayDb($product_id);
            $product_from_ebay_db = $this->model_ebayeaxe_product->checkIfProductExistsInEbayProductByProductId($product_id);
            $this->setProductForTemplate($product_from_ebay_db, $product_id);
        } else {
            $this->setProductForTemplate($product_from_ebay_db, $product_id);
        }
    }

    /**
     * Function to set all product detail into the template
     * @param type $product_from_ebay_db
     * @param type $product_id
     * @access private
     * @author Anik Goel
     */
    private function setProductForTemplate($product_from_ebay_db, $product_id) {
        $this->data['product_info'][$product_id] = $product_from_ebay_db['product_info'];
        $this->data['product_shipping_options'][$product_id] = $product_from_ebay_db['shipping_options'];
        if ($product_from_ebay_db['product_info']['category_id'] != NULL) {
            $this->data['product_selected_categories'][$product_id] = array_reverse($this->model_ebayeaxe_category->getParentCategoriesFromChildCategory($product_from_ebay_db['product_info']['category_id']));
            foreach ($this->data['product_selected_categories'][$product_id] as $key => $selected_categories) {
                $child_categories = array();
                $child_categories = $this->model_ebayeaxe_category->getChildCategoriesByCategoryId($selected_categories);
                if ($child_categories) {
                    $this->data['selected_category_array'][$product_id][$key + 1] = $child_categories;
                }
            }
        }
        foreach ($product_from_ebay_db['shipping_options'] as $shipping_option) {
            $this->data['shipping_option_names'][$product_id][] = $shipping_option['shipping_type'];
        }
        foreach ($product_from_ebay_db['payment_options'] as $payment_option) {
            $this->data['product_payment_options'][$product_id][] = $payment_option['payment_type'];
        }
        $this->load->model('tool/image');
        if ($product_from_ebay_db['product_info']['image'] && file_exists(DIR_IMAGE . $product_from_ebay_db['product_info']['image'])) {
            $this->data['product_info'][$product_id]['thumb'] = $this->model_tool_image->resize($product_from_ebay_db['product_info']['image'], 100, 100);
        } else {
            $this->data['product_info'][$product_id]['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }
    }

    /**
     * Function to save product data in ebay db before processing of ebay upload.
     * @param int $product_id
     * @access private
     * @author Anik Goel
     */
    private function saveProductInEbayDb($product_id) {
        $product_info = $this->model_catalog_product->getProduct($product_id);
        $this->load->model('localisation/language');
        $temp_data['languages'] = $this->model_localisation_language->getLanguages();

        $temp_data['product_info'][$product_id]['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['model'] = $product_info['model'];
        } else {
            $temp_data['product_info'][$product_id]['model'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['sku'] = $product_info['sku'];
        } else {
            $temp_data['product_info'][$product_id]['sku'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['upc'] = $product_info['upc'];
        } else {
            $temp_data['product_info'][$product_id]['upc'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['location'] = $product_info['location'];
        } else {
            $temp_data['product_info'][$product_id]['location'] = '';
        }

        $this->load->model('setting/store');

        $temp_data['stores'] = $this->model_setting_store->getStores();

        $temp_data['product_info'][$product_id]['product_store'] = $this->model_catalog_product->getProductStores($product_id);

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['keyword'] = $product_info['keyword'];
        } else {
            $temp_data['product_info'][$product_id]['keyword'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['image'] = $product_info['image'];
        } else {
            $temp_data['product_info'][$product_id]['image'] = '';
        }

        $this->load->model('tool/image');

        if (!empty($product_info) && $product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
            $temp_data['product_info'][$product_id]['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
        } else {
            $temp_data['product_info'][$product_id]['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['shipping'] = $product_info['shipping'];
        } else {
            $temp_data['product_info'][$product_id]['shipping'] = 1;
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['price'] = $product_info['price'];
        } else {
            $temp_data['product_info'][$product_id]['price'] = '';
        }

        $this->load->model('catalog/profile');

        $temp_data['profiles'] = $this->model_catalog_profile->getProfiles();

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['product_profiles'] = $this->model_catalog_product->getProfiles($product_info['product_id']);
        } else {
            $temp_data['product_info'][$product_id]['product_profiles'] = array();
        }

        $this->load->model('localisation/tax_class');

        $temp_data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['tax_class_id'] = $product_info['tax_class_id'];
        } else {
            $temp_data['product_info'][$product_id]['tax_class_id'] = 0;
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['date_available'] = date('Y-m-d', strtotime($product_info['date_available']));
        } else {
            $temp_data['product_info'][$product_id]['date_available'] = date('Y-m-d', time() - 86400);
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['quantity'] = $product_info['quantity'];
        } else {
            $temp_data['product_info'][$product_id]['quantity'] = 1;
        }

        $this->load->model('localisation/stock_status');

        $temp_data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();


        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['status'] = $product_info['status'];
        } else {
            $temp_data['product_info'][$product_id]['status'] = 1;
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['weight'] = $product_info['weight'];
        } else {
            $temp_data['product_info'][$product_id]['weight'] = '';
        }

        $this->load->model('localisation/weight_class');

        $temp_data['product_info'][$product_id]['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['weight_class_id'] = $product_info['weight_class_id'];
        } else {
            $temp_data['product_info'][$product_id]['weight_class_id'] = $this->config->get('config_weight_class_id');
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['length'] = $product_info['length'];
        } else {
            $temp_data['product_info'][$product_id]['length'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['width'] = $product_info['width'];
        } else {
            $temp_data['product_info'][$product_id]['width'] = '';
        }

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['height'] = $product_info['height'];
        } else {
            $temp_data['product_info'][$product_id]['height'] = '';
        }

        $this->load->model('localisation/length_class');

        $temp_data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['length_class_id'] = $product_info['length_class_id'];
        } else {
            $temp_data['product_info'][$product_id]['length_class_id'] = $this->config->get('config_length_class_id');
        }

        $this->load->model('catalog/manufacturer');

        if (!empty($product_info)) {
            $temp_data['product_info'][$product_id]['manufacturer_id'] = $product_info['manufacturer_id'];
        } else {
            $temp_data['product_info'][$product_id]['manufacturer_id'] = 0;
        }

        if (!empty($product_info)) {
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

            if ($manufacturer_info) {
                $temp_data['product_info'][$product_id]['manufacturer'] = $manufacturer_info['name'];
            } else {
                $temp_data['product_info'][$product_id]['manufacturer'] = '';
            }
        } else {
            $temp_data['product_info'][$product_id]['manufacturer'] = '';
        }

// Categories
        $this->load->model('catalog/category');

        $categories = $this->model_catalog_product->getProductCategories($product_id);

        $temp_data['product_categories'] = array();

        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $temp_data['product_info'][$product_id]['product_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
                );
            }
        }

// Filters
        $this->load->model('catalog/filter');

        $filters = $this->model_catalog_product->getProductFilters($product_id);

        $temp_data['product_info'][$product_id]['product_filters'] = array();

        foreach ($filters as $filter_id) {
            $filter_info = $this->model_catalog_filter->getFilter($filter_id);

            if ($filter_info) {
                $temp_data['product_info'][$product_id]['product_filters'][] = array(
                    'filter_id' => $filter_info['filter_id'],
                    'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
                );
            }
        }

// Attributes
        $this->load->model('catalog/attribute');

        $product_attributes = $this->model_catalog_product->getProductAttributes($product_id);

        $temp_data['product_info'][$product_id]['product_attributes'] = array();

        foreach ($product_attributes as $product_attribute) {
            $attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

            if ($attribute_info) {
                $temp_data['product_info'][$product_id]['product_attributes'][] = array(
                    'attribute_id' => $product_attribute['attribute_id'],
                    'name' => $attribute_info['name'],
                    'product_attribute_description' => $product_attribute['product_attribute_description']
                );
            }
        }

// Options
        $this->load->model('catalog/option');

        $product_options = $this->model_catalog_product->getProductOptions($product_id);

        $temp_data['product_info'][$product_id]['product_options'] = array();

        foreach ($product_options as $product_option) {
            if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                $product_option_value_data = array();

                foreach ($product_option['product_option_value'] as $product_option_value) {
                    $product_option_value_data[] = array(
                        'product_option_value_id' => $product_option_value['product_option_value_id'],
                        'option_value_id' => $product_option_value['option_value_id'],
                        'quantity' => $product_option_value['quantity'],
                        'subtract' => $product_option_value['subtract'],
                        'price' => $product_option_value['price'],
                        'price_prefix' => $product_option_value['price_prefix'],
                        'points' => $product_option_value['points'],
                        'points_prefix' => $product_option_value['points_prefix'],
                        'weight' => $product_option_value['weight'],
                        'weight_prefix' => $product_option_value['weight_prefix']
                    );
                }

                $temp_data['product_info'][$product_id]['product_options'][] = array(
                    'product_option_id' => $product_option['product_option_id'],
                    'product_option_value' => $product_option_value_data,
                    'option_id' => $product_option['option_id'],
                    'name' => $product_option['name'],
                    'type' => $product_option['type'],
                    'required' => $product_option['required']
                );
            } else {
                $temp_data['product_info'][$product_id]['product_options'][] = array(
                    'product_option_id' => $product_option['product_option_id'],
                    'option_id' => $product_option['option_id'],
                    'name' => $product_option['name'],
                    'type' => $product_option['type'],
                    'option_value' => $product_option['option_value'],
                    'required' => $product_option['required']
                );
            }
        }

        $temp_data['product_info'][$product_id]['option_values'] = array();

        foreach ($temp_data['product_info'][$product_id]['product_options'] as $product_option) {
            if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                if (!isset($temp_data['product_info'][$product_id]['option_values'][$product_option['option_id']])) {
                    $temp_data['product_info'][$product_id]['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
                }
            }
        }

        $temp_data['product_info'][$product_id]['product_discounts'] = $this->model_catalog_product->getProductDiscounts($product_id);

        $temp_data['product_info'][$product_id]['product_specials'] = $this->model_catalog_product->getProductSpecials($product_id);

// Images
        $product_images = $this->model_catalog_product->getProductImages($product_id);

        $temp_data['product_info'][$product_id]['product_images'] = array();

        foreach ($product_images as $product_image) {
            if ($product_image['image'] && file_exists(DIR_IMAGE . $product_image['image'])) {
                $image = $product_image['image'];
            } else {
                $image = 'no_image.jpg';
            }

            $temp_data['product_info'][$product_id]['product_images'][] = array(
                'image' => $image,
                'thumb' => $this->model_tool_image->resize($image, 100, 100),
                'sort_order' => $product_image['sort_order']
            );
        }

        $temp_data['product_info'][$product_id]['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        $all_setting = $this->buildSettingArray($this->model_ebayeaxe_setting->getAllSettings($temp_data['product_info'][$product_id]));
        $this->model_ebayeaxe_product->saveProductForEbay($temp_data['product_info'][$product_id], $product_id, $all_setting);
    }

    /**
     * Function to build all setting into an array
     * @param type $all_setting
     * @return type
     * @access private
     * @author Anik Goel
     */
    private function buildSettingArray($all_setting) {
        $temp_array = array();
        foreach ($all_setting as $value) {
            $temp_array[$value['name']] = $value['value'];
        }
        return $temp_array;
    }

    /**
     * Function to set all language variables in product detail page
     * @access private
     * @author Anik Goel
     */
    private function setLanguageForProductDetails() {
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_plus'] = $this->language->get('text_plus');
        $this->data['text_minus'] = $this->language->get('text_minus');
        $this->data['text_default'] = $this->language->get('text_default');
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['text_browse'] = $this->language->get('text_browse');
        $this->data['text_clear'] = $this->language->get('text_clear');
        $this->data['text_option'] = $this->language->get('text_option');
        $this->data['text_option_value'] = $this->language->get('text_option_value');
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_percent'] = $this->language->get('text_percent');
        $this->data['text_amount'] = $this->language->get('text_amount');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $this->data['entry_description'] = $this->language->get('entry_description');
        $this->data['entry_store'] = $this->language->get('entry_store');
        $this->data['entry_keyword'] = $this->language->get('entry_keyword');
        $this->data['entry_model'] = $this->language->get('entry_model');
        $this->data['entry_sku'] = $this->language->get('entry_sku');
        $this->data['entry_upc'] = $this->language->get('entry_upc');
        $this->data['entry_ean'] = $this->language->get('entry_ean');
        $this->data['entry_jan'] = $this->language->get('entry_jan');
        $this->data['entry_isbn'] = $this->language->get('entry_isbn');
        $this->data['entry_mpn'] = $this->language->get('entry_mpn');
        $this->data['entry_location'] = $this->language->get('entry_location');
        $this->data['entry_minimum'] = $this->language->get('entry_minimum');
        $this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
        $this->data['entry_shipping'] = $this->language->get('entry_shipping');
        $this->data['entry_date_available'] = $this->language->get('entry_date_available');
        $this->data['entry_quantity'] = $this->language->get('entry_quantity');
        $this->data['entry_stock_status'] = $this->language->get('entry_stock_status');
        $this->data['entry_price'] = $this->language->get('entry_price');
        $this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
        $this->data['entry_points'] = $this->language->get('entry_points');
        $this->data['entry_option_points'] = $this->language->get('entry_option_points');
        $this->data['entry_subtract'] = $this->language->get('entry_subtract');
        $this->data['entry_weight_class'] = $this->language->get('entry_weight_class');
        $this->data['entry_weight'] = $this->language->get('entry_weight');
        $this->data['entry_dimension'] = $this->language->get('entry_dimension');
        $this->data['entry_length'] = $this->language->get('entry_length');
        $this->data['entry_image'] = $this->language->get('entry_image');
        $this->data['entry_download'] = $this->language->get('entry_download');
        $this->data['entry_category'] = $this->language->get('entry_category');
        $this->data['entry_filter'] = $this->language->get('entry_filter');
        $this->data['entry_related'] = $this->language->get('entry_related');
        $this->data['entry_attribute'] = $this->language->get('entry_attribute');
        $this->data['entry_text'] = $this->language->get('entry_text');
        $this->data['entry_option'] = $this->language->get('entry_option');
        $this->data['entry_option_value'] = $this->language->get('entry_option_value');
        $this->data['entry_required'] = $this->language->get('entry_required');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_date_start'] = $this->language->get('entry_date_start');
        $this->data['entry_date_end'] = $this->language->get('entry_date_end');
        $this->data['entry_priority'] = $this->language->get('entry_priority');
        $this->data['entry_tag'] = $this->language->get('entry_tag');
        $this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $this->data['entry_reward'] = $this->language->get('entry_reward');
        $this->data['entry_layout'] = $this->language->get('entry_layout');
        $this->data['entry_profile'] = $this->language->get('entry_profile');

        $this->data['text_recurring_help'] = $this->language->get('text_recurring_help');
        $this->data['text_recurring_title'] = $this->language->get('text_recurring_title');
        $this->data['text_recurring_trial'] = $this->language->get('text_recurring_trial');
        $this->data['entry_recurring'] = $this->language->get('entry_recurring');
        $this->data['entry_recurring_price'] = $this->language->get('entry_recurring_price');
        $this->data['entry_recurring_freq'] = $this->language->get('entry_recurring_freq');
        $this->data['entry_recurring_cycle'] = $this->language->get('entry_recurring_cycle');
        $this->data['entry_recurring_length'] = $this->language->get('entry_recurring_length');
        $this->data['entry_trial'] = $this->language->get('entry_trial');
        $this->data['entry_trial_price'] = $this->language->get('entry_trial_price');
        $this->data['entry_trial_freq'] = $this->language->get('entry_trial_freq');
        $this->data['entry_trial_length'] = $this->language->get('entry_trial_length');
        $this->data['entry_trial_cycle'] = $this->language->get('entry_trial_cycle');

        $this->data['text_length_day'] = $this->language->get('text_length_day');
        $this->data['text_length_week'] = $this->language->get('text_length_week');
        $this->data['text_length_month'] = $this->language->get('text_length_month');
        $this->data['text_length_month_semi'] = $this->language->get('text_length_month_semi');
        $this->data['text_length_year'] = $this->language->get('text_length_year');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_attribute'] = $this->language->get('button_add_attribute');
        $this->data['button_add_option'] = $this->language->get('button_add_option');
        $this->data['button_add_option_value'] = $this->language->get('button_add_option_value');
        $this->data['button_add_discount'] = $this->language->get('button_add_discount');
        $this->data['button_add_special'] = $this->language->get('button_add_special');
        $this->data['button_add_image'] = $this->language->get('button_add_image');
        $this->data['button_remove'] = $this->language->get('button_remove');
        $this->data['button_add_profile'] = $this->language->get('button_add_profile');

        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_data'] = $this->language->get('tab_data');
        $this->data['tab_attribute'] = $this->language->get('tab_attribute');
        $this->data['tab_option'] = $this->language->get('tab_option');
        $this->data['tab_profile'] = $this->language->get('tab_profile');
        $this->data['tab_discount'] = $this->language->get('tab_discount');
        $this->data['tab_special'] = $this->language->get('tab_special');
        $this->data['tab_image'] = $this->language->get('tab_image');
        $this->data['tab_links'] = $this->language->get('tab_links');
        $this->data['tab_reward'] = $this->language->get('tab_reward');
        $this->data['tab_design'] = $this->language->get('tab_design');
        $this->data['tab_marketplace_links'] = $this->language->get('tab_marketplace_links');
    }

    /**
     * eBay Settings page function
     * @access public
     * @author Anik Goel
     */
    public function ebaySettings() {
        $this->load->model('ebayeaxe/setting');
        $this->language->load('ebayeaxe/ebay_settings');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->template = 'ebayeaxe/setting.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->data['listing_durations'] = $this->model_ebayeaxe_setting->allListingDuration();
        $this->data['listing_sites'] = $this->model_ebayeaxe_setting->allListingSites();
        $this->data['listing_countries'] = $this->model_ebayeaxe_setting->allListingCountries();
        $this->data['listing_return_accepted_options'] = $this->model_ebayeaxe_setting->allListingReturnAcceptedOptions();
        $this->data['listing_refund_options'] = $this->model_ebayeaxe_setting->allListingRefundOptions();
        $this->data['listing_return_within_options'] = $this->model_ebayeaxe_setting->allListingReturnWithinOptions();
        $this->data['listing_return_shipping_options'] = $this->model_ebayeaxe_setting->allListingReturnShippingCostOptions();
        $this->data['listing_currencies'] = $this->model_ebayeaxe_setting->allListingCurrencies();
        $all_settings = $this->model_ebayeaxe_setting->getAllSettings();
        foreach ($all_settings as $data) {
            $this->data[$data['name']] = $data['value'];
        }
        $this->setLanguageForSettingsPage();
        $this->data['action'] = $this->url->link('ebayeaxe/listing/saveEbaySettings', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['token'] = $this->session->data['token'];
        $this->response->setOutput($this->render());
    }

    /**
     * Function to save ebay settings in database
     * @access public
     * @author Anik Goel
     */
    public function saveEbaySettings() {
        $this->load->model('ebayeaxe/setting');
        $this->model_ebayeaxe_setting->saveSettings($this->request->post);
        $this->redirect($this->url->link('ebayeaxe/listing/ebaySettings', 'token=' . $this->session->data['token'], 'SSL'));
    }

    /**
     * Function to get a sub category of a category using parent id
     * @access public
     * @author Anik Goel
     */
    public function getSubCategory() {
        $category_id = $this->request->post['category_id'];
        $this->load->model('ebayeaxe/category');
        $cat_array = $this->model_ebayeaxe_category->getChildCategoriesByCategoryId($category_id);
        if ($cat_array) {
            $response = array(
                'status' => 1,
                'data' => $cat_array
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 0, 'message' => 'No Sub Categories Found'));
        }
    }

    /**
     * Function to make combinations of array values
     * @param type $data
     * @param type $group
     * @param type $val
     * @param type $i
     * @param type $option_name
     * @return type
     * @access public
     * @author Anik Goel
     */
    public function combos($data, $group = array(), $val = null, $i = 0, $option_name = NULL) {
        if (isset($val)) {
            $group[]['name'] = $option_name;
            end($group);
            $group[key($group)]['value'] = $val;
        }
        if ($i >= count($data)) {
            array_push($this->all, $group);
        } else {
            foreach ($data[$i]['value'] as $v) {
                $this->combos($data, $group, $v, $i + 1, $data[$i]['name']);
            }
        }
        return $this->all;
    }

    /**
     * Function to build xml for adding new product to ebay or to revise the
     * existing product on eBay
     * @param type $product_data
     * @return type
     * @access public
     * @author Anik Goel
     */
    public function buildXmlAndHitEbay($product_data) {
        if ($this->call_from_cron) {
            $requested_data['ItemID'] = $this->item_id;
        } else {
            $requested_data = $this->request->post;
        }

        $weight['exploded_value'] = explode('.', $product_data['product_info']['weight']);
        $dimension['value']['length'] = ($product_data['product_info']['length']);
        $dimension['value']['width'] = ($product_data['product_info']['width']);
        $dimension['value']['height'] = ($product_data['product_info']['height']);
        foreach ($product_data['weight_classes'] as $weight_class) {
            if ($weight_class['weight_class_id'] == $product_data['product_info']['weight_class_id']) {
                $weight['unit'] = $weight_class['unit'];
                break;
            }
        }
        foreach ($product_data['length_classes'] as $length_class) {
            if ($length_class['length_class_id'] == $product_data['product_info']['length_class_id']) {
                $dimension['unit'] = $length_class['unit'];
                break;
            }
        }
        if (!empty($product_data['options'])) {
            foreach ($product_data['options'] as $option_key => $option) {
                if ($option['type'] != 'select' && $option['type'] != 'radio' && $option['type'] != 'checkbox') {
                    unset($product_data['options'][$option_key]);
                }
            }
        }
        $count = 1;
        $store_url = $this->config->get('config_url');
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';
        if ($requested_data['ItemID'] == '') {
            $uploadXML .= '<AddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        } else {
            $uploadXML .= '<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        }
        $uploadXML .= '<ErrorLanguage>en_US</ErrorLanguage>';

        $string = utf8_encode($product_data['product_info']['description']);
//            $string = preg_replace('/[\x00-\x1F\x7f-\xFF\&auml;]/', '', $string);
        $string = substr($string, 0, 500000);
        $UUID = md5(uniqid(rand(), true));

        $uploadXML .= '<MessageID>' . $count . '</MessageID>';
        $uploadXML .= '<Item>';
        if ($requested_data['ItemID'] != '') {
            $uploadXML .= '<ItemID>' . $requested_data['ItemID'] . '</ItemID>';
        }
        if ($product_data['product_info']['upc'] != NULL) {
            $uploadXML .= '<ProductListingDetails>';
            $uploadXML .= '<UPC>' . $product_data['product_info']['upc'] . '</UPC>';
            $uploadXML .= '</ProductListingDetails>';
        }
        $uploadXML .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>';
        $uploadXML .= '<Country>' . $product_data['product_info']['listing_country'] . '</Country>';
        $uploadXML .= '<location>' . $product_data['product_info']['listing_country'] . '</location>';
        $uploadXML .= '<Currency>' . $product_data['product_info']['currency'] . '</Currency>';
        $uploadXML .= '<ConditionID>' . $product_data['product_info']['ConditionID'] . '</ConditionID>';
        $uploadXML .= '<Description>' . $string . '</Description>';
        $uploadXML .= '<DispatchTimeMax>' . $product_data['product_info']['dispatch_time'] . '</DispatchTimeMax>';
        $uploadXML .= '<ListingDuration>' . $product_data['product_info']['listing_duration'] . '</ListingDuration>';
        $uploadXML .= '<ListingType>FixedPriceItem</ListingType>';
        $paypal = false;
        foreach ($product_data['payment_options'] as $value) {
            $uploadXML .= '<PaymentMethods>' . $value['payment_type'] . '</PaymentMethods>';
            if ($value['payment_type'] == 'PayPal') {
                $paypal = TRUE;
            }
        }
        if ($paypal) {
            $uploadXML .= '<PayPalEmailAddress>' . $product_data['product_info']['paypal_address'] . '</PayPalEmailAddress>';
        }
        $uploadXML .= '<PictureDetails>';
//        $uploadXML .= '<PictureURL>' . $store_url . 'image/' . $product_data['product_info']['image'] . '</PictureURL>';
        if ($this->related_images) {
            foreach ($this->related_images as $g_imgs) {
                $uploadXML .= '<PictureURL>' . $g_imgs . '</PictureURL>';
            }
        }
        $uploadXML .= '</PictureDetails>';
        $uploadXML .= '<PostalCode>' . $product_data['product_info']['postal_code'] . '</PostalCode>';
        $uploadXML .= '<PrimaryCategory>';
        $uploadXML .= '<CategoryID>' . $product_data['product_info']['category_id'] . '</CategoryID>';
        $uploadXML .= '</PrimaryCategory>';
        $uploadXML .= '<ReturnPolicy>';
        $uploadXML .= '<ReturnsAcceptedOption>' . $product_data['product_info']['listing_return_accepted'] . '</ReturnsAcceptedOption>';
        $uploadXML .= '<RefundOption>' . $product_data['product_info']['listing_refund_option'] . '</RefundOption>';
        $uploadXML .= '<ReturnsWithinOption>' . $product_data['product_info']['listing_return_within_option'] . '</ReturnsWithinOption>';
        $uploadXML .= '<Description>' . $product_data['product_info']['returnpolicy_description'] . '</Description>';
        $uploadXML .= '<ShippingCostPaidByOption>' . $product_data['product_info']['listing_return_shipping_option'] . '</ShippingCostPaidByOption>';
        $uploadXML .= '</ReturnPolicy>';
        $shipping_first_key = key($product_data['shipping_options']);
        $uploadXML .= '<ShippingDetails>';
        $uploadXML .= '<ShippingType>';
        $uploadXML .= (isset($product_data['shipping_options'][$shipping_first_key]['shipping_value']) && $product_data['shipping_options'][$shipping_first_key]['shipping_value'] == 'calculated') ? 'Calculated' : 'Flat';
        $uploadXML .= '</ShippingType>';
        if (isset($product_data['shipping_options'][$shipping_first_key]['shipping_value']) && $product_data['shipping_options'][$shipping_first_key]['shipping_value'] == 'calculated') {
            $uploadXML .= '<CalculatedShippingRate>';
            $uploadXML .= '<MeasurementUnit>English</MeasurementUnit>';
            $uploadXML .= '<OriginatingPostalCode>' . $product_data['product_info']['postal_code'] . '</OriginatingPostalCode>';
            $uploadXML .= '<PackageDepth>' . $dimension['value']['height'] . '</PackageDepth>';
            $uploadXML .= '<PackageLength>' . $dimension['value']['length'] . '</PackageLength>';
            $uploadXML .= '<PackageWidth>' . $dimension['value']['width'] . '</PackageWidth>';
            $uploadXML .= '<ShippingPackage>PackageThickEnvelope</ShippingPackage>';
            $uploadXML .= '<WeightMajor>';
            $uploadXML .= $weight['exploded_value'][0];
            $uploadXML .= '</WeightMajor>';
            $uploadXML .= '<WeightMinor>';
            $uploadXML .= (isset($weight['exploded_value'][1])) ? (rtrim($weight['exploded_value'][1], '0') != NULL) ? rtrim($weight['exploded_value'][1], '0') : 0  : 0;
            $uploadXML .= '</WeightMinor>';
            $uploadXML .= '</CalculatedShippingRate>';
        }
        foreach ($product_data['shipping_options'] as $key => $shipping_option) {
            if ($shipping_option['international'] == 1) {
                $uploadXML .= '<InternationalShippingServiceOption>';
            } else {
                $uploadXML .= '<ShippingServiceOptions>';
            }
            $uploadXML .= '<FreeShipping>';
            $uploadXML .= ($shipping_option['free_shipping'] == 1) ? 'true' : 'false';
            $uploadXML .= '</FreeShipping>';
            $uploadXML .= '<ShippingServicePriority>' . ($key + 1) . '</ShippingServicePriority>';
            $uploadXML .= '<ShippingService>' . $shipping_option['shipping_type'] . '</ShippingService>';
            if ($shipping_option['shipping_value'] != 'calculated') {
                $uploadXML .= '<ShippingServiceCost>';
                $uploadXML .= ($shipping_option['shipping_value'] != '') ? $shipping_option['shipping_value'] : '0.00';
                $uploadXML .= '</ShippingServiceCost>';
                $uploadXML .= '<ShippingServiceAdditionalCost>';
                $uploadXML .= ($shipping_option['shipping_value'] != '') ? $shipping_option['shipping_value'] : '0.00';
                $uploadXML .= '</ShippingServiceAdditionalCost>';
            }
            if ($shipping_option['international'] == 1) {
                $uploadXML .= '</InternationalShippingServiceOption>';
            } else {
                $uploadXML .= '</ShippingServiceOptions>';
            }
        }

        $uploadXML .= '</ShippingDetails>';

        if (!empty($product_data['product_attribute'])) {
            $uploadXML .= '<ItemSpecifics>';
            foreach ($product_data['product_attribute'] as $attribute) {
                $uploadXML .= '<NameValueList>';
                $uploadXML .= '<Name>';
                $uploadXML .= $attribute['name'];
                $uploadXML .= '</Name>';
                foreach ($attribute['product_attribute_description'] as $attribute_value) {
                    $uploadXML .= '<Value>';
                    $uploadXML .= $attribute_value['text'];
                    $uploadXML .= '</Value>';
                }
                $uploadXML .= '</NameValueList>';
            }
            $uploadXML .= '</ItemSpecifics>';
        }
        if (!empty($product_data['options']) && ($product_data['product_info']['upload_option'] == 1 || $requested_data['ItemID'] != '')) {
            $uploadXML .= '<Variations>';
            $uploadXML .= '<VariationSpecificsSet>';
            foreach ($product_data['options'] as $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
                    $uploadXML .= '<NameValueList>';
                    $uploadXML .= '<Name>' . $option['name'] . '</Name>';
                    $i = 1;
                    foreach ($option['option_value'] as $option_value) {
                        if ($i > 5) {
                            break;
                        }
                        $uploadXML .= '<Value>' . $option_value['name'] . '</Value>';
                    }

                    $uploadXML .= '</NameValueList>';
                }
            }
            $uploadXML .= '</VariationSpecificsSet>';

            $temp_option = array();

            foreach ($product_data['options'] as $key => $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
                    $temp_option[$key]['name'] = $option['name'];
                    foreach ($option['option_value'] as $gh) {
                        $temp_option[$key]['value'][] = $gh['name'];
                    }
                }
            }
            $combos = $this->combos($temp_option);


            foreach ($combos as $combination) {
                $uploadXML .= '<Variation>';
                if ($product_data['product_info']['upload_option'] == 0) {
                    $uploadXML .= '<Delete>TRUE</Delete>';
                }
                $uploadXML .='<StartPrice>' . $product_data['product_info']['price'] . '</StartPrice>';
                $uploadXML .='<Quantity>' . $product_data['product_info']['quantity'] . '</Quantity>';
                $uploadXML .= '<VariationSpecifics>';
                foreach ($combination as $name_value) {
                    $uploadXML .= '<NameValueList>';
                    $uploadXML .= '<Name>' . $name_value['name'] . '</Name>';
                    $uploadXML .= '<Value>' . $name_value['value'] . '</Value>';
                    $uploadXML .= '</NameValueList>';
                }
                $uploadXML .= '</VariationSpecifics>';
                $uploadXML .= '</Variation>';
            }

            $uploadXML .= '</Variations>';
        } else {
            $uploadXML .= '<Quantity>' . $product_data['product_info']['quantity'] . '</Quantity>';
            $uploadXML .= '<StartPrice currencyID="USD">' . $product_data['product_info']['price'] . '</StartPrice>';
        }


        $uploadXML .= '<Site>' . $product_data['product_info']['listing_site'] . '</Site>';
        $uploadXML .= '<SKU>' . $product_data['product_info']['sku'] . '</SKU>';
        $uploadXML .= '<Title>' . substr(strip_tags($product_data['product_info']['name']), 0, 78) . '</Title>';
        $uploadXML .= '<UUID>' . $UUID . '</UUID>';
        $uploadXML .= '</Item>';
        $count++;
        $uploadXML .= '<RequesterCredentials>';
        $uploadXML .= '<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>';
        $uploadXML .= '</RequesterCredentials>';
        $uploadXML .= '<WarningLevel>High</WarningLevel>';
        if ($requested_data['ItemID'] == '') {
            $uploadXML .= '</AddFixedPriceItemRequest>';
        } else {
            $uploadXML .= '</ReviseFixedPriceItemRequest>';
        }
//        print_r($uploadXML);
//        print_r(json_decode(json_encode((array) simplexml_load_string($uploadXML)), 1));
//        die;
        $response = json_decode(json_encode((array) simplexml_load_string($this->call($uploadXML))), 1);
//        echo '<pre>';
//        print_r($response);
//        die;
        return $response;
    }

    /**
     * Developer Function to fetch various details from eBay
     * NOT USED IN THE USER FUNCTIONALITY
     * @access public
     * @author Anik Goel
     */
    public function getEbayDetails() {

        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        $detail_code = $this->request->get['detail_code'];
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <DetailName>' . $detail_code . '</DetailName>
  <ErrorLanguage>en_US</ErrorLanguage>
  <WarningLevel>High</WarningLevel>
  <RequesterCredentials><eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken></RequesterCredentials>' .
                '</GeteBayDetailsRequest>';
        $this->call = 'GeteBayDetails';
        $this->getHeaders();
        $response = $this->call($xml);
        $response = simplexml_load_string($response);
        $temp_arr = json_decode(json_encode((array) $response), 1);
        foreach ($temp_arr['ShippingServiceDetails'] as $value) {
            if (is_array($value['ServiceType'])) {
                $service_type = 2;
            } else {
                if ($value['ServiceType'] == 'Flat') {
                    $service_type = 0;
                } else {
                    $service_type = 1;
                }
            }
            if (isset($value['InternationalService']) && $value['InternationalService'] == 'true') {
                $international = 1;
            } else {
                $international = 0;
            }
            $this->db->query("INSERT into oc_eaxe_ebay_shipping_options (ShippingService,Description,ShippingServiceID,ServiceType,InternationalService) VALUES ('" . addslashes($value['ShippingService']) . "','" . addslashes($value['Description']) . "','" . $value['ShippingServiceID'] . "',$service_type,$international)");
        }
        echo '<pre>';
        print_r($temp_arr);
        die;
    }

    /**
     * Function to call ebay API using the xml passed to it.
     * @param type $xml
     * @return type
     * @access private
     * @author Anik Goel
     */
    private function call($xml) {
        $connection = curl_init();
//set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

//stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

//set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);

//set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

//set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $xml);

//set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

//Send the Request
        $response = curl_exec($connection);

//close the connection
        curl_close($connection);

        return $response;
    }

    /**
     * Function to build the ebay Header array
     * @access private
     * @author Anik Goel
     */
    private function getHeaders() {
        $this->boundary = "MIME_boundary";
        $this->getEbayAuthSettingsFromDb();
        if ($this->check_header_type_image) {
            $data = 'Content-Type: multipart/form-data; boundary=' . $this->boundary;
        } else {
            $data = "";
        }
        $this->headers = array(
            $data,
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatibility_level,
            //set the keys
            'X-EBAY-API-DEV-NAME:' . $this->cred['Setting']['dev_id'],
            'X-EBAY-API-APP-NAME:' . $this->cred['Setting']['app_id'],
            'X-EBAY-API-CERT-NAME:' . $this->cred['Setting']['cert_id'],
            //SiteID must also be set in the Request's XML
//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
//SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID:' . $this->siteID,
            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->call
        );
    }

    /**
     * Function to get all ebay auth setting from db
     * @access private
     * @author Anik Goel
     */
    private function getEbayAuthSettingsFromDb() {
        if (!$this->call_from_cron) {
            $this->load->model('ebayeaxe/setting');
        } else {
            $this->model_ebayeaxe_setting = new ModelEbayeaxeSetting($this->registry);
        }
        $this->cred['Setting'] = array(
            'dev_id' => $this->model_ebayeaxe_setting->getSettingByName('dev_id'),
            'app_id' => $this->model_ebayeaxe_setting->getSettingByName('app_id'),
            'cert_id' => $this->model_ebayeaxe_setting->getSettingByName('certificate_id'),
            'user_token' => $this->model_ebayeaxe_setting->getSettingByName('user_token'),
        );
    }

    /**
     * Opencart Function to get the product based on different filters
     * @access private
     * @author Anik Goel
     */
    private function getList() {
        $this->load->model('ebayeaxe/product');
        $this->load->model('catalog/product');
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_category'])) {
            $filter_category = $this->request->get['filter_category'];
        } else {
            $filter_category = null;
        }

        if (isset($this->request->get['filter_upc'])) {
            $filter_upc = $this->request->get['filter_upc'];
        } else {
            $filter_upc = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $this->data['insert'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['products'] = array();

        $data = array(
            'filter_name' => $filter_name,
            'filter_model' => $filter_model,
            'filter_price' => $filter_price,
            'filter_quantity' => $filter_quantity,
            'filter_status' => $filter_status,
            'filter_category' => $filter_category,
            'filter_upc' => $filter_upc,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );

        $this->load->model('tool/image');
        $product_total = $this->model_ebayeaxe_product->getTotalProducts($data);
        $results = $this->model_ebayeaxe_product->getProducts($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
            );

            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
            }

            $special = false;

            $product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

            foreach ($product_specials as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
                    $special = $product_special['price'];

                    break;
                }
            }

            $ebayDetails = $this->getEbayProductDetails($result['product_id']);

            $this->data['products'][] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model'],
                'price' => $result['price'],
                'upc' => $result['upc'],
                'special' => $special,
                'image' => $image,
                'quantity' => $result['quantity'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected' => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
                'action' => $action,
                'listing_status' => $ebayDetails['product_data']['listing_status'],
                'ItemID' => (isset($ebayDetails['product_data']['ItemID']) && $ebayDetails['product_data']['ItemID'] != 0) ? $ebayDetails['product_data']['ItemID'] : NULL,
                'product_errors' => $ebayDetails['product_errors']
            );
        }
//        echo '<pre>';
//        print_r($this->data['products']);
//        die;
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');

        $this->data['column_image'] = $this->language->get('column_image');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_copy'] = $this->language->get('button_copy');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_filter'] = $this->language->get('button_filter');

        $this->data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
        $this->data['sort_model'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
        $this->data['sort_price'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        $this->data['sort_quantity'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
        $this->data['sort_status'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
        $this->data['sort_order'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('ebayeaxe/listing', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_model'] = $filter_model;
        $this->data['filter_price'] = $filter_price;
        $this->data['filter_quantity'] = $filter_quantity;
        $this->data['filter_status'] = $filter_status;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;
    }

    /**
     * Function to get details of product from ebay db
     * @param int $product_id
     * @return \ArrayObject
     * @author Anik Goel
     */
    private function getEbayProductDetails($product_id) {
        $this->load->model('ebayeaxe/ebay_product');
        /* @var $ebayProductDetails ArrayObject */
        $ebayProductDetails = $this->model_ebayeaxe_ebay_product->getEbayProductDetailsfromDb($product_id);
        return $ebayProductDetails;
    }

    /**
     * Function to set language variables for bulk and individual product editing
     * page
     * @author Anik Goel
     */
    private function setLanguageForProductEditingPage() {
        $this->data['heading'] = $this->language->get('heading_title_ebay');
        $this->data['category_main'] = $this->language->get('category_main');
        $this->data['category_sub'] = $this->language->get('category_sub');
        $this->data['list_to_ebay_button'] = $this->language->get('list_to_ebay_button');
        $this->data['sub_category_default'] = $this->language->get('sub_category_default');
        $this->data['category_default'] = $this->language->get('category_default');
        $this->data['text_upload_option'] = $this->language->get('text_upload_option');
    }

    /**
     * Function to set all language variables for eBay Settings Page
     * @access private
     * @author Anik Goel
     */
    private function setLanguageForSettingsPage() {
        $this->data['heading'] = $this->language->get('heading');
        $this->data['text_credentials'] = $this->language->get('text_credentials');
        $this->data['text_dev_id'] = $this->language->get('text_dev_id');
        $this->data['text_site'] = $this->language->get('text_site');
        $this->data['text_app_id'] = $this->language->get('text_app_id');
        $this->data['text_cert_id'] = $this->language->get('text_cert_id');
        $this->data['text_token_id'] = $this->language->get('text_token_id');
        $this->data['text_save'] = $this->language->get('text_save');
        $this->data['text_dispatch_time'] = $this->language->get('text_dispatch_time');
        $this->data['text_listing_duration'] = $this->language->get('text_listing_duration');
        $this->data['text_default_country'] = $this->language->get('text_default_country');
        $this->data['text_return_accepted_options'] = $this->language->get('text_return_accepted_options');
        $this->data['text_refund_options'] = $this->language->get('text_refund_options');
        $this->data['text_return_within_options'] = $this->language->get('text_return_within_options');
        $this->data['text_return_shipping_options'] = $this->language->get('text_return_shipping_options');
        $this->data['text_paypal_address'] = $this->language->get('text_paypal_address');
        $this->data['text_postal_code'] = $this->language->get('text_postal_code');
        $this->data['text_currency'] = $this->language->get('text_currency');
        $this->data['text_general_settings'] = $this->language->get('text_general_settings');
        $this->data['text_returnpolicy_description'] = $this->language->get('text_returnpolicy_description');
    }

    /**
     * Function to upload image to ebase server
     * @param type $related_images_data
     * @param type $product_id
     * @param type $product_ebay_id
     * @auther Anil Gautam
     */
    private function buildImagesXMLUpload($related_images_data, $product_id, $product_ebay_id) {
// the call being made:
        $version = $this->compatibility_level;                          // eBay API version
        $this->related_images = array();
        $userToken = $this->cred['Setting']['user_token'];
        $related_images_data = array_reverse($related_images_data);
        foreach ($related_images_data as $single_image) {
            $image_name = $this->_getNamefromUrl($single_image['image']);
            $file = DIR_IMAGE . $single_image['image'];
            $handle = fopen($file, 'r');         // do a binary read of image
            $multiPartImageData = fread($handle, filesize($file));
            fclose($handle);
///Build the request XML request which is first part of multi-part POST
            $xmlReq = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $xmlReq .= '<' . $this->call . 'Request xmlns="urn:ebay:apis:eBLBaseComponents">' . "\n";
            $xmlReq .= "<Version>$version</Version>\n";
            $xmlReq .= "<PictureName>$image_name</PictureName>\n";
            $xmlReq .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>\n";
            $xmlReq .= '</' . $this->call . 'Request>';
            $boundary = "MIME_boundary";
            $CRLF = "\r\n";

// The complete POST consists of an XML request plus the binary image separated by boundaries
            $firstPart = '';
            $firstPart .= "--" . $boundary . $CRLF;
            $firstPart .= 'Content-Disposition: form-data; name="XML Payload"' . $CRLF;
            $firstPart .= 'Content-Type: text/xml;charset=utf-8' . $CRLF . $CRLF;
            $firstPart .= $xmlReq;
            $firstPart .= $CRLF;
            $secondPart = '';
            $secondPart .= "--" . $boundary . $CRLF;
            $secondPart .= 'Content-Disposition: form-data; name="dummy"; filename="dummy"' . $CRLF;
            $secondPart .= "Content-Transfer-Encoding: binary" . $CRLF;
            $secondPart .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
            $secondPart .= $multiPartImageData;
            $secondPart .= $CRLF;
            $secondPart .= "--" . $boundary . "--" . $CRLF;

            $fullPost = $firstPart . $secondPart;
            $response = json_decode(json_encode((array) simplexml_load_string($this->call($fullPost))), 1);

            $this->model_ebayeaxe_ebay_product->saveEbayProduct($response, $product_id, $product_ebay_id);
            if ($response['Ack'] === "Failure") {
                $this->related_images = false;
            } else {
                $this->related_images[] = $response['SiteHostedPictureDetails']['FullURL'];
            }
        }
    }

    /**
     *  Function to get name from image data
     *  @author  Anil Gautam
     *  @return  name of the image
     */
    private function _getNamefromUrl($single_image_name) {
        $name = explode("/", $single_image_name);
        $name_end = end($name);
        $name_explode = explode(".", $name_end);
        return $name_explode[0];
    }

    /**
     * Function to set header for calls
     * @param type $call_name
     * @param type $status
     * @author  Anil Gautam
     * @return  name of the image
     */
    private function _setHeader($call_name, $status) {
        $this->call = $call_name;
        $this->check_header_type_image = $status;
        $this->getHeaders();
    }

    /**
     * Function to store related images link to database
     * @param type $call_name
     * @param type $status
     * @author Anil Gautam
     * @return 
     */
    private function _saveRelatedImagesToDatabase($product_id) {
        $sort_order = 1;
        if ($this->related_images) {
            $this->model_ebayeaxe_product->deleteImagesData($product_id);
            foreach ($this->related_images as $g_imgs) {
                $sort_order++;
                $this->model_ebayeaxe_product->storeImagesData($product_id, $g_imgs, $sort_order);
            }
        }
    }

    /**
     * Function to set related images to database
     * @param type $call_name
     * @param type $status
     * @author Anil Gautam
     * @return 
     */
    private function _setRelatedImages($product_id) {
        $images_data = $this->model_ebayeaxe_product->getImagesData($product_id);
        $this->related_images = array();
        foreach($images_data as $image_group){
            $this->related_images[] = $image_group['ebay_image_link'];
        }
    }

}
