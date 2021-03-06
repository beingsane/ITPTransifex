<?php
/**
 * @package      ITPTransifex
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Install script file of the component.
 */
class pkg_itpTransifexInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined("ITPTRANSIFEX_PATH_COMPONENT_ADMINISTRATOR")) {
            define("ITPTRANSIFEX_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_itptransifex");
        }

        // Register Install Helper
        JLoader::register("ItpTransifexInstallHelper", ITPTRANSIFEX_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "install.php");

        jimport('Prism.init');
        jimport('Transifex.init');

        $params = JComponentHelper::getParams("com_itptransifex");
        /** @var $params Joomla\Registry\Registry */

        // Prepare images folders.
        $imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/itptransifex"));

        // Create images folder.
        $imagesPath   = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $imagesFolder);
        if (!is_dir($imagesPath)) {
            ItpTransifexInstallHelper::createFolder($imagesPath);
        }

        // Start table with the information
        ItpTransifexInstallHelper::startTable();

        // Requirements
        ItpTransifexInstallHelper::addRowHeading(JText::_("COM_ITPTRANSIFEX_MINIMUM_REQUIREMENTS"));

        // Display result about verification for existing folder
        $title = JText::_("COM_ITPTRANSIFEX_IMAGE_FOLDER");
        $info  = $imagesFolder;
        if (!is_dir($imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writable folder
        $title = JText::_("COM_ITPTRANSIFEX_WRITABLE_FOLDER");
        $info  = $imagesFolder;
        if (!is_writable($imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for GD library
        $title = JText::_("COM_ITPTRANSIFEX_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_ITPTRANSIFEX_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_ITPTRANSIFEX_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_ITPTRANSIFEX_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_ITPTRANSIFEX_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_ITPTRANSIFEX_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_ITPTRANSIFEX_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_("COM_ITPTRANSIFEX_FILEINFO");
        $info  = "";
        if (!function_exists('finfo_open')) {
            $info   = JText::_("COM_ITPTRANSIFEX_FILEINFO_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP version.
        $title = JText::_("COM_ITPTRANSIFEX_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_ITPTRANSIFEX_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed ITPrism Library
        $title = JText::_("COM_ITPTRANSIFEX_PRISM_LIBRARY");
        $info  = "";
        if (!class_exists("Prism\\Version")) {
            $info   = JText::_("COM_ITPTRANSIFEX_PRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        ItpTransifexInstallHelper::addRow($title, $result, $info);

        // End table with the information
        ItpTransifexInstallHelper::endTable();

        echo JText::sprintf("COM_ITPTRANSIFEX_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_itptransifex"));

        if (!class_exists("Prism\\Version")) {
            echo JText::_("COM_ITPTRANSIFEX_MESSAGE_INSTALL_PRISM_LIBRARY");
        } else {

            if (class_exists("Transifex\\Version")) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Transifex\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, "<")) {
                    echo JText::_("COM_ITPTRANSIFEX_MESSAGE_INSTALL_PRISM_LIBRARY");
                }
            }
        }
    }
}
