<?php if (!empty ($photo)): ?>
    <div id="breadcrumb_trail"><p><a href="album_list.php">Albums</a> &gt; <a href="<?php echo $photo->album->getAbsoluteUrl () ?>"><?php echo $photo->album->title ?></a> &gt; <?php echo $photo->title ?></p></div>
    <h3 class="title"><?php echo full_escape ($photo->title) ?></h3>
    <p class="credit">In Album: <a href="<?php echo $photo->album->getAbsoluteUrl () ?>"><?php echo full_escape ($photo->album->title) ?></a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_photo.php?id={$photo->id}") ?>">Edit</a><?php endif ?><br/>Photo <?php echo $photo_index ?> of <?php echo $photo_count ?></p>
    <?php if (!empty ($prev_photo)):?>
    <p style="display: inline-block;"><a href="<?php echo $prev_photo->getAbsoluteUrl () ?>">&lt; <?php echo full_escape ($prev_photo->title) ?></a></p>
    <?php endif ?>

    <?php if (!empty ($next_photo)):?>
    <p style="float: right; display: inline-block;"><a href="<?php echo $next_photo->getAbsoluteUrl () ?>"><?php echo full_escape ($next_photo->title) ?> &gt;</a></p>
    <div style="clear: both;"></div>
    <?php endif ?>

    <p style="text-align: center;"><a href="<?php echo $photo->mediaUrl ?>"><img style="border: none;" src="<?php if ($photo->thumbLoc) {echo $photo->mediaThumbUrl;} else {echo $photo->mediaUrl;} ?>" alt="<?php echo full_escape ($photo->title) ?>" /></a></p>
    <p style="text-align: center;"><?php echo nl2br (full_escape ($photo->description)) ?></p>
    <p style="text-align: center; font-size: 0.8em;">Click image to view full image</p>

<?php else: ?>
    <h3>Not Found</h3>
    <p>Photo could not be found.</p>
<?php endif ?>
