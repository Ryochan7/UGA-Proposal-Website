<?php if (!empty ($alter_user)):?>
    <?php if ($session->getUser ()->isAdmin ()):?><p style="float: right;"><a href="<?php echo generate_link_url ($alter_user->getAbsoluteUrl ()) ?>">View on Site</a></p><?php endif ?>
    <?php if ($session->getUser ()->isAdmin ()):?><div id="breadcrumb_trail"><p><a href="user_options.php?users=all">User Options</a> &gt; Edit</p></div><?php endif ?>
    <h3>Edit Profile</h3>
    <div style="clear: both;"></div>
    <?php include ("fragments/form_errors_tpl.php"); ?>
    <p>Edit profile for user <?php echo full_escape ($alter_user->userName) ?></p>
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
        <ul>
            <li><label>Username:</label><?php echo full_escape ($alter_user->userName) ?></li>
            <li><label class="optional<?php if (!empty ($form_errors["password"])): ?> error<?php endif ?>" for="password">Password:<span class="sub_text">* optional</span></label><input type="password" id="password" name="password" size="20" value="<?php echo full_escape ($form_values["password"]); ?>" /></li>
            <li><label class="optional<?php if (!empty ($form_errors["password"])): ?> error<?php endif ?>" for="password2">Confirm password:<span class="sub_text">* optional</span></label><input type="password" id="password2" name="password2" size="20" value="<?php echo full_escape ($form_values["password2"]); ?>" /></li>

            <li><label class="optional<?php if (!empty ($form_errors["steamId"])): ?> error<?php endif ?>" for="steamId">Steam Id:<span class="sub_text">* optional</span></label><input type="text" id="steamId" name="steamId" size="20" value="<?php echo full_escape ($form_values["steamId"]); ?>" /></li>

            <li><label class="optional<?php if (!empty ($form_errors["xboxId"])): ?> error<?php endif ?>" for="xboxId">Xbox Gamertag:<span class="sub_text">* optional</span></label><input type="text" id="xboxId" name="xboxId" size="20" value="<?php echo full_escape ($form_values["xboxId"]); ?>" /></li>
            <li><label class="optional<?php if (!empty ($form_errors["psnId"])): ?> error<?php endif ?>" for="psnId">PSN Id:<span class="sub_text">* optional</span></label><input type="text" id="psnId" name="psnId" size="20" value="<?php echo full_escape ($form_values["psnId"]); ?>" /></li>
            <li><label class="optional<?php if (!empty ($form_errors["wiiId"])): ?> error<?php endif ?>" for="wiiId">Wii Friend Code:<span class="sub_text">* optional</span></label><input type="text" id="wiiId" name="wiiId" size="20" value="<?php echo full_escape ($form_values["wiiId"]); ?>" /></li>



            <?php if ($session->getUser ()->isAdmin ()):?>
                <li><label <?php if (!empty ($form_errors["usertype"])): ?>class="error" <?php endif ?>for="usertype">User Type:</label>
                    <select name="usertype" id="usertype">
                        <option value="<?php echo User::ANONYMOUS_TYPE ?>"<?php if ($alter_user->getUserType () == User::ANONYMOUS_TYPE) echo " selected=\"selected\"";?>>Anonymous</option>
                        <option value="<?php echo User::ADMIN_TYPE ?>"<?php if ($alter_user->getUserType () == User::ADMIN_TYPE) echo " selected=\"selected\"";?>>Admin</option>
                        <option value="<?php echo User::TRUSTED_TYPE ?>"<?php if ($alter_user->getUserType () == User::TRUSTED_TYPE ) echo " selected=\"selected\"";?>>Trusted User</option>
                        <option value="<?php echo User::REGUSER_TYPE ?>"<?php if ($alter_user->getUserType () == User::REGUSER_TYPE ) echo " selected=\"selected\"";?>>Regular User</option>
                    </select>
                </li>

                <li><label <?php if (!empty ($form_errors["status"])): ?>class="error" <?php endif ?> for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="<?php echo User::STATUS_PENDING ?>"<?php if ($alter_user->getStatus () == User::STATUS_PENDING) echo " selected=\"selected\"";?>>Pending</option>
                        <option value="<?php echo User::STATUS_NEEDADMIN ?>"<?php if ($alter_user->getStatus () == User::STATUS_NEEDADMIN) echo " selected=\"selected\"";?>>Require Admin Approval</option>
                        <option value="<?php echo User::STATUS_OK ?>"<?php if ($alter_user->getStatus () == User::STATUS_OK) echo " selected=\"selected\"";?>>Valid User</option>
                        <option value="<?php echo User::STATUS_BANNED ?>"<?php if ($alter_user->getStatus () == User::STATUS_BANNED) echo " selected=\"selected\"";?>>Banned</option>
                    </select>
                </li>
            <?php endif ?>

            <li><input type="hidden" id="id" name="id" value="<?php echo full_escape ($form_values["id"]); ?>" /></li>
            <li class="submit"><input type="submit" value="Submit" /></li>
        </ul>
    </form>
    <?php if ($session->getUser ()->isAdmin ()):?>
        <p><a href="<?php echo generate_link_url ("delete_profile.php?id={$alter_user->id}") ?>">Delete</a></p>
    <?php endif ?>
<?php else: ?>
    <?php if ($session->getUser ()->isAdmin ()):?><div id="breadcrumb_trail"><p><a href="user_options.php?users=all">User Options</a> &gt; Edit</p></div><?php endif ?>
    <h3>User not found</h3>
<?php endif ?>
