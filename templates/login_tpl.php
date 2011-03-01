<?php include ("fragments/form_errors_tpl.php"); ?>
<h3>Login Form</h3>
<form action="" method="post">
    <ul>
        <li><label <?php if (!empty ($form_errors["username"])): ?>class="error" <?php endif ?>for="username">Username:</label><input type="text" id="username" name="username" size="20" value="<?php echo full_escape ($form_values["username"]); ?>" /></li>
        <li><label <?php if (!empty ($form_errors["password"])): ?>class="error" <?php endif ?>for="password">Password:</label><input type="password" id="password" name="password" size="20" value="<?php echo full_escape ($form_values["password"]); ?>" /></li>
        <li class="submit"><input type="submit" value="Login" /></li>
    </ul>
</form>
