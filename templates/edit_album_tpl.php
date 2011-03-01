<?php if (!empty ($album)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ($album->getAbsoluteUrl ()) ?>">View on Site</a></p>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a> &gt; Edit</p></div>
    <h3>Edit Album</h3>
    <div style="clear: both;"></div>
    <?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
    <p>Use the form below to edit the attributes of the album.</p>
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
    <ul>
        <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?> for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
        <li><input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" /></li>
        <li class="submit"><input type="submit" value="Submit" /></li>
    </ul>
    </form>
    <p><a href="<?php echo generate_link_url ("delete_album.php?id={$album->id}") ?>">Delete</a></p>
<?php else: ?>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a> &gt; Edit</p></div>
    <h3>Edit Album</h3>
    <p>Album not found</p>
<?php endif ?>
