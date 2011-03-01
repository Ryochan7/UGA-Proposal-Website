<h3>Member List</h3>
<?php if (!empty ($user_array)):?>

        <form action="" method="get">
        <p>Starts with:
            <select name="startswith" id="startswith">
                <?php $alpha_array = array ("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");?><option value="">None</option><?php if (!empty ($form_values["startswith"])):?><?php foreach ($alpha_array as $letter):?><option value="<?php echo $letter ?>"<?php if ($form_values["startswith"] == $letter) echo "selected=\"selected\""?>><?php echo $letter ?></option><?php endforeach ?><?php else: ?><?php foreach ($alpha_array as $letter):?><option value="<?php echo $letter ?>"><?php echo $letter ?></option><?php endforeach ?><?php endif ?>
            </select>
            <input type="submit" value="Filter" />
        </p>
        </form>

        <form action="" method="get">
        <p>Filter by:
            <select name="identity" id="identity"> 
                <option value="">None</option>
                <option value="steam"<?php if (strcmp ($form_values["identity"], "steam") == 0) echo "selected=\"selected\""?>>Steam</option>
                <option value="xbox"<?php if (strcmp ($form_values["identity"], "xbox") == 0) echo "selected=\"selected\""?>>Xbox</option>
                <option value="psn"<?php if (strcmp ($form_values["identity"], "psn") == 0) echo "selected=\"selected\"" ?>>PSN</option>
                <option value="wii"<?php if (strcmp ($form_values["identity"], "wii") == 0) echo "selected=\"selected\""?>>Wii</option>
            </select>
            <input type="submit" value="Filter" />
        </p>
        </form>
    <table cellspacing="0" class="list">
        <thead><tr><th>Username</th><th>Steam</th><th>Xbox</th><th>PSN</th><th>Wii</th></tr></thead>
        <tbody>
        <?php $i = 0; foreach ($user_array as $user):?>
           <tr<?php if ($i % 2 == 0) echo " class=\"row1\"";?>><td><a href="<?php echo $user->getAbsoluteUrl () ?>"><?php echo $user->userName ?></a></td>
<?php if ($user->steamId):?><td><?php echo $user->steamId ?></td><?php else: ?><td> </td><?php endif ?>
<?php if ($user->xboxId):?><td><?php echo $user->xboxId ?></td><?php else: ?><td> </td><?php endif ?>
<?php if ($user->psnId):?><td><?php echo $user->psnId ?></td><?php else: ?><td> </td><?php endif ?>
<?php if ($user->wiiId):?><td><?php echo $user->wiiId ?></td><?php else: ?><td> </td><?php endif ?></tr>
        <?php $i++; endforeach ?>
        </tbody>
    </table>
    <?php include joinPath ("fragments", "pagination_tpl.php"); ?>
<?php else: ?>
    <form action="" method="get">
    <p>Starts with:
        <select name="startswith" id="startswith">
            <?php $alpha_array = array ("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");?><option value="">None</option><?php if (!empty ($form_values["startswith"])):?><?php foreach ($alpha_array as $letter):?><option value="<?php echo $letter ?>"<?php if ($form_values["startswith"] == $letter) echo "selected=\"selected\""?>><?php echo $letter ?></option><?php endforeach ?><?php else: ?><?php foreach ($alpha_array as $letter):?><option value="<?php echo $letter ?>"><?php echo $letter ?></option><?php endforeach ?><?php endif ?>
        </select>
        <input type="submit" value="Filter" />
    </p>
    </form>

    <form action="" method="get">
    <p>Filter Userlist:
        <select name="identity" id="identity_filter"> 
            <option value="">Filter by</option>
            <option value="steam"<?php if (strcmp ($form_values["identity"], "steam") == 0) echo "selected=\"selected\""?>>Steam</option>
            <option value="xbox"<?php if (strcmp ($form_values["identity"], "xbox") == 0) echo "selected=\"selected\""?>>Xbox</option>
            <option value="psn"<?php if (strcmp ($form_values["identity"], "psn") == 0) echo "selected=\"selected\"" ?>>PSN</option>
            <option value="wii"<?php if (strcmp ($form_values["identity"], "wii") == 0) echo "selected=\"selected\""?>>Wii</option>
        </select>
        <input type="submit" value="Filter" />
    </form>
    </p>
    <p>No users found.</p>
<?php endif ?>
