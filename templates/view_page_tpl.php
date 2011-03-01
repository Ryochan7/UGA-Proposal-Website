<?php if (!empty ($page)): ?>
    <h3 class="title"><?php echo full_escape ($page->title) ?></h3>
    <p class="credit">Created by: <a href="<?php echo full_escape ($page->user->getAbsoluteUrl ()) ?>"><?php echo $page->user->userName ?></a><?php if ($session->getUser () && $session->getUser ()->isAdmin ()):?> | <a href="<?php echo generate_link_url ("edit_page.php?id={$page->id}") ?>">Edit</a><?php endif ?></p>
    <?php echo stripslashes ($page->content) ?>
<?php else: ?>
    <h3>Not Found</h3>
    <p>Page could not be found.</p>
<?php endif ?>
