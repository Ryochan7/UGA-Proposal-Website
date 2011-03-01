<?php if (!empty ($album_array)):?>
    <h3>Create Photo</h3>
    <?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
    <p>Use the form below to create a photo.</p>
    <form action="" method="post" enctype="multipart/form-data">
    <ul>
        <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?>for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
        <li><label <?php if (!empty ($form_errors["description"])): ?>class="error" <?php endif ?> for="description">Description:</label><textarea rows="20" cols="70" name="description" id="description"><?php echo full_escape ($form_values["description"]) ?></textarea></li>
        <li><label <?php if (!empty ($form_errors["albumid"])): ?>class="error" <?php endif ?>for="albumid">Album Id:</label>
            <select name="albumid" id="albumid">
                <?php foreach ($album_array as $album):?>
                    <option value="<?php echo $album->id ?>"><?php echo full_escape ($album->title) ?></option>
                <?php endforeach ?>
            </select>
        </li>

        <li><label <?php if (!empty ($form_errors["imagefile"])): ?>class="error" <?php endif ?>for="imagefile">Image:</label><input type="file" name="imagefile" id="imagefile" /></li>
        <li class="submit"><input type="submit" value="Submit" /></li>
    </ul>
    </form>
<?php else: ?>
    <h3>No albums exists for photo</h3>
    <p>Please make an album first</p>
<?php endif ?>
