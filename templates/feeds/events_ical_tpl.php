BEGIN:VCALENDAR
CALSCALE:GREGORIAN
PRODID:-//<?php echo SITE_NAME ?>//Calendar App//EN
VERSION:2.0
<?php foreach ($event_array as $event):?>

BEGIN:VEVENT
UID:<?php echo gmstrftime ("%Y%m%dT%H%M%SZ", $event->date) . "-"; echo full_escape ($event->title) . "-" . $event->id . "@"; echo $_SERVER["HTTP_HOST"]; ?>

DTSTAMP:<?php echo gmstrftime ("%Y%m%dT%H%M%SZ") ?>

DTSTART;VALUE=DATE:<?php echo gmstrftime ("%Y%m%d", $event->date) ?>

DTEND;VALUE=DATE:<?php echo gmstrftime ("%Y%m%d", $event->date) ?>

TRANSP:TRANSPARENT
ORGANIZER:CN=<?php echo full_escape ($event->user->userName) ?>

SUMMARY:<?php echo full_escape ($event->title) ?>

DESCRIPTION:<?php $string = full_escape ($event->description); $string = str_replace("\n", "\\n", $string); $string = str_replace("\r", "", $string); echo $string; ?>

URL:<?php echo generate_link_url ($event->getAbsoluteUrl ()) ?>

END:VEVENT

<?php endforeach ?>
END:VCALENDAR
