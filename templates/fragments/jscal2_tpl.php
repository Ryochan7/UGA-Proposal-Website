<script type="text/javascript">
Calendar.setup({
    trigger    : "<?php if (isset ($trigger)) {echo $trigger;} else { echo "calendar-trigger";}?>",
    inputField : "<?php if (isset ($dateField)) {echo $dateField;} else { echo "date"; } ?>",
    dateFormat : "%d %B %Y",
});
</script>
