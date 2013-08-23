<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */
?>

<div id="activities-list">
    <? if(count($activities)) : ?>
    <? foreach ($activities as $activity) : ?>
       <? $list[substr($activity->created_on, 0, 10)][] = $activity; ?>
    <? endforeach; ?>

    <? foreach($list as $date => $activities) : ?>
        <h4><?= helper('date.humanize', array('date' => $date)) ?></h4>
        <? foreach($activities as $activity) : ?>
        <div class="activity">
            <i class="icon-<?= $activity->action ?>"></i>
            <?= helper('activity.message', array('row' => $activity)) ?>
            <span class="activity__info">
                <?= $activity->package.' - '.$activity->name ?> | <?= date(array('format' => "H:i", 'date' =>  $activity->created_on)) ?>
            </span>
        </div>
        <? endforeach ?>
    <? endforeach ?>
	<? endif; ?>
</div>