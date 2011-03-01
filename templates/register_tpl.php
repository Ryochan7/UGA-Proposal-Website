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
<h3>Register</h3>
<p>Complete the form to start the registration process.</p>
<form action="" method="post">
    <ul>
        <li><label <?php if (!empty ($form_errors["username"])): ?>class="error" <?php endif ?>for="username">Username:</label><input type="text" id="username" name="username" size="20" value="<?php echo full_escape ($form_values["username"]); ?>" /></li>
        <li><label <?php if (!empty ($form_errors["password"])): ?>class="error" <?php endif ?>for="password">Password:</label><input type="password" id="password" name="password" size="20" value="<?php echo full_escape ($form_values["password"]); ?>" /></li>
        <li><label <?php if (!empty ($form_errors["password"])): ?>class="error" <?php endif ?>for="password2">Confirm password:</label><input type="password" id="password2" name="password2" size="20" value="<?php echo full_escape ($form_values["password2"]); ?>" /></li>
        <li><label for="password2">Ulid:</label><input type="text" id="ulid" name="ulid" size="20" value="<?php echo full_escape ($form_values["ulid"]); ?>" /></li>
        <li class="submit"><input type="submit" value="Register" /></li>
    </ul>
</form>
