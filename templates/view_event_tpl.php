<?php if (!empty ($event)): ?>
    <h3 class="title"><?php echo full_escape ($event->title) ?></h3>
    <p class="credit">Posted by: <a href="<?php echo full_escape ($event->user->getAbsoluteUrl ()) ?>"><?php echo $event->user->userName ?></a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_event.php?id={$event->id}") ?>">Edit</a><?php endif ?></p>
    <?php if (empty ($attending) && $session->getUser () && $session->getUser ()->validUser ()):?>
        <form action="mark_attendance.php" method="post"><p><input type="hidden" name="eventid" id="eventid" value="<?php echo $event->id ?>" /><input type="submit" value="Mark as Attending" /></p></form>
    <?php elseif (!empty ($attending) && $session->getUser () && $session->getUser ()->validUser ()):?>
        <form action="mark_attendance.php" method="post"><p><input type="hidden" name="eventid" id="eventid" value="<?php echo $event->id ?>" /><input type="hidden" name="action" id="action" value="remove" /><input type="submit" value="Remove from Attendance" /></p></form>
    <?php endif ?>
    <p>Date: <?php echo $event->dateString ?></p>
    <p>Platform Choice: <a href="<?php echo generate_link_url ("event_list.php?platform={$event->platformId}") ?>">

<?php echo $event->platform->name ?></a>
</p>
    <p>Official Event: <?php if ($event->sanctioned == true) { echo "Yes";} else { echo "No"; } ?></p>
    <p>Number Attending: <?php echo $attend_count ?></p>
    <?php if (!empty ($attend_array)):?><p>People Attending: <?php for ($i = 0, $length = count ($attend_array); $i < $length; $i++):?><a href="<?php echo generate_link_url ($attend_array[$i]->user->getAbsoluteUrl ()) ?>"><?php echo full_escape ($attend_array[$i]->user->userName) ?></a><?php if ($i < $length-1):?>, <?php endif ?><?php endfor ?></p><?php endif ?>
    <p style="float: left; margin-top: 0px;">Description: </p><p style="float: left; margin-left: 10px; margin-top: 0px; width: 575px;"><?php echo nl2br (full_escape ($event->description)) ?></p><div style="clear: both;"></div>
<?php else: ?>
    <h3>Not Found</h3>
    <p>Event could not be found.</p>
<?php endif ?>
