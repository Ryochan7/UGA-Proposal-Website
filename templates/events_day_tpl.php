<h3>Events on <?php echo strftime ("%B %d, %Y", $start) ?></h3>
<?php if ($current_platform):?><h3>Filtered by platform: <?php echo $current_platform->name ?></h3><?php endif ?>
<?php if (!empty ($event_array)):?>
    <?php foreach ($event_array as $event):?>
        <?php include (joinPath ("fragments", "event_mindisplay_tpl.php")) ?>
    <?php endforeach ?>
<?php else: ?>
<p>No event founds for this day</p>
<?php endif ?>
<?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
<div style="margin-top: 10px;"></div>

<?php if (!empty ($prev_eventday)):?>
<a href="events_day.php?<?php echo strftime ("month=%m&amp;day=%d&amp;year=%Y", $prev_eventday->date); if ($current_platform) { echo "&amp;platform={$current_platform->id}"; } ?>">&lt; <?php echo strftime ("%B %d, %Y", $prev_eventday->date) ?></a>
<?php endif ?>
<?php if (!empty ($next_eventday)):?>
<a style="float: right;" href="events_day.php?<?php echo strftime ("month=%m&amp;day=%d&amp;year=%Y", $next_eventday->date); if ($current_platform) { echo "&amp;platform={$current_platform->id}"; } ?>"><?php echo strftime ("%B %d, %Y", $next_eventday->date) ?> &gt;</a>
<?php endif ?>
<div style="clear: both"></div>
<p style="text-align: center;">
    <?php if ($current_platform):?><a href="events_day.php?<?php echo strftime ("month=%m&amp;day=%d&amp;year=%Y", $start); ?>">Reset Platform</a>&nbsp;&nbsp;<?php endif ?>
    <a href="events_day.php">Events Day Home</a>
</p>
