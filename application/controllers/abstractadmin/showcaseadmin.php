<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:24 AM
 */


require_once(__DIR__ . "/lightspeedconsole.php");

abstract class Showcaseadmin extends Lightspeedconsole
{
    protected $_crsDataStructure;
    protected $_showcaseConfigs;

    protected function showcase_enforce_access() {
        $this->_crsDataStructure = getCRSStructure();
        if ((!$this->checkValidAccess('coupons') && !@$_SESSION['userRecord']['admin']) || $this->_crsDataStructure === FALSE) {
            redirect('mInventory');
            exit();
        }

        $this->_showcaseConfigs = array(
            "showcase_enable" => 1,
            "showcase_include_in_footer" => 1
        );
    }

    protected function _getPageByTag($tag, $default_label, $default_title) {
        global $PSTAPI;
        initializePSTAPI();
        $showcase_page = $PSTAPI->pages()->fetch(array(
            "tag" => $tag
        ));

        if (count($showcase_page) > 0) {
            return $showcase_page[0];
        } else {
            return $PSTAPI->pages()->add(array(
                "tag" => $tag,
                "label" => $default_label,
                "title" => $default_title
            ));
        }
    }

    /*
     * Settings for the showcase - where we start:
     * - Enable showcase yes/no
     * - Include showcase in footer navigation yes/no - it would be right next to the site map.
     * - click to button - to go edit the page.
     */

    public function showcase_settings() {
        $this->showcase_enforce_access();

        // We need some settings - e.g., we have to have a lightspeed login set, lightspeed has to be enabled, and then we need a little control for the mode when lightspeed parts come in as active or inactive by default....

        global $PSTAPI;
        initializePSTAPI();

        // get the settings.
        foreach ($this->_showcaseConfigs as $key => $default) {
            $this->_mainData[$key] = $PSTAPI->config()->getKeyValue($key, $default);
        }

        // We have to make the page if  it does not exist...and since they can edit them,
        $this->_mainData["showcase_page"] = $this->_getPageByTag("showcase_page", "Unit Showcase", "Unit Showcase");


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/showcase/settings_v', $this->_mainData);
    }

    public function showcase_settings_save() {
        global $PSTAPI;
        initializePSTAPI();

        // get the settings.
        foreach ($this->_showcaseConfigs as $key => $default) {
            $PSTAPI->config()->setKeyValue($key, array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default);
        }

        $_SESSION["showcase_settings"] = "Settings updated successfully.";
        header("Location: " . site_url("admin/showcase_settings"));
    }

    public function showcase_makes() {

    }


    // The purpose of this is to show basic settings...
    public function showcase_make($crs_make_id) {

    }

    // Save the settings. Basically, you'll probably be saving a grid of years at the start to get this moving.
    // You'll also have the opportunity of including a description on the page...
    public function showcase_make_save($crs_make_id) {

    }


    // The purpose of this is to remove anything related to this make. They should probably be prompted to confirm it.
    public function showcase_exclude_make($crs_make_id) {

    }

}