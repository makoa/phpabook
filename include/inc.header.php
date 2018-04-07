<?php
if (!isset($HTTP_COOKIE_VARS["userInfo"]) || $HTTP_COOKIE_VARS["userInfo"] == "") {
	$body_tag = "<body onload=\"document.f.auth_user.focus();\">\n";
}
else {
	$body_tag = "<body>\n";
}
$img_header_size = getimagesize($img['header']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Address Book <?php echo substr($abook_vers, 9); ?></title>
<style type="text/css" media="Screen">
<!--
<?php
//if (eregi("^Mozilla/4[.]([0|7][0-9])", $_SERVER['HTTP_USER_AGENT'])) {
	include("css/abook_ns4.css");
/*}
else {
	include("css/abook_ie.css");
}*/
?>
-->
</style>
</head>
<?php echo $body_tag; ?>
<table border="0" width="640" cellspacing="0" cellpadding="0" align="center" class="mainTable">
<tr>
	<td><img src="<?php echo $img['header']; ?>" alt="<?php echo $abook_vers; ?>" id="abook" <?php echo $img_header_size[3]; ?> border="0"></td>
</tr>
<tr>
	<td class="text"<?php if (!isset($HTTP_COOKIE_VARS["userInfo"]) || $HTTP_COOKIE_VARS["userInfo"] == "") { ?> height="400px" valign="middle"<?php } ?>>
