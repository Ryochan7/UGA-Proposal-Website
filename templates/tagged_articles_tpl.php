<?php if (!empty ($tag)):?>
    <h3>Tagged by: <?php echo $tag->name ?></h3>
    <?php $i = 0; foreach ($article_array as $article): ?>
        <?php if (!empty ($articletags_array[$i])):?>
            <?php $articletags = $articletags_array[$i]; ?>
            <?php include (joinPath ("fragments", "article_minidisplay_tpl.php")) ?>
        <?php endif ?>
    <?php $i++; endforeach ?>
    <?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
<?php else: ?>
    <h3>Tag does not exist</h3>
<?php endif ?>
