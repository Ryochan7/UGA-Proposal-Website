<?php if (!empty ($album_array) && strcmp ($action, "delete") == 0):?>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a> &gt; Delete Group</p></div>
    <h3><?php echo $content_title ?></h3>
    <p>Do you want to delete the following albums?</p>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <?php foreach ($album_array as $album):?>
        <p>ID <?php echo $album->id ?>: <?php echo $album->title ?></p>
        <input type="hidden" name="ids[]" value="<?php echo $album->id ?>" />
    <?php endforeach ?>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Delete" />
    </form>

<?php elseif (!empty ($album_array)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_album.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a></p></div>
    <h3>Album Options</h3>
    <div style="clear: both;"></div>
    <p>List of albums</p>
    <form action="" method="get">
    <p>Actions:
    <select id="action" name="action" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="delete">Delete</option>
    </select>
    <input type="submit" name="doaction" value="Submit" />
    </p>

    <table cellspacing="0" class="list">
        <thead><tr><th></th><th></th><th></th><th>Id</th><th>Title</th></tr></thead>
        <tbody>
        <?php $i = 0; foreach ($album_array as $album):?>
           <tr<?php if ($i % 2 == 0) echo " class=\"row1\"";?>>
<td><input type="checkbox" name="ids[]" id="page<?php echo $i ?>" value="<?php echo $album->id ?>" /></td>
<td><a href="<?php echo generate_link_url ("edit_album.php?id={$album->id}") ?>">Edit</a></td>
<td><a href="<?php echo generate_link_url ("delete_album.php?id={$album->id}") ?>">Delete</a></td>
<td><a href="<?php echo generate_link_url ($album->getAbsoluteUrl ()) ?>"><?php echo $album->id ?></a></td>
<td><?php echo $album->title ?></td>
</tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
    </form>
    <?php include (joinPath ("fragments", "pagination_tpl.php"));?>
<?php elseif (strcmp ($action, "delete") == 0): ?>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a></p></div>
    <h3>No albums selected</h3>
    <p>No albums chosen for deletion</p>
<?php else: ?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_album.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="album_options.php">Album Options</a></p></div>
    <h3>Album Options</h3>
    <div style="clear: both;"></div>
    <p>No albums exist</p>
<?php endif ?>
