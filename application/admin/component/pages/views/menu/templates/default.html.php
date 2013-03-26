<?
/**
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
?>

<?= @helper('behavior.validator') ?>

<!--
<script src="media://koowa/js/koowa.js" />
-->

<ktml:module position="toolbar">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</ktml:module>

<form action="<?= @route('&id='.$menu->id)?>" method="post" class="-koowa-form">
    <input type="hidden" name="application" value="site" />
    
    <div class="main">
        <div class="title">
            <input class="required" type="text" name="title" maxlength="255" value="<?= $menu->title ?>" placeholder="<?= @text('Title') ?>" />
        </div>
        <div class="scrollable">
            <label for="name"><?= @text('Slug') ?>:</label>
            <input type="text" name="slug" size="30" maxlength="25" value="<?= $menu->slug ?>" />

            <label for="description"><?= @text('Application') ?>:</label>
            <?= @helper('com:application.listbox.applications', array('selected' => $menu->isNew() ? $state->application : $menu->application)) ?>
            
            <label for="description"><?= @text('Description') ?>:</label>
            <textarea name="description" rows="3" placeholder="<?= @text('Description') ?>" maxlength="255"><?= $menu->description ?></textarea>
        </div>
    </div>
</form>
