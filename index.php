<?php
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
# Description: phpAbook v.9.0
#
# Date: 2016-06-06
# File: index.php
##############################################################################

// To display all errors
// Remove the next 2 lines if you don't want to show errors
// in a production site
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Logout?
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "logout") {
	setcookie("userInfo");
	header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
}
# If the they haevn't logged in yet...let's tell them to do so
elseif (!isset($_COOKIE["userInfo"]) || $_COOKIE["userInfo"] == "") {
	include("include/lib.login.php");
}
# If they are logged in, let them in...
else {

	include("include/lib.abook.php");

	#include the header template
	include("include/inc.header.php");

	if (isset($_COOKIE["userInfo"]) && $_COOKIE["userInfo"] != "") {
		echo "<blockquote>$message[1] <span class=\"bold\">". ucfirst($userName)."</span>!</blockquote>";
?>
<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td class="menuTD" align="right"><?php
		if ($is_admin == true) {
			print("<a href=\"".$_SERVER['PHP_SELF']."?action=add\" title=\"$message[13]\">$message[13]</a> | <a href=\"".$_SERVER['PHP_SELF']."?action=admin\" title=\"$message[26]\">$message[26]</a> | ");
		}
		print("<a href=\"".$_SERVER['PHP_SELF']."\" title=\"$message[55]\">$message[55]</a> | <a href=\"".$_SERVER['PHP_SELF']."?action=logout\" title=\"$message[44]\">$message[44]</a>");
?></td>
	</tr>
</table>
<?php }
if (isset($_COOKIE["userInfo"]) && $_COOKIE["userInfo"] != "") {
	if (!isset($_REQUEST['action']) || $_REQUEST['action'] == "(list|search)") {
?>
<div align="center">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="search">
<table border="0" width="90%" cellspacing="0" cellpadding="4" align="center" bgcolor="#CCFF99" class="viewTable">
	<tr>
		<td class="label"><?php echo $message[38]; ?></td>
		<td><select name="field"><option label="Last Name" value="LastName" selected>Last Name</option><option label="First Name" value="FirstName">First Name</option><option label="Address" value="Address1">Address</option><option label="Email Address" value="Email1">Email Address</option></select></td>
		<td><select name="how"><option label="<?php echo $message[39]; ?>" value="contain" selected><?php echo $message[39]; ?></option><option label="<?php echo $message[40]; ?>" value="begin"><?php echo $message[40]; ?></option><option label="<?php echo $message[42]; ?>" value="is"><?php echo $message[42]; ?></option><option label="<?php echo $message[41]; ?>" value="end"><?php echo $message[41]; ?></option></select></td>
		<td><input type="text" name="str" value="Search..." size="20" maxlength="100" onfocus="javascript:document.search.str.value='';"></td>
		<td><input type="hidden" name="action" value="search"><input type="submit" class="buttons" value="<?php echo $message[53]; ?>"></td>
	</tr>
</table>
</form>
</div>
<?php
	}
}

if (!isset($_REQUEST['action']) || $_REQUEST['action'] == "list") {
	list_entries();
}
elseif ($_REQUEST['action'] == "view") {
	view_entry($_REQUEST['id']);
}
elseif ($_REQUEST['action'] == "edit") {
	edit_entry($_REQUEST['id']);
}
elseif ($_REQUEST['action'] == "delete") {
	delete_entry($_REQUEST['id']);
}
elseif ($_REQUEST['action'] == "add") {

	$FirstName=$LastName="";
	if(isset($_REQUEST['FirstName'])){
		$FirstName=$_REQUEST['FirstName']; $LastName=$_REQUEST['LastName'];
	}
	add_entry($FirstName,$LastName);
}
elseif ($_REQUEST['action'] == "search") {
	search($_REQUEST['field'], $_REQUEST['how'], $_REQUEST['str']);
}
elseif ($_REQUEST['action'] == "admin") {
	user_admin();
}
elseif ($_REQUEST['action'] == "add_user") {
	adduser($_REQUEST['u_user'], $_REQUEST['u_pass1'], $_REQUEST['u_pass2'], $_REQUEST['u_admin']);
}
	elseif ($_REQUEST['action'] == "del_user") {
		rmuser($_REQUEST['uid']);
	}
	elseif ($_REQUEST['action'] == "mod_user") {
		moduser($_REQUEST['uid']);
	}

	# include the footer template
	include("include/inc.footer.php");
}
?>