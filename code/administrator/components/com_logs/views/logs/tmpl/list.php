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
foreach ($logs as $log) {
	$list[substr($log->created_on, 0, 10)][] = $log;
}
?>

<div id="logs-list">
	<? foreach($list as $date => $logs) : ?>
		<h4><?= @helper('date.humanize', array('date' => $date)) ?></h4>
		<div class="activities">
			<? foreach($logs as $log) : ?>
			<div class="activity">
				<span class="icon icon-16-<?= $log->action ?>"></span>
				<?= @helper('com://admin/logs.template.helper.message.build', array('row' => $log)) ?>
				<span class="info">
					<small><?= $log->package.' - '.$log->name ?> | <?= date("H:i", strtotime($log->created_on)) ?></small>
				</span>
			</div>
			<? endforeach ?>
		</div>
	<? endforeach ?>
</div>