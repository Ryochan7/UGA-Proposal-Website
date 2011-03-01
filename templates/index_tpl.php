<?php include ("header_tpl.php") ?>
<body>
<div id="header"><a href="<?php echo BASE_URL ?>"><img src="<?php echo generate_media_url ("images/mainlogo2.png") ?>" alt="Main logo" /></a></div>
<div style="clear: both;"></div>
<div id="page_link_header">
    <ul>
        <li><a href="<?php echo generate_link_url ("view_page.php?id=3") ?>">Tester</a></li>
        <li><a href="<?php echo generate_link_url ("view_page.php?id=1") ?>">Lisa</a></li>
        <li><a href="event_list.php">Events</a></li>
        <li><a href="events_day.php">Event Day View</a></li>
        <li><a href="events_month.php">Event Month View</a></li>
    </ul>
</div>
<div style="clear: both;"></div>

<div id="page">
    <div id="sidebar">
        <?php if (isset ($session) && $session->getUser () != null && $session->getUser ()->validUser ()):?>
            <div id="user_display">
                <p>User: <a href="<?php echo $session->getUser ()->getAbsoluteUrl () ?>"><?php echo $session->getUser ()->userName ?></a></p>
            </div>
        <?php endif ?>
        <ul>
            <li>
                <?php if (isset ($session) && $session->getUser () != null && $session->getUser ()->validUser ()):?>
                    <h3>Options</h3>
                    <ul>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="<?php echo $session->getUser ()->editProfileUrl ?>">Edit Profile</a></li>
                    </ul>
                    <?php if ($session->getUser ()->isAdmin ()):?>
                        <h3>Admin</h3>
                        <ul>
                            <li><a href="user_options.php">Pending Users Options</a></li>
                            <li><a href="user_options.php?users=all">All Users Options</a></li>
                            <li><a href="page_options.php">Page Options</a></li>
                            <li><a href="event_options.php">Event Options</a></li>
                            <li><a href="article_options.php">Article Options</a></li>
                            <li><a href="album_options.php">Album Options</a></li>
                        </ul>
                    <?php endif ?>
                <?php else: ?>
                <h3>Sign On</h3>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
                <?php endif ?>
            </li>

            <?php if (!empty ($sidebar_extra)) { 
                include ($sidebar_extra);
            } ?>

            <li>
                <h3>Main</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="phpBB/">Forums</a></li>
                    <li><a href="article_list.php">Articles</a></li>
                    <li><a href="album_list.php">Albums</a></li>
                    <li><a href="<?php echo generate_link_url ("user_list.php") ?>">Members</a></li>
                </ul>
            </li>

            <li>
                <h3>Events</h3>
                <ul>
                    <li><a href="event_list.php">Event</a></li>
                    <li><a href="events_day.php">Event Day View</a></li>
                    <li><a href="events_month.php">Event Month View</a></li>
                </ul>
            </li>

            <li>
                <h3>Find Us</h3>
                <ul>
                    <li><a href="http://www.facebook.com/group.php?gid=30646701413" target="_blank">Facebook</a><a href="http://www.facebook.com/group.php?gid=30646701413" target="_blank"> <img src="<?php echo generate_media_url ("images/FaceBook-icon.png") ?>" alt="Facebook" style="margin-bottom: -4px;" /></a></li>
                    <li style="line-height: 1.75em;"><a href="http://www.ilstu.edu/" target="_blank">Illinois State University</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="content_pane">
        <?php if (isset ($session) && strlen ($session->getMessage ()) != 0): ?>
            <?php if ($session->getMessageType () == Session::MESSAGE_NORMAL): ?>
                <p class="session_message"><?php echo $session->getMessage () ?></p>
            <?php else: ?>
                <p class="session_error"><?php echo $session->getMessage () ?></p>
            <?php endif ?>
        <?php endif ?>

        <?php if (!empty ($main_page)):?>
            <?php include ($main_page); ?>
        <?php else: ?>
            <p>Check out our Facebook group or click the banner to go to the Forums.</p>
            <p>Welcome to the UGA</p>
        <?php endif ?>
    </div>
    <div style="clear: both"></div>
    <div id="footer"><p>&copy;2010 University Gaming Association</p></div>
</div>
</body>
</html>
