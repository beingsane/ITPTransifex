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
<?php if(!empty($this->items)) {?>
<table class="table table-striped" id="resourcesList">
    <tbody>
        <?php foreach ($this->items as $i => $item) { ?>
        <tr>
            <td class="nowrap">
                <a href="<?php echo JRoute::_("index.php?option=com_itptransifex&view=resources&id=".(int)$this->projectId."&filter_search=id:".(int)$item["id"]);?>" >
                    <?php echo $this->escape($item["name"]); ?>
                </a>
                <div class="small">
                    <?php echo JText::sprintf("COM_ITPTRANSIFEX_ALIAS_S", $item["alias"]); ?>
                </div>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php }?>