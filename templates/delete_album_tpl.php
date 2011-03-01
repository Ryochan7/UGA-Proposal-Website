<div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a> &gt; Delete</p></div>
<h3>Delete Album</h3>
<?php if (!empty ($delete_album)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Are you sure you want to delete the album: <?php echo full_escape ($delete_album->title) ?></p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <?php if (!empty ($album_array)): ?>
            <li><label for="album_move">Move current photos to or delete: </label>
                <select name="album_move" id="album_move">
                    <option value="">-----</option>
                    <option value="0"<?php if ($form_values["album_move"] == 0) { echo " selected=\"selected\""; } ?>>Delete Photos</option>
                    <?php foreach ($album_array as $album):?>
                        <option value="<?php echo $album->id ?>"<?php if ($album->id == $form_values["album_move"]) { echo " selected=\"selected\""; } ?>>Album: <?php echo full_escape ($album->title) ?></option>
                    <?php endforeach ?>
                </select>
            </li>
            <?php endif ?>
            <li class="submit"><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <p>Album not found</p>
<?php endif ?>
