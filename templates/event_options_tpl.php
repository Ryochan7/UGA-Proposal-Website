<?php if (!empty ($event_array) && strcmp ($action, "delete") == 0):?>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a> &gt; Delete Group</p></div>
    <h3>Event Options</h3>
    <p>Do you want to delete the following events?</p>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <?php foreach ($event_array as $event):?>
        <p>ID <?php echo $event->id ?>: <?php echo $event->title ?></p>
        <input type="hidden" name="ids[]" value="<?php echo $event->id ?>" />
    <?php endforeach ?>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Delete" />
    </form>

<?php elseif (!empty ($event_array)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_event.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a></p></div>
    <h3>Event Options</h3>
    <div style="clear: both;"></div>
    <p>List of events</p>
    <form action="" method="get">
    <p>Actions:
    <select id="action" name="action" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="delete">Delete</option>
    </select>
    <input type="submit" name="doaction" value="Submit" /><br/>
    Alter Event Status:
    <select id="status" name="status" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="<?php echo Event::PENDING_STATUS ?>">Pending</option>
        <option value="<?php echo Event::APPROVED_STATUS ?>">Approved</option>
        <option value="<?php echo Event::DENIED_STATUS ?>">Denied</option>
    </select>
    <input type="submit" name="domodstatus" value="Submit" />
    </p>

    <table cellspacing="0" class="list">
        <thead><tr><th></th><th></th><th></th><th>Id</th><th>Title</th><th>Posted by</th><th>Sanctioned</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php $i = 0; foreach ($event_array as $event):?>
           <tr<?php if ($i % 2 == 0) echo " class=\"row1\"";?>>
<td><input type="checkbox" name="ids[]" id="page<?php echo $i ?>" value="<?php echo $event->id ?>" /></td>
<td><a href="<?php echo generate_link_url ("edit_event.php?id={$event->id}") ?>">Edit</a></td>
<td><a href="<?php echo generate_link_url ("delete_event.php?id={$event->id}") ?>">Delete</a></td>
<td><a href="<?php echo generate_link_url ($event->getAbsoluteUrl ()) ?>"><?php echo $event->id ?></a></td>
<td><?php echo full_escape ($event->title) ?></td>
<td><a href="<?php echo generate_link_url ("view_profile.php?id={$event->user->id}") ?>"><?php echo full_escape ($event->user->userName) ?></a></td>
<td><?php if ($event->sanctioned) {echo "True";} else {echo "False";} ?></td>
<td>
<?php switch ($event->status) {
    case Event::PENDING_STATUS:
        echo "Pending";
        break;
    case Event::APPROVED_STATUS:
        echo "Approved";
        break;
    case Event::DENIED_STATUS:
        echo "Denied";
        break;
    default:
        echo "Unknown";
}?>
</td>
<td><?php echo date ("M d, Y", $event->date) ?></td>
</tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
    </form>
    <?php include (joinPath ("fragments", "pagination_tpl.php"));?>
<?php elseif (strcmp ($action, "delete") == 0): ?>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a></p></div>
    <h3>No events selected</h3>
    <p>No events chosen for deletion</p>
<?php else: ?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_event.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a></p></div>
    <h3>Event Options</h3>
    <div style="clear: both;"></div>
    <p>No events exist</p>
<?php endif ?>
