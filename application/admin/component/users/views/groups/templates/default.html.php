<?
/**
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
?>

<!--
<script src="media://koowa/js/koowa.js" />
<style src="media://koowa/css/koowa.css" />
-->

<?= @template('com:base.view.grid.toolbar.html') ?>

<form action="" method="get" class="-koowa-grid">
    <?= @template('default_scopebar.html') ?>
    <table>
        <thead>
            <tr>
                <th width="1">
                    <?= @helper('grid.checkall'); ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'Name')) ?>
                </th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="2">
                    <?= @helper('paginator.pagination', array('total' => $total)) ?>
                </td>
            </tr>
        </tfoot>

        <tbody>
            <? foreach($groups as $group) : ?>
                <tr>
                    <td align="center">
                        <?= @helper('grid.checkbox', array('row' => $group)) ?>
                    </td>
                    <td>
                        <a href="<?= @route('view=group&id='.$group->id) ?>">
                            <?= @escape($group->name) ?>
                        </a>
                    </td>
                </tr>
            <? endforeach ?>
       </tbody>
    </table>
</form>