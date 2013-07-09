<?php
/**
 * @package        Nooku_Server
 * @subpackage     Articles
 * @copyright      Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */
?>

<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.keepalive'); ?>

<!--
<script src="media://js/koowa.js"/>
-->

<div class="btn-toolbar">
    <?= @helper('com:base.toolbar.render', array('toolbar' => $toolbar));?>
</div>

<form method="post" action="" class="-koowa-form form-horizontal">
    <input type="hidden" name="published" value="0" />
    <input type="hidden" name="access" value="0" />
    
    <fieldset>
        <input class="input-block-level" type="text" name="title" maxlength="100" value="<? echo @escape($article->title); ?>" style="margin-bottom: 10px"/>
        <?= @object('com:wysiwyg.controller.editor')->render(array('name' => 'text', 'text' => $article->text)) ?>
    </fieldset>
    <fieldset>
        <legend><?= @text('Publishing'); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"><?= @text('Published'); ?></label>
            <div class="controls">
                <input type="checkbox" name="published" value="1" <?= $article->published ? 'checked="checked"' : '' ?> />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="access"><?= @text('Registered'); ?></label>
            <div class="controls">
                <input type="checkbox" name="access" value="1" <?= $article->access ? 'checked="checked"' : '' ?> />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="publish_on"><?= @text('Publish on'); ?></label>
            <div class="controls">
                <input type="datetime-local" name="publish_on" value="<?= $article->publish_on ?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="unpublish_on"><?= @text('Unpublish on'); ?></label>
            <div class="controls">
                <input type="datetime-local" name="unpublish_on" value="<?= $article->unpublish_on ?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="categories_category_id"><?= @text('Category'); ?></label>
            <div class="controls">
                <?= @template('com:categories.view.categories.radiolist.html', array(
                    'categories' =>  @object('com:articles.model.categories')->sort('title')->table('articles')->getRowset(),
                    'selected' => $article->categories_category_id,
                    'uncategorised' => true))
                ?>
            </div>
        </div>
    </fieldset>
</form>