<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/15/18
 * Time: 8:10 PM
 */

require_once(__DIR__ . "/individualpageadmin.php");

abstract class Lightspeedconsole extends Individualpageadmin {


    public function view_lightspeedconsole() {
        if (!$this->checkValidAccess('lightspeedconsole') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->setNav('admin/nav_v', 0);
        global $PSTAPI;
        initializePSTAPI();
        $this->_mainData['lightspeed_feed_username'] = $PSTAPI->config()->getKeyValue('lightspeed_feed_username', '');
        $this->_mainData['lightspeed_feed_password'] = $PSTAPI->config()->getKeyValue('lightspeed_feed_password', '');
        $this->renderMasterPage('admin/master_v', 'admin/lightspeedconsole_v', $this->_mainData);
    }

}
