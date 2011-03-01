<div id="breadcrumb_trail"><p><a href="user_options.php?users=all">User Options</a></p></div>
<?php if (!empty ($user_array) && strcmp ($action, "delete") == 0):?>
    <h3><?php echo $content_title ?></h3>
    <p>Do you want to delete the following users?</p>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
    <?php foreach ($user_array as $user):?>
        <p>ID <?php echo $user->id ?>: <?php echo $user->userName ?></p>
        <input type="hidden" name="userids[]" value="<?php echo $user->id ?>" />
    <?php endforeach ?>
    <p>Be aware that doing this will also delete all articles, attendance records, events and pages associated with the users.</p>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Delete" />
    </form>

<?php elseif (!empty ($user_array)):?>
    <h3><?php echo $content_title ?></h3>
    <p>Check list of user profiles. Use links in Ulid column to check ISU People Search for a user's general information from ISU</p>
    <script type="text/javascript">
        function ulidsearch_submit (id) {
            document.forms[0].ulid.value = id;
            document.forms[0].submit ();
            return false;
        }
    </script>
    <form action="http://www.ilstu.edu/home/find/peoplesearch.phtml" method="post">
    <input type="hidden" name="ulid" id="ulid" value="" />
    <input type="hidden" name="Find3" id="Find3" value="Find" />
    </form>
    <form action="" method="get">
    <p>Actions:
    <select id="action" name="action" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="delete">Delete</option>
    </select>
    <input type="submit" name="doaction" value="Submit" />
    <br/>Alter User Status:
    <select id="status" name="status" style="margin-bottom: 10px;">
        <option value="">-----</option>
        <option value="<?php echo User::STATUS_PENDING ?>">Pending</option>
        <option value="<?php echo User::STATUS_NEEDADMIN ?>">Require Admin Approval</option>
        <option value="<?php echo User::STATUS_OK ?>">Valid</option>
        <option value="<?php echo User::STATUS_BANNED ?>">Banned</option>
    </select>
    <input type="submit" name="domodstatus" value="Submit" /></p>
    <table cellspacing="0" class="list"><thead><tr><th></th><th></th><th></th><th>Username</th><th>Ulid</th><th>Status</th><th>Type</th></tr></thead>
    <tbody>
    <?php $i = 0; foreach ($user_array as $user): ?>
        <tr<?php if ($i % 2 == 0) echo " class=\"row1\""?>><td><input type="checkbox" name="userids[]" id="user<?php echo $i ?>" value="<?php echo $user->id ?>" /></td><td><a href="<?php echo generate_link_url ("edit_profile.php?id={$user->id}") ?>">Edit</a></td><td><a href="<?php echo generate_link_url ("delete_profile.php?id={$user->id}") ?>">Delete</a></td><td><a href="<?php echo $user->getAbsoluteUrl () ?>"><?php echo full_escape ($user->username) ?></a></td><td><a href="http://www.ilstu.edu/home/find/peoplesearch.phtml" onclick="return ulidsearch_submit ('<?php echo $user->ulid ?>');"><?php echo $user->ulid ?></a></td><td>
<?php switch ($user->status) {
    case User::STATUS_PENDING:
        echo "Pending";
        break;
    case User::STATUS_NEEDADMIN:
        echo "Require Admin Approval";
        break;
    case User::STATUS_OK:
        echo "Valid User";
        break;
    case User::STATUS_BANNED:
        echo "Banned User";
        break;
    default:
        echo "Unknown";
}?>
</td><td>
<?php switch ($user->userType) {
    case User::ANONYMOUS_TYPE:
        echo "Anonymous";
        break;
    case User::ADMIN_TYPE:
        echo "Admin";
        break;
    case User::TRUSTED_TYPE:
        echo "Trusted";
        break;
    case User::REGUSER_TYPE:
        echo "Regular User";
        break;
    default:
        echo "Unknown";
}?></td></tr>
    <?php $i++; endforeach ?>
    </tbody>
    </table>
    </form>
    <?php include (joinPath ("fragments", "pagination_tpl.php")) ?>
<?php elseif (strcmp ($action, "delete") == 0): ?>
    <h3>No users selected</h3>
    <p>No users chosen for deletion</p>
<?php else: ?>
    <h3>Pending Users</h3>
    <p>There are no pending users</p>
<?php endif ?>
