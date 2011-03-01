<?php if (!empty ($event)):?>
    <h3 class="title"><a href="<?php echo $event->getAbsoluteUrl () ?>"><?php echo full_escape ($event->title) ?></a></h3>
    <p class="credit">Posted by: <a href="<?php echo full_escape ($event->user->getAbsoluteUrl ()) ?>"><?php echo $event->user->userName ?></a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_event.php?id={$event->id}") ?>">Edit</a><?php endif ?></p>
    <p><span style="font-weight: bold">Date:</span> <?php echo strftime ("%B %d, %Y", $event->date) ?></p>
    <p style="font-weight: bold">Description:</p><p><?php echo nl2br (full_escape ($event->description)) ?></p>
    <div style="clear: both"></div>
    <p><a href="<?php echo generate_link_url ($event->getAbsoluteUrl ()) ?>">Read for more details</a></p>
<?php endif ?>
