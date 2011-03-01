<div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a> &gt; Delete</p></div>
<h3>Delete Page</h3>
<?php if (!empty ($delete_page)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Are you sure you want to delete the page: <?php echo full_escape ($delete_page->title) ?></p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <li><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <p>Page not found</p>
<?php endif ?>
