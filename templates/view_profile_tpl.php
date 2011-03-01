<?php if (!empty ($user)):?>
    <h3><?php echo full_escape ($user->userName) ?> Profile</h3>
    <dl>
        <dt>Username: </dt><dd><?php echo $user->userName ?></dd>
        <dt>Logo:</dt><dd><img src="<?php echo full_escape ($user->gravatarImage) ?>" alt="Profile image" /></dd>
        <dt>User type: </dt><dd><?php
switch ($user->userType) {
    case User::ADMIN_TYPE:
        echo "Admin";
        break;
    case User::TRUSTED_TYPE:
        echo "Trusted User";
        break;
    case User::REGUSER_TYPE:
        echo "Regular User";
        break;
    default:
        echo "Unknown";
}
?></dd>
        <?php if ($user->steamId):?><dt>Steam Id: </dt><dd><?php echo $user->steamId; ?></dd><?php endif ?>
        <?php if ($user->xboxId):?><dt>Xbox Gamertag: </dt><dd><?php echo $user->xboxId; ?></dd><?php endif ?>
        <?php if ($user->psnId):?><dt>PSN Id: </dt><dd><?php echo $user->psnId; ?></dd><?php endif ?>
        <?php if ($user->wiiId):?><dt>Wii Friend Code: </dt><dd><?php echo $user->wiiId; ?></dd><?php endif ?>
    </dl>
<?php else: ?>
    <h3>User not found</h3>
<?php endif ?>
