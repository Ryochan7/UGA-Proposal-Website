<h3 class="title">Album List</h3>
<?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?><p class="credit"><a href="<?php echo generate_link_url ("create_photo.php") ?>">Create Photo</a></p><?php else: ?><p class="credit" style="line-height: 0px;">&nbsp;</p><?php endif ?>
    <?php if (!empty ($album_array)):?><table style="width: 90%; margin: 0px auto;"><?php endif ?>
<?php $i = 0; foreach ($album_array as $album): ?>
    <?php if (!empty ($photo_info_array[$i]) && count ($photo_info_array[$i]) == 2 && $photo_info_array[$i][0] > 0):?>
        <?php $count = $photo_info_array[$i][0]; $photo = $photo_info_array[$i][1]; ?>
            <?php if ($i % 3 == 0): ?><tr><?php endif ?>
            <td style="vertical-align: middle; text-align: center;"><p><a href="<?php echo $album->getAbsoluteUrl () ?>"><img style="border: none;" src="<?php if ($photo->thumbLoc) {echo $photo->mediaThumbUrl;} else {echo $photo->mediaUrl;} ?>" alt="<?php echo full_escape ($photo->title) ?>" height="100" /></a><br/><br/><span style="font-weight: bold"><?php echo full_escape ($album->title) ?></span><br/><span style="font-size: 0.9em; font-weight: normal;">Photo Count: <?php echo $count ?></span></p></td>
            <?php if ($i % 3 == 2): ?></tr><?php endif ?>
    <?php endif ?>
<?php $i++; endforeach ?>
<?php if ($i % 3 == 2) {echo "</tr>";}?>
<?php if (!empty ($album_array)):?></table><?php endif ?>
<?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
