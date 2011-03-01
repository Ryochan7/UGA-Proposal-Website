<?php if (!empty ($page_array) && strcmp ($action, "delete") == 0):?>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a> &gt; Delete Group</p></div>
    <h3>Page Options</h3>
    <p>Do you want to delete the following pages?</p>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <?php foreach ($page_array as $page):?>
        <p>ID <?php echo $page->id ?>: <?php echo $page->title ?></p>
        <input type="hidden" name="ids[]" value="<?php echo $page->id ?>" />
    <?php endforeach ?>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Delete" />
    </form>

<?php elseif (!empty ($page_array)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_page.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a></p></div>
    <h3>Page Options</h3>
    <div style="clear: both;"></div>
    <p>List of pages</p>
    <form action="" method="get">
    <p>Actions:
    <select id="action" name="action" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="delete">Delete</option>
    </select>
    <input type="submit" name="doaction" value="Submit" />
    </p>

    <table cellspacing="0" class="list">
        <thead><tr><th></th><th></th><th></th><th>Id</th><th>Title</th><th>Published</th></tr></thead>
        <tbody>
        <?php $i = 0; foreach ($page_array as $page):?>
           <tr<?php if ($i % 2 == 0) echo " class=\"row1\"";?>>
<td><input type="checkbox" name="ids[]" id="page<?php echo $i ?>" value="<?php echo $page->id ?>" /></td>
<td><a href="<?php echo generate_link_url ("edit_page.php?id={$page->id}") ?>">Edit</a></td>
<td><a href="<?php echo generate_link_url ("delete_page.php?id={$page->id}") ?>">Delete</a></td>
<td><a href="<?php echo generate_link_url ($page->getAbsoluteUrl ()) ?>"><?php echo $page->id ?></a></td>
<td><?php echo $page->title ?></td>
<td><?php if ($page->published) {echo "True";} else {echo "False";} ?></td>
</tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
    </form>
    <?php include (joinPath ("fragments", "pagination_tpl.php"));?>
<?php elseif (strcmp ($action, "delete") == 0): ?>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a></p></div>
    <h3>No pages selected</h3>
    <p>No pages chosen for deletion</p>
<?php else: ?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_page.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a></p></div>
    <h3>Page Options</h3>
    <div style="clear: both;"></div>
    <p>No pages exist</p>
<?php endif ?>
