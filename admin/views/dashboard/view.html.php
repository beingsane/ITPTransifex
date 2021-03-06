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

class ItpTransifexViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $version;

    protected $option;

    protected $sidebar;

    protected $prismVersion;
    protected $prismVersionLowerMessage;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->version = new Transifex\Version();

        // Load Prism library version
        if (!class_exists("Prism\\Version")) {
            $this->prismVersion = JText::_("COM_ITPTRANSIFEX_PRISM_LIBRARY_DOWNLOAD");
        } else {
            $prismVersion       = new Prism\Version();
            $this->prismVersion = $prismVersion->getShortVersion();

            if (version_compare($this->prismVersion, $this->version->requiredPrismVersion, "<")) {
                $this->prismVersionLowerMessage = JText::_("COM_ITPTRANSIFEX_PRISM_LIBRARY_LOWER_VERSION");
            }
        }

        // Add submenu
        ItpTransifexHelper::addSubmenu($this->getName());

        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_("COM_ITPTRANSIFEX_DASHBOARD"));

        JToolBarHelper::preferences('com_itptransifex');
        JToolBarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_ITPTRANSIFEX_HELP_URL'));

    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_ITPTRANSIFEX_DASHBOARD_META_TITLE'));
    }
}
