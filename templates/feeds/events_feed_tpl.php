<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title><?php echo get_title ($title) ?></title>
<atom:link href="<?php echo DOMAIN_ADDR . $_SERVER["REQUEST_URI"] ?>" rel="self" type="application/rss+xml" />
<link><?php echo generate_link_url () ?></link>
<description>Latest events for the <?php echo SITE_NAME ?></description>
<language>en-us</language>
<lastBuildDate><?php echo date ("r") ?></lastBuildDate>
<?php foreach ($event_array as $event): ?>

<item>
    <title><?php echo full_escape ($event->title) ?></title>
    <link><?php echo generate_link_url ($event->getAbsoluteUrl ()) ?></link>
    <description><?php echo full_escape (nl2br ($event->description)) ?></description>
    <pubDate><?php echo date ("r", $event->date) ?></pubDate>
    <guid><?php echo generate_link_url ($event->getAbsoluteUrl ()) ?></guid>
</item>
<?php endforeach ?>

</channel>
</rss>
