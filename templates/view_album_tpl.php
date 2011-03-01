<?php if (!empty ($album)): ?>
    <div id="breadcrumb_trail"><p><a href="album_list.php">Albums</a> &gt; <?php echo $album->title ?></p></div>
    <h3 class="title">Album: <?php echo full_escape ($album->title) ?></h3>
    <p class="credit"><a href="album_list.php">Albums</a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_album.php?id={$album->id}") ?>">Edit</a> | <a href="<?php echo generate_link_url ("create_photo.php") ?>">Create Photo</a><?php endif ?></p>
    <?php if (!empty ($photo_array)):?>
        <table style="width: 90%; margin: 0px auto;">
        <?php $i = 0; foreach ($photo_array as $photo):?>
            <?php if ($i % 3 == 0): ?><tr><?php endif ?>
            <td style="vertical-align: middle; text-align: center;"><p><a href="<?php echo $photo->getAbsoluteUrl () ?>"><img style="border: none;" src="<?php if ($photo->thumbLoc) {echo $photo->mediaThumbUrl;} else {echo $photo->mediaUrl;} ?>" alt="<?php echo full_escape ($photo->title) ?>" height="100" /></a></p></td>
            <?php if ($i % 3 == 2): ?></tr><?php endif ?>
            <?php /*
            <p style="float: left; width: 300px; text-align: center; margin-left: 10px;"><a href="<?php echo $photo->getAbsoluteUrl () ?>"><img style="border: none;" src="<?php if ($photo->thumbLoc) {echo $photo->mediaThumbUrl;} else {echo $photo->mediaUrl;} ?>" alt="<?php echo full_escape ($photo->title) ?>" height="100" /></a><br/><br/><?php echo full_escape ($photo->title) ?></p>
            */?>
        <?php $i++; endforeach ?><?php if ($i % 3 == 2) {echo "</tr>";}?>
        
        </table>
    <?php else: ?>
        <p>No photos found for article.</p>
    <?php endif ?>
    <?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
<?php else: ?>
    <h3>Not Found</h3>
    <p>Article could not be found.</p>
<?php endif ?>
