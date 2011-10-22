<?php
/**
 * @version     $Id$
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Logs
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

defined('KOOWA') or die('Restricted access') ?>

<style src="media://com_logs/css/logs-list.css" />

<?
foreach ($activities as $activity) {
	$list[substr($activity->created_on, 0, 10)][] = $activity;
}
?>

<div id="logs-list">
	<? foreach($list as $date => $activities) : ?>
		<h4><?= @helper('date.humanize', array('date' => $date)) ?></h4>
		<div class="activities">
			<? foreach($activities as $activity) : ?>
			<div class="activity">
				<span class="icon icon-16-<?= $activity->action ?>"></span>
				<?= @helper('com://admin/activities.template.helper.message.build', array('row' => $activity)) ?>
				<span class="info">
					<small><?= $activity->package.' - '.$activity->name ?> | <?= date("H:i", strtotime($activity->created_on)) ?></small>
				</span>
			</div>
			<? endforeach ?>
		</div>
	<? endforeach ?>
</div>