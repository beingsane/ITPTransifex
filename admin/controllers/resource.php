<?php
/**
 * @package      ITPTransifex
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.backend');

/**
 * ItpTransifex resource controller class.
 *
 * @package      ITPTransifex
 * @subpackage   Components
 * @since		1.6
 */
class ItpTransifexControllerResource extends ITPrismControllerFormBackend {

    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Resource', $prefix = 'ItpTransifexModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Save an item
     */
    public function save() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Get form data 
        $data = $app->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "id");
        
        $redirectOptions = array(
            "task" => $this->getTask(), 
            "id"   => $itemId
        );
        
        $model = $this->getModel();
        /** @var $model ItpTransifexModelResource **/
        
        $form = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if (! $form) {
            throw new Exception($model->getError(), 500);
        }
        
        // Validate form data
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
        
        try {
            
            $itemId = $model->save($validData);
            
            $redirectOptions["id"] = $itemId;
             
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_ITPTRANSIFEX_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_ITPTRANSIFEX_RESOURCE_SAVED'), $redirectOptions);
    }
}