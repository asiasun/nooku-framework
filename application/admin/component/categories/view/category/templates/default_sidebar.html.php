<?
/**
 * @package     Nooku_Server
 * @subpackage  Categories
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
?>

<fieldset>
    <legend><?= @text( 'Publish' ); ?></legend>
    <div>
        <label for="published"><?= @text('Published') ?></label>
        <div>
            <input type="checkbox" name="published" value="1" <?= $category->published ? 'checked="checked"' : '' ?> />
        </div>
    </div>
    <div>
        <label for="access"><?= @text('Registered') ?></label>
        <div>
            <input type="checkbox" name="access" value="1" <?= $category->access ? 'checked="checked"' : '' ?> />
        </div>
    </div>
</fieldset>
<? if($state->table == 'articles') : ?>
    <fieldset class="categories group">
        <legend><?= @text('Parent') ?></legend>
        <div>
            <?= @helper('com:categories.listbox.categories', array(
                'name'      => 'parent_id',
                'selected'  => $category->parent_id,
                'prompt'    => '- None -',
                'max_depth' => 1,
                'table'     => 'articles',
                'parent'	=> '0'
            )) ?>
            <?= @template('com:categories.view.categories.radiolist.html', array(
                'categories' =>  @object('com:articles.model.categories')->sort('title')->table('articles')->parent('0')->getRowset(),
                'selected' => $category->parent_id))
            ?>

        </div>
    </fieldset>
<? endif ?>

<? if($category->isAttachable()) : ?>
<fieldset>
    <legend><?= @text('Image') ?></legend>
    <? if (!$category->isNew()) : ?>
        <?= @template('com:attachments.view.attachments.list.html', array('attachments' => $category->getAttachments(), 'attachments_attachment_id' => $category->attachments_attachment_id)) ?>
    <? endif ?>
    <?= @template('com:attachments.view.attachments.upload.html') ?>
</fieldset>
<? endif ?>