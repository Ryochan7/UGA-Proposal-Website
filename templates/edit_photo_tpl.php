<?php if (!empty ($photo)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ($photo->getAbsoluteUrl ()) ?>">View on Site</a></p>
    <h3>Edit Photo</h3>
    <div style="clear: both;"></div>
    <?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
    <p>Use the form below to create a photo.</p>
    <form action="" method="post" enctype="multipart/form-data">
    <ul>
        <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?>for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
        <li><label <?php if (!empty ($form_errors["description"])): ?>class="error" <?php endif ?>for="description">Description:</label><textarea rows="20" cols="70" name="description" id="description"><?php echo full_escape ($form_values["description"]) ?></textarea></li>
        <li><label <?php if (!empty ($form_errors["albumid"])): ?>class="error" <?php endif ?>for="albumid">Album Id:</label>
            <select name="albumid" id="albumid">
                <?php foreach ($album_array as $album):?>
                    <option value="<?php echo $album->id ?>"<?php if ($form_values["albumid"] == $album->id) {echo " selected=\"selected\"";} ?>><?php echo full_escape ($album->title) ?></option>
                <?php endforeach ?>
            </select>
        </li>

        <li><label>Current:</label><img src="<?php if ($photo->thumbLoc) {echo $photo->mediaThumbUrl;} else {echo $photo->mediaUrl;} ?>" width="350" alt="<?php echo full_escape ($photo->title) ?>" /></li>
        <li><label class="optional<?php if (!empty ($form_errors["imagefile"])): ?> error<?php endif ?>" for="imagefile">New Image:<span class="sub_text">* optional</span></label><input type="file" name="imagefile" id="imagefile" /></li>
        <li><input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" /></li>
        <li class="submit"><input type="submit" value="Submit" /></li>
    </ul>
    <p><a href="<?php echo generate_link_url ("delete_photo.php?id={$photo->id}") ?>">Delete</a></p>
<?php else: ?>
    <h3>Edit Photo</h3>
    <p>Photo not found</p>
<?php endif ?>
