<div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a> &gt; Delete</p></div>
<h3>Delete Event</h3>
<?php if (!empty ($delete_event)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Are you sure you want to delete the event: <?php echo full_escape ($delete_event->title) ?></p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <li><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <p>Event not found</p>
<?php endif ?>
