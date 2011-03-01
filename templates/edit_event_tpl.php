<?php if (!empty ($event)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ($event->getAbsoluteUrl ()) ?>">View on Site</a><br/></p>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a> &gt; Edit</p></div>
    <h3>Edit Event</h3>
    <div style="clear: both;"></div>
    <?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
    <p>Use the form below to edit the attributes of the event.</p>
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
    <ul>
        <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?> for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
        <li><label <?php if (!empty ($form_errors["description"])): ?>class="error" <?php endif ?> for="description">Description:</label><textarea rows="20" cols="70" name="description" id="description"><?php echo full_escape ($form_values["description"]) ?></textarea></li>
 
            <li><label <?php if (!empty ($form_errors["platform"])): ?>class="error" <?php endif ?> for="platform">Platform:</label><select name="platform" id="platform">
<?php foreach ($platform_array as $platform):?>
    <option value="<?php echo $platform->id ?>"<?php if ($form_values["platform"] == $platform->id) echo "selected=\"selected\""; ?>><?php echo $platform->name ?></option>
<?php endforeach ?></select></li>

        <?php if ($session->getUser ()->isAdmin () || ($session->getUser ()->validUser () && $session->getUser ()->getUserType () == User::TRUSTED_TYPE)):?>
            <li><label <?php if (!empty ($form_errors["sanctioned"])): ?>class="error" <?php endif ?> for="sanctioned">Sanctioned:</label><select name="sanctioned" id="sanctioned"><option value="false"<?php if ($form_values["sanctioned"] == "false") echo "selected=\"selected\""; ?>>False</option><option value="true"<?php if ($form_values["sanctioned"] == "true") echo "selected=\"selected\""; ?>>True</option></select></li>

            <li><label <?php if (!empty ($form_errors["status"])): ?>class="error" <?php endif ?> for="status">Status:</label><select name="status" id="status"><option value="<?php echo Event::PENDING_STATUS ?>"<?php if ($form_values["status"] == Event::PENDING_STATUS) echo "selected=\"selected\""; ?>>Pending</option><option value="<?php echo Event::APPROVED_STATUS ?>"<?php if ($form_values["status"] == Event::APPROVED_STATUS) echo "selected=\"selected\""; ?>>Approved</option><option value="<?php echo Event::DENIED_STATUS ?>"<?php if ($form_values["status"] == Event::DENIED_STATUS) echo "selected=\"selected\""; ?>>Denied</option></select></li>
        <?php endif ?>
    <li><label <?php if (!empty ($form_errors["date"])): ?>class="error" <?php endif ?> for="date">Date:</label> <input type="text" name="date" id="date" readonly="readonly" value="<?php echo $form_values["date"] ?>" /> <input type="button" id="calendar-trigger" value="..." /></li>
    <li><input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" /></li>
        <li class="submit"><input type="submit" value="Submit" /></li>
    </ul>
    </form>
    <?php include (joinPath ("fragments", "jscal2_tpl.php")); ?>
    <p><a href="<?php echo generate_link_url ("delete_event.php?id={$event->id}") ?>">Delete</a></p>
<?php else: ?>
    <div id="breadcrumb_trail"><p><a href="event_options.php">Event Options</a> &gt; Edit</p></div>
    <h3>Edit Event</h3>
    <p>Event not found</p>
<?php endif ?>
