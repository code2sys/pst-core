<?php

require_once(APPPATH . 'controllers/Master_Controller.php');
class Vault extends Master_Controller {

    // private $_pageId = TOP_LEVEL_PAGE_ID_DIRT;
    // private $_categoryId= TOP_LEVEL_CAT_DIRT_BIKES;

    private $_pageId = TOP_LEVEL_PAGE_ID_VAULT;


    function __construct()
    {
        parent::__construct();
        $this->load->model('pages_m');
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
        $title = STYLED_HOSTNAME . " Vault Gallery";
        $this->setMasterPageVars('title', $title);
        unset($_SESSION['search']);
        //$this->output->enable_profiler(TRUE);
    }

    public function index()
    {
        print "A\n";
        exit();

        $this->_mainData['pageRec'] = $this->pages_m->getPageRecByTag($this->_pageId);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->setMasterPageVars('metatag', html_entity_decode($this->_mainData['pageRec']['metatags']));
        $this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
        $this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));

        $_SESSION['url'] = 'vault/';

        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_pageId);
        $notice = $this->pages_m->getTextBoxes($this->_pageId);

        $this->_mainData['notice'] = $notice[0]['text'];
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', $this->_mainData['pageRec']['title']);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_pageId, $this->_mainData['pageRec']);
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'comp_info');

        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');

        $this->_mainData['new_header']  = 1;
        $this->_mainData['image']  = $this->pages_m->getVaultImages();

        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');

        $this->setFooterView('master/footer_v.php');
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/master_v', 'vault/vault_gallery', $this->_mainData);
    }
}
