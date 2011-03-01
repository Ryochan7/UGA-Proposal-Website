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
