    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a> &gt; Create</p></div>
<h3>Create Album</h3>
<?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
<p>Use the form below to create a album.</p>
<form action="" method="post">
<ul>
    <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?>for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
    <li class="submit"><input type="submit" value="Submit" /></li>
</ul>
</form>
