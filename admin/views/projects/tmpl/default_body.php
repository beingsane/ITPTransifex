<?php
/**
 * @package      ITPTransifex
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $resourceData = JArrayHelper::getValue($this->resources, $item->id, 0);
    $numberOfResources = (!empty($resourceData)) ? $resourceData->number : 0;
?>
	<tr class="row<?php echo $i % 2; ?>">
        <td class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="nowrap">
            <a href="<?php echo JRoute::_("index.php?option=com_itptransifex&view=project&layout=edit&id=".(int)$item->id);?>"><?php echo $this->escape($item->name); ?></a>
            <div class="small">
            <a href="<?php echo JRoute::_("index.php?option=com_itptransifex&view=resources&layout=edit&id=".(int)$item->id);?>"> <?php echo JText::sprintf("COM_ITPTRANSIFEX_RESOURCES_D", $numberOfResources)?></a>
            </div>
        </td>
		<td class="nowrap hidden-phone">
		    <?php echo $this->escape($item->alias); ?>
		</td>
		<td class="nowrap hidden-phone">
		    <?php echo $this->escape($item->filename); ?>
		</td>
		<td class="nowrap center hidden-phone">
		  <?php 
	      if(!$numberOfResources) {?>
		      (0)
	      <?php } else { ?>
		      <a href="<?php echo JRoute::_("index.php?option=com_itptransifex&view=resources&layout=edit&id=".(int)$item->id);?>">(<?php echo $resourceData->number; ?>)</a>
	      <?php }?>
		</td>
        <td class="nowrap center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  