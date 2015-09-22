<?php
/**
 * @package      ItpTransifex
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class ItpTransifexModelImport extends JModelForm
{
    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.import', 'import', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.import.data', array());

        return $data;
    }

    public function extractFile($file, $destFolder)
    {
        $filePath = "";

        // extract type
        $zipAdapter = JArchive::getAdapter('zip');
        $zipAdapter->extract($file, $destFolder);

        $dir = new DirectoryIterator($destFolder);

        $fileName = JFile::stripExt(basename($file));

        /** @var $fileinfo object */
        foreach ($dir as $fileinfo) {

            $currentFileName = JFile::stripExt($fileinfo->getFilename());

            if (!$fileinfo->isDot() and strcmp($fileName, $currentFileName) == 0) {
                $filePath = $destFolder . DIRECTORY_SEPARATOR . JFile::makeSafe($fileinfo->getFilename());
                break;
            }

        }

        return $filePath;
    }

    /**
     * Import project from XML file.
     * The XML file is generated by the current extension ( ItpTransifex )
     *
     * @param string $file    A path to file
     * @param bool   $override Reset existing IDs with new ones.
     */
    public function importProject($file, $override = false)
    {
        $xmlstr  = file_get_contents($file);
        $content = new SimpleXMLElement($xmlstr);
        /** @var $content object */

        if (!empty($content)) {

            // Create a project record.
            $keys = array(
                "alias" => (string)$content->alias
            );

            // Get a project by its alias.
            $project = new Transifex\Project\Project(JFactory::getDbo());
            $project->load($keys);

            // Create a project.
            if (!$project->getId()) {

                $project
                    ->setName((string)$content->name)
                    ->setAlias((string)$content->alias)
                    ->setDescription((string)$content->description)
                    ->setFileName((string)$content->filename)
                    ->setLanguage((string)$content->source_language_code);

                $project->store();
            }

            // Import the packages.
            $packagesMap = $this->importPackages($content, $project->getId(), $override);

            // Import resources
            $packagesMap = $this->importResources($content, $project->getId(), $packagesMap, $override);

            // Insert packages map records.
            $this->insertPackagesMap($packagesMap);
            
        }
    }

    protected function importPackages($content, $projectId, $override = false)
    {
        $db = JFactory::getDbo();

        $packagesMap = array();
        // Generate data for importing.
        foreach ($content->package as $item) {

            $alias = JString::trim($item->alias);
            if (!$alias) {
                continue;
            }

            $keys = array("alias" => $alias);
            $package = new Transifex\Package\Package($db);
            $package->load($keys);

            if (!$package->getId() or ($package->getId() and $override)) {

                $package->setName(JString::trim($item->name));
                $package->setAlias($alias);
                $package->setDescription(JString::trim($item->description));
                $package->setFileName(JString::trim($item->filename));
                $package->setVersion(JString::trim($item->version));
                $package->setLanguage(JString::trim($item->language));
                $package->setType(JString::trim($item->type));
                $package->setProjectId($projectId);

                $package->store();

                $packagesMap[$alias]  = array(
                    "package_id" => $package->getId(),
                    "resources"  => array()
                );

            }
        }

        return $packagesMap;
    }

    protected function importResources($content, $projectId, $packagesMap, $override = false)
    {
        $db = JFactory::getDbo();

        // Generate data for importing.
        foreach ($content->resource as $item) {

            $alias        = JString::trim($item->alias);
            $packageAlias = JString::trim($item->package_alias);
            if ((!$alias or !$packageAlias) or !isset($packagesMap[$packageAlias])) {
                continue;
            }

            $keys = array("alias" => $alias);
            $resource = new Transifex\Resource\Resource($db);
            $resource->load($keys);

            // Create new resource.
            if (!$resource->getId() or ($resource->getId() and $override)) {

                $resource->setName(JString::trim($item->name));
                $resource->setAlias($alias);
                $resource->setFileName(JString::trim($item->filename));
                $resource->setType(JString::trim($item->type));
                $resource->setI18nType(JString::trim($item->i18n_type));
                $resource->setSourceLanguageCode(JString::trim($item->source_language_code));
                $resource->setState(JString::trim($item->published));
                $resource->setProjectId($projectId);

                $resource->store();
            }

            $packagesMap[$packageAlias]["resources"][] = $resource->getId();
        }

        return $packagesMap;
    }
    
    protected function insertPackagesMap($packagesMap)
    {
        $db = JFactory::getDbo();

        foreach ($packagesMap as $map) {

            $packageId = Joomla\Utilities\ArrayHelper::getValue($map, "package_id", 0, "int");
            $resources = Joomla\Utilities\ArrayHelper::getValue($map, "resources", array(), "array");
            
            if (!$packageId or !$resources) {
                continue;
            }

            foreach ($resources as $resourceId) {

                // Check for existing record.
                $query = $db->getQuery(true);

                $query
                    ->select("COUNT(*)")
                    ->from($db->quoteName("#__itptfx_packages_map", "a"))
                    ->where("a.package_id = " .(int)$packageId)
                    ->where("a.resource_id = " .(int)$resourceId);

                $db->setQuery($query, 0, 1);

                $result = $db->loadResult();

                // Insert packages map records
                if (!$result) {
                    $query = $db->getQuery(true);

                    $query
                        ->insert($db->quoteName("#__itptfx_packages_map"))
                        ->set($db->quoteName("package_id") ." = " .(int)$packageId)
                        ->set($db->quoteName("resource_id") ." = " .(int)$resourceId);

                    $db->setQuery($query);

                    $db->execute();
                }
            }
        }
        
    }
}
