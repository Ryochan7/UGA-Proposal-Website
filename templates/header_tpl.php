<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo get_title ($title) ?></title>
<link href="<?php echo generate_media_url ("css/stylesheet.css") ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo generate_media_url ("css/pagination.css") ?>" rel="stylesheet" type="text/css" />
<?php if (isset ($extra_header)) {
    include ($extra_header);
}?>
</head>

