<?php if (!empty ($article_array) && strcmp ($action, "delete") == 0):?>
    <div id="breadcrumb_trail"><p><a href="article_options.php">Article Options</a> &gt; Delete Group</p></div>
    <h3><?php echo $content_title ?></h3>
    <p>Do you want to delete the following articles?</p>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <?php foreach ($article_array as $article):?>
        <p>ID <?php echo $article->id ?>: <?php echo $article->title ?></p>
        <input type="hidden" name="ids[]" value="<?php echo $article->id ?>" />
    <?php endforeach ?>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Delete" />
    </form>

<?php elseif (!empty ($article_array)):?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_article.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="article_options.php">Article Options</a></p></div>
    <h3>Article Options</h3>
    <div style="clear: both;"></div>
    <p>List of articles</p>
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
        <?php $i = 0; foreach ($article_array as $article):?>
           <tr<?php if ($i % 2 == 0) echo " class=\"row1\"";?>>
<td><input type="checkbox" name="ids[]" id="page<?php echo $i ?>" value="<?php echo $article->id ?>" /></td>
<td><a href="<?php echo generate_link_url ("edit_article.php?id={$article->id}") ?>">Edit</a></td>
<td><a href="<?php echo generate_link_url ("delete_article.php?id={$article->id}") ?>">Delete</a></td>
<td><a href="<?php echo generate_link_url ($article->getAbsoluteUrl ()) ?>"><?php echo $article->id ?></a></td>
<td><?php echo $article->title ?></td>
<td><?php if ($article->published) {echo "True";} else {echo "False";} ?></td>
</tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
    </form>
    <?php include (joinPath ("fragments", "pagination_tpl.php"));?>
<?php elseif (strcmp ($action, "delete") == 0): ?>
    <div id="breadcrumb_trail"><p><a href="article_options.php">Article Options</a></p></div>
    <h3>No articles selected</h3>
    <p>No articles chosen for deletion</p>
<?php else: ?>
    <p style="float: right;"><a href="<?php echo generate_link_url ("create_article.php");?>">Create</a></p>
    <div id="breadcrumb_trail"><p><a href="article_options.php">Article Options</a></p></div>
    <h3>Article Options</h3>
    <div style="clear: both;"></div>
    <p>No articles exist</p>
<?php endif ?>
