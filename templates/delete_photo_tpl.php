<h3>Delete Photo</h3>
<?php if (!empty ($delete_photo)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Are you sure you want to delete the photo: <?php echo full_escape ($delete_photo->title) ?></p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <li><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <p>Photo not found</p>
<?php endif ?>
