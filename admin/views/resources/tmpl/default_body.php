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
?>
<?php foreach ($this->items as $i => $item) {?>
	<tr class="row<?php echo $i % 2; ?>">
        <td class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'resources.'); ?>
		</td>
        <td class="nowrap has-context">
            <a href="<?php echo JRoute::_('index.php?option=com_itptransifex&view=resource&layout=edit&id='.(int)$item->id);?>">
                <span id="js-resource-name-<?php echo $item->id; ?>"><?php echo $this->escape($item->name); ?></span>
            </a>
            <?php if ($item->source) {?>
                <div class="small">
                    <?php echo JText::sprintf('COM_ITPTRANSIFEX_SOURCE_FOLDER') . ': <i>'.$this->escape($item->source).'</i>'; ?>
                </div>
            <?php } ?>
            <?php if ($item->path) {?>
            <div class="small">
                <?php echo JText::sprintf('COM_ITPTRANSIFEX_TARGET_PATH') . ': <i>'.$this->escape($item->path).'</i>'; ?>
            </div>
            <?php } ?>
        </td>
        <td class="nowrap hidden-phone">
		    <?php echo $this->escape($item->alias); ?>
		</td>
		<td class="nowrap hidden-phone">
		    <span class="js-editable-filename" data-pk="<?php echo $item->id; ?>">
                <?php echo $this->escape($item->filename); ?>
            </span>
		</td>
		<td class="nowrap hidden-phone">
            <span class="js-editable-type" data-pk="<?php echo $item->id; ?>">
		    <?php echo $this->escape($item->category); ?>
            </span>
		</td>
		<td class="nowrap center hidden-phone">
		  <?php echo $this->escape($item->source_language_code); ?>
		</td>
        <td class="nowrap center hidden-phone">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php }?>