<h3 style="text-align: center;">Events for the month of <?php echo strftime ("%B %Y", $start) ?></h3>
<?php if ($current_platform):?><h3 style="text-align: center;">Filtered by platform: <?php echo $current_platform->name ?></h3><?php endif ?>
<table border="0" cellpadding="0" cellspacing="0" class="calendar">
    <thead>
        <tr class="monthName"><th><a href="events_month.php?<?php $prev_month = strtotime ("-1 month", $start); echo strftime ("month=%m&amp;year=%Y", $prev_month); if ($current_platform) { echo "&amp;platform={$current_platform->id}"; } ?>" style="padding: 0px 3px;">&lt; <?php echo strftime ("%b %Y", $prev_month) ?></a></th><th colspan="5"><?php echo strftime ("%B %Y", $start) ?></th><th><a href="events_month.php?<?php $next_month = strtotime ("+1 month", $start); echo strftime ("month=%m&amp;year=%Y", $next_month); if ($current_platform) { echo "&amp;platform={$current_platform->id}"; } ?>" style="padding: 0px 3px;"><?php echo strftime ("%b %Y", $next_month) ?> &gt;</a></th>
        </tr>
        <tr class="dayName"><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($day_row = $start; date ("w", $day_row) != 0; $day_row = strtotime ("-1 day", $day_row)):?><?php endfor ?>
    <?php $current_day = $day_row; while ($current_day < $end):?>
        <tr>
        <?php for ($i = 0; $i < 7; $current_day = strtotime ("+1 day", $current_day), $i++):?>
            <td<?php if (date ("m", $current_day) != date ("m", $start)): ?> class="otherMonth"<?php else: ?> class="day"<?php endif ?>><a href="events_day.php?<?php echo strftime ("month=%m&amp;day=%d&amp;year=%Y", $current_day); if ($current_platform) { echo "&amp;platform={$current_platform->id}"; } ?>" class="daynum"><?php echo date ("d", $current_day) ?></a><?php $tmp_array = array (); foreach ($event_array as $key => $event):?><?php if (date ("d", $event->date) == date ("d", $current_day)):?><?php $tmp_array[$key] = $event ?><?php endif ?><?php endforeach ?><?php if (!empty ($tmp_array)):?><ul><?php $size = count ($tmp_array); $j = 0; foreach ($tmp_array as $key => $event):?><li<?php if ($j == $size-1):?> class="last"<?php endif ?>><a href="<?php echo $event->getAbsoluteUrl () ?>"><?php echo full_escape ($event->title); unset ($event_array[$key]); ?></a></li><?php $j++; endforeach ?></ul><?php endif ?>
            </td>
        <?php endfor ?>
        </tr>
    <?php endwhile ?>
    </tbody>
</table>
<form action="" method="get">
    <ul style="text-align: center">
        <li><select name="month" id="month">
                <?php $form_month = strtotime ("January 1"); for ($i = date ("m", $form_month); $i <= 12; $i++, $form_month = strtotime ("+1 month", $form_month)):?>
                    <option value="<?php echo date ("m", $form_month); ?>"
                    <?php if (idate ("m", $start) == $i) echo " selected=\"selected\""; ?>><?php echo date ("F", $form_month); ?></option>
                <?php endfor ?>
            </select>
            <select name="year" id="year">
                <?php for ($i = 2010; $i <= 2100; $i++):?>
                    <option value="<?php echo $i ?>"<?php if (idate ("Y", $start) == $i) echo " selected=\"selected\""; ?>><?php echo $i ?></option>
                <?php endfor ?>
            </select>
            <?php if ($current_platform):?>
                <input type="hidden" name="platform" id="platform" value="<?php echo $current_platform->id ?>" />
            <?php endif ?>
            <input type="submit" value="Submit" />
        </li>
    </ul>
</form>
<p style="text-align: center;">
    <?php if ($current_platform):?><a href="events_month.php?<?php echo strftime ("month=%m&amp;year=%Y", $start); ?>">Reset Platform</a>&nbsp;&nbsp;<?php endif ?>
    <a href="events_month.php">Events Month Home</a>
</p>
