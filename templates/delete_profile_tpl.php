<div id="breadcrumb_trail"><p><a href="user_options.php?users=all">User Options</a> &gt; Delete</p></div>
<?php if (!empty ($delete_user)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <h3>Delete Profile</h3>
    <p>Are you sure you want to delete the profile for user: <?php echo full_escape ($delete_user->userName) ?></p>
    <p>Be aware that doing this will also delete all articles, attendance records, events and pages associated with the user.</p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <li><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <h3>User not found</h3>
<?php endif ?>
