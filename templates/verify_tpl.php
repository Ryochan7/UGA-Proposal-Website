<?php if (!empty ($form_errors)):?>
    <div class="error_msg">
        <p>There were errors with your submission. Please correct any issues mentioned.</p>
        <ul>
        <?php foreach ($form_errors as $key => $value):?>
            <li><?php echo $value ?></li>
        <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
<h3>Verify User</h3>
<p>Please enter your login credentials to confirm your account.</p>
<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
    <ul>
        <li><label for="username">Username:</label><input type="text" id="username" name="username" size="20" value="<?php echo full_escape ($form_values["username"]); ?>" /></li>
        <li><label for="password">Password:</label><input type="password" id="password" name="password" size="20" value="<?php echo full_escape ($form_values["password"]); ?>" /></li>
        <input type="hidden" name="token" id="token" value="<?php echo $form_values["token"] ?>" />
        <li class="submit"><input type="submit" value="Verify Account" /></li>
    </ul>
</form>
