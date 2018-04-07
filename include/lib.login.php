<?php
require_once("include/config.inc.php");
##############################################################################
# PHP Code by Gilnei Moraes
# Contact: gilneim@hotmail.com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
##############################################################################
# Description: Login sequence
#
# Date: 2016-06-06
# File: lib.login.php
##############################################################################
function recursive_ls($listing, $directory, $count) {
	$dummy = $count;
	if ($handle = opendir($directory)) {
		while ($file = readdir($handle)) {
			if ($file == '.' || $file == '..') continue;
			else if ($h = @opendir($directory.$file."/")) {
				closedir($h);
				$count = -1;
				$listing["$file"] = array();
				recursive_ls($listing["$file"], $directory.$file."/", $count + 1);
			}
			else {
				$listing[$dummy] = $file;
				$dummy = $dummy + 1;
			}
		}
	}
	closedir($handle);
	return ($listing);
}
$dir_ls = recursive_ls(array(), "include/lang", 0);
$auth = false;
if (!isset($_POST['auth_user'])) {
	include("include/inc.header.php");
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="f">
<table class="viewTable" border="0" width="350" cellspacing="0" cellpadding="2" align="center" bgcolor="#CCFF99">
<tr>
		<td colspan="2" class="nameHeader"><?php echo $message[6]; ?></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[16]; ?></td>
		<td><input type="text" name="auth_user" size="22" maxlength="15"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[17]; ?></td>
		<td><input type="password" name="auth_passwd" size="22" maxlength="15"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[54]; ?></td>
		<td><select name="lang">
<?php

	while(list($k,$v) = each($dir_ls)) {
		//if ($v != ".DS_Store" && $v != "CVS") {
			print("<option value=\"$v\"");
			if ($default_lang == $v) {
				print(" selected");
			}
			print(">$v</option>\n");
		//}
	}
?></select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="buttons" type="submit" name="submit" value="<?php echo $message[43]; ?>"></td>
	</tr>
</table>
</form>
<?php
	// print ("<div align=\"center\">You failed to provide a correct password...</div><br>\n");
	include("include/inc.footer.php");
	exit;
	}
else {
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	// Check connection
	if (!$con) { die("Connection error: " . mysqli_connect_error()); }

	$username = strtolower($_POST['auth_user']);
	$result = mysqli_query($con,"SELECT uid, password FROM $db_auth_table WHERE username = '$username'");
	$row = mysqli_fetch_array($result);

	if (md5($_POST['auth_passwd']) != $row['password']) {
	include("include/inc.header.php");
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="f">
<table class="viewTable" border="0" width="350" cellspacing="0" cellpadding="2" align="center" bgcolor="#CCFF99">
	<tr>
		<td colspan="2" class="nameHeader"><?php echo $message[6]; ?></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[16]; ?></td>
		<td><input type="text" name="auth_user" size="22" maxlength="15"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[17]; ?></td>
		<td><input type="password" name="auth_passwd" size="22" maxlength="15"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $message[54]; ?></td>
		<td><select name="lang">
<?php
	while(list($k,$v) = each($dir_ls)) {
		//if ($v != ".DS_Store" && $v != "CVS") {
		print("<option value=\"$v\"");
		if ($default_lang == $v) {
			print(" selected");
		}
		print(">$v</option>\n");
		//}
	}
?></select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="buttons" type="submit" name="submit" value="<?php echo $message[43]; ?>"></td>
	</tr>
</table>
</form>
<?php
	print ("<div align=\"center\">$errorMsg[4]<br></div>\n");
	include("include/inc.footer.php");
	exit;
	}
}
$auth = true;
$userInfo = $username . " " . $row['uid'] . " " . $_POST['lang'];
setcookie("userInfo", $userInfo, time() + 3600);
//setcookie("userID", $row['uid'], time() + 3600);
header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?action=list");
?>