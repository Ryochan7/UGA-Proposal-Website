<li>
    <h3>Event Extras</h3>
    <ul>
        <li><a href="feeds/latest_rss/">RSS</a></li>
        <li><a href="feeds/latest_ical/">iCalendar</a></li>
    </ul>
</li>
<li>
    <h3>Filter Platform</h3>
    <ul>
        <?php if (!empty ($platform_array)):?>
            <?php foreach ($platform_array as $platform):?>
                <li><a href="<?php echo "{$_SERVER["PHP_SELF"]}?"?><?php if (isset ($start)):?><?php echo strftime ("month=%m&amp;day=%d&amp;year=%Y&amp;", $start); ?><?php endif ?><?php echo "platform={$platform->id}" ?>"><?php echo $platform->name ?></a></li>
            <?php endforeach ?>
        <?php endif ?>
    </ul>
</li>
