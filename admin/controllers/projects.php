<?php
/**
 * @package      ITPTransifex
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * ItpTransifex Projects Controller
 *
 * @package     ItpTransifex
 * @subpackage  Components
 */
class ItpTransifexControllerProjects extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'ItpTransifexModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function update()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get form data
        $pks   = $this->input->post->get('cid', array(), 'array');
        $model = $this->getModel();
        /** @var $model ItpTransifexModelProject */

        $redirectOptions = array(
            "view" => $this->view_list
        );

        JArrayHelper::toInteger($pks);

        // Check for validation errors.
        if (empty($pks)) {
            $this->displayWarning(JText::_("COM_ITPTRANSIFEX_INVALID_ITEM"), $redirectOptions);

            return;
        }

        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        $options = array(
            "username" => $params->get("username"),
            "password" => $params->get("password"),
            "url"      => $params->get("api_url") . "project",
        );

        try {

            $model->synchronize($pks, $options);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_ITPTRANSIFEX_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::plural('COM_ITPTRANSIFEX_PROJECTS_SYNCHRONIZED', count($pks)), $redirectOptions);
    }

    /**
     * Remove records which have been connected with a project.
     *
     * @param ItpTransifexModelProject $model
     * @param array        $cid
     *
     * @throws Exception
     */
    protected function postDeleteHook(ItpTransifexModelProject $model, $cid = null)
    {
        try {

            $model->removePackages($cid);
            $model->removeResources($cid);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_ITPTRANSIFEX_ERROR_SYSTEM'));
        }

    }
}
