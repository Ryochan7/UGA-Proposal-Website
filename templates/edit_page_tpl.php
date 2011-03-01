<?php if (!empty ($page)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ($page->getAbsoluteUrl ()) ?>">View on Site</a></p>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a> &gt; Edit</p></div>
    <h3>Edit Page</h3>
    <div style="clear: both;"></div>
    <?php include joinPath ("fragments", "form_errors_tpl.php"); ?>
    <p>Use the form below to edit the attributes of the page.</p>
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
    <ul>
        <li><label <?php if (!empty ($form_errors["title"])): ?>class="error" <?php endif ?> for="title">Title:</label><input type="text" name="title" id="title" value="<?php echo full_escape ($form_values["title"]) ?>" /></li>
        <li><label <?php if (!empty ($form_errors["content"])): ?>class="error" <?php endif ?>for="content">Content:</label><textarea rows="20" cols="70" name="content" id="content"><?php echo full_escape ($form_values["content"]) ?></textarea></li>
        <li><label <?php if (!empty ($form_errors["published"])): ?>class="error" <?php endif ?>for="published">Published:</label><select name="published" id="published"><option value="false"<?php if ($form_values["published"] == "false") echo "selected=\"selected\""; ?>>False</option><option value="true"<?php if ($form_values["published"] == "true") echo "selected=\"selected\""; ?>>True</option></select></li>
            <li><label class="optional<?php if (!empty ($form_errors["template"])): ?> error<?php endif ?>" for="template">Template:<span class="sub_text">* optional</span></label><input type="text" name="template" id="template" value="<?php echo full_escape ($form_values["template"]) ?>" /></li>
            <li><input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" /></li>
            <li class="submit"><input type="submit" value="Submit" /></li>
    </ul>
    <?php include (joinPath ("fragments", "tinymce_tpl.php")); ?>
    </form>
    <p><a href="<?php echo generate_link_url ("delete_page.php?id={$page->id}") ?>">Delete</a></p>
<?php else: ?>
    <div id="breadcrumb_trail"><p><a href="page_options.php">Page Options</a> &gt; Edit</p></div>
    <h3>Edit Page</h3>
    <p>Page not found</p>
<?php endif ?>
