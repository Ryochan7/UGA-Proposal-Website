<div id="breadcrumb_trail"><p><a href="article_options.php">Article Options</a> &gt; Delete</p></div>
<h3>Delete Article</h3>
<?php if (!empty ($delete_article)):?>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Are you sure you want to delete the article: <?php echo full_escape ($delete_article->title) ?></p>
    <form action="" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" />
        <ul>
            <li><input type="submit" value="Delete" /></li>
        </ul>
    </form>
<?php else: ?>
    <p>Article not found</p>
<?php endif ?>
