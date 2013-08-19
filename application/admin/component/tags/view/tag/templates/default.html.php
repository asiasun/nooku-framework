<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>

<!--
<script src="media://js/koowa.js" />
<style src="media://css/koowa.css" />
-->

<ktml:module position="actionbar">
    <?= @helper('actionbar.render', array('actionbar' => $actionbar))?>
</ktml:module>

<form action="" method="post" class="-koowa-form" id="tag-form">
    <input type="hidden" name="table" value="<?= $state->table ?>" />
    
    <div class="main">
		<div class="title">
			<input class="required" type="text" name="title" maxlength="255" value="<?= $tag->title; ?>" placeholder="<?= @text( 'Title' ); ?>" />
		    <div class="slug">
		        <span class="add-on"><?= @text('Slug'); ?></span>
		        <input type="text" name="slug" maxlength="255" value="<?= $tag->slug ?>" />
		    </div>
		</div>

		<div class="scrollable">

		</div>
	</div>
</form>