<?php if (!empty ($event_array)):?>
    <h3>Latest Events</h3>
    <?php if ($current_platform):?><h3>Filtered by platform: <?php echo $current_platform->name ?></h3><?php endif ?>    

    <?php foreach ($event_array as $event):?>
        <?php include (joinPath ("fragments", "event_mindisplay_tpl.php")) ?>
    <?php endforeach ?>
    <?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
<?php else: ?>
    <h3>No events found</h3>
<?php endif ?>
<p style="text-align: center;"><a href="event_list.php">Event List Home</a></p>
