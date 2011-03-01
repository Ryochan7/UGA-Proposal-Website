<?php if (!empty ($article)): ?>
    <h3 class="title"><a href="<?php echo $article->getAbsoluteUrl () ?>"><?php echo full_escape ($article->title) ?></a></h3>
    <p class="credit">
        Created by: <a href="<?php echo full_escape ($article->user->getAbsoluteUrl ()) ?>"><?php echo $article->user->userName ?></a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_article.php?id={$article->id}") ?>">Edit</a><?php endif ?><br/>
        Posted on: <?php echo strftime ("%B %e, %Y", $article->postDate) ?>
        <?php if ($article->updateDate > $article->postDate):?><br/>Updated on: <?php echo strftime ("%B %e, %Y", $article->updateDate) ?><?php endif ?>
        <br/>Tags: <?php if (!empty ($articletags)):?><?php $i = 0; $size = count ($articletags); foreach ($articletags as $tag): ?><a href="<?php echo $tag->getAbsoluteUrl (); ?>"><?php echo $tag->name ?></a><?php if ($i < $size-1) { echo ", "; } $i++ ?><?php endforeach ?><?php endif ?>
    </p>
    <?php echo stripslashes ($article->content) ?>
<?php else: ?>
    <h3>Not Found</h3>
    <p>Article could not be found.</p>
<?php endif ?>
