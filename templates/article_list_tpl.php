<h3>Latest Articles</h3>
<?php $i = 0; foreach ($article_array as $article): ?>
    <?php if (!empty ($articletags_array[$i])):?>
        <?php $articletags = $articletags_array[$i]; ?>
    <?php endif ?>
    <?php include (joinPath ("fragments", "article_minidisplay_tpl.php")) ?>
<?php $i++; endforeach ?>
<?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
