<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */
?>

<style src="media://terms/css/admin.css" />

<form action="" method="post" class="-koowa-grid">
<div style="margin-bottom: 25px">
	<div style="float: left">
		<?= @helper( 'grid.search'); ?>
	</div>
</div>


<table class="adminlist" style="clear: both;">
<thead>
	<tr>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?= count($terms); ?>);" />
		</th>
		<th>
			<?= @helper('grid.sort', array('column' => 'title')); ?>
		</th>
		<th>
			<?= @helper('grid.sort', array('column' => 'slug')); ?>
		</th>
		<th>
			<?= @helper('grid.sort', array('column' => 'count')); ?>
		</th>
	</tr>
	<tr>
		<td colspan="2">
			<?= @text('Filters'); ?>	
		</td>
		<td>
		</td>
	</tr>
</thead>
<tbody>
<? if (count($terms)) : ?>
	<?= @template('default_terms.html'); ?>
<? else : ?>
	<tr>
		<td colspan="8" align="center">
			<?= @text('No items found'); ?>
		</td>
	</tr>
<? endif; ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="20">
			<?= @helper('paginator.pagination', array('total' => $total)) ?>
		</td>
	</tr>
</tfoot>
</table>
</form>