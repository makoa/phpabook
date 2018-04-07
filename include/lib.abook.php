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
# Description: Library of functions for the Abook
#
# Date: 2016-06-06
# File: lib.abook.php
##############################################################################
require("include/config.inc.php");

# Common connection to the database
$con = @mysqli_connect($db_host, $db_user, $db_pass, $db_db);

// Check connection
if (!$con) { die("Connection error: " . mysqli_connect_error()); }

# Let's determine if the user has 'admin' access to the database
# If not, let them browse through the addressbook but not edit/add/delete
# Get the userID from the cookie we set during login

# query the database...
$result = @mysqli_query($con,"SELECT admin FROM $db_auth_table WHERE 1 && uid = '$userID'");
// Check connection
if (!result){ die("Connection error: " . $errorMsg[3]); }


$data = @mysqli_fetch_array($result);

# Now for the fun stuff
if ($data[0] == "YES") {
	$is_admin = true;
}
else {
	$is_admin = false;
}

# For errors and messages - sets up a small <table> for
# the error or confirmation/success messages to be displayed
######################################################
function print_msg($msg_title, $msg, $link) {
	global $message;

	# Just print out the table with the message
	print("<table class=\"viewTable\" width=\"60%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n");
	print("<tr>\n\t<td class=\"nameHeader\">$msg_title</td>\n</tr>\n");
	print("<tr>\n\t<td class=\"text\" style=\"padding: 4px;\">$msg<br></td>\n</tr>");
	print("<tr>\n\t<td class=\"text\" align=\"center\" style=\"padding: 4px;\"><input class=\"buttons\" type=\"button\" name=\"$message[56]\" value=\"$message[56]\" onclick=\"javascript:location.href='$link';\"></td>\n</tr>\n");
	print("</table>\n<br>");
}

# Let's print a rolodex
######################################################
function rolodex() {
	global $rol, $rolArray;

	# Now we list out each $k/$v and in one fail swoop
	print("|");
	while (list($k, $v) = each($rolArray)) {
		if ($rol == $v) {
			print("<span class=\"rolodex_sel\">&nbsp;$v&nbsp;</span>|");
		}
		else {
			print("<a href=\"".$_SERVER['PHP_SELF']."?action=list&amp;rol=$v\" title=\"$v\">&nbsp;$v&nbsp;</a>|");
		}
	}
}

# List Records in Abook
######################################################
function list_entries() {
	# Global 'vars' needed in this function
	global $con, $img, $errorMsg, $message, $db_table, $is_admin, $pos, $max_list, $rol;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Start the HTML output (<table>)
	print("<table class=\"viewTable\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\">\n");
	print("<tr>\n\t<td colspan=\"5\" class=\"nameHeader\">$message[7]");
	if (isset($_GET['rol'])) {
		print(" &raquo; ".$_GET['rol']);
	}
	print("</td>\n</tr>\n");
	print("<tr>\n\t<td colspan=\"5\" class=\"rolodex\">");
		rolodex();
	print("</td>\n</tr>\n");

	# We now set up a way to control how many entries
	# are output per page. This is all controlled
	# by the $max_list var in config.inc.php

	# First, get the total # of entries in the table
	$total_query = "SELECT * FROM $db_table";
	$total_result = mysqli_query($con,$total_query);
	$total_records = mysqli_num_rows($total_result);

	# Then divide the $total_records by the $max_list
	# to get the total # of pages we'll be dealing with
	$total_pages = ceil($total_records / $max_list);

	# If there is no $pos var set, set it to 0
	# basically, start on page 0
	if(!isset($_GET['pos'])) {
		$_GET['pos'] = 0;
	}

	# start the query to get the results
	# multiply the current $pos by the $max_list
	# so 0*20, 1*20, 2*20 etc...
	$start = $_GET['pos'] * $max_list;

	# the query that gets the records that will be displayed on each page
	# here is where we will set up an alphabet type rolodex thingy (that's so technical!)

	#is there a rolodex call?
	if(isset($_GET['rol']) && $_GET['rol'] != "") {
		$sqlQuery = "SELECT * FROM $db_table WHERE LastName LIKE '".$_GET['rol']."%' ORDER BY LastName LIMIT $start, $max_list";
	}
	elseif (!isset($_GET['rol']) || $_GET['rol'] == "all") {
		$sqlQuery = "SELECT * FROM $db_table ORDER BY LastName LIMIT $start, $max_list";
	}
 	$result  = @mysqli_query($con,$sqlQuery) or die($errorMsg[3]);
	mysqli_free_result($total_result);
	$num_rows = mysqli_num_rows($result);

	# Loop through the results of the SQL command
	for($i = 0; $i < $num_rows; $i++) {
		mysqli_data_seek($result, $i);
		$array = mysqli_fetch_array($result);
		$id = $array['id'];
		$FirstName = $array['FirstName'];
		$LastName = $array['LastName'];
		$Email1 = $array['Email1'];
		$Email2 = $array['Email2'];
		$HomePh = $array['HomePh'];
		$WorkPh = $array['WorkPh'];
		$MobPh = $array['MobilePh'];

		# If there's no HomePh in the database, don't display it
		# display one of the other phone numbers...
		# Doesn't exactly work like it should :/
		if ($HomePh == "" && $MobPh == "" && $WorkPh == "") {
			$phone = "&nbsp;&nbsp;&nbsp;" . $message[59];
		}
		else {
			if ($HomePh != "") {
				$phone = "<span class=\"bold\">H</span> " . $HomePh;
			}
			elseif ($MobPh != "") {
				$phone = "<span class=\"bold\">M</span> " . $MobPh;
			}
			elseif ($WorkPh != "") {
				$phone = "<span class=\"bold\">W</span> " . $WorkPh;
			}
			else {
				$phone = "&nbsp;&nbsp;&nbsp;" . $message[59];
			}
		}
		if ($Email1 == "" && $Email2 == "") {
			$email = $message[59];
		}
		else {
			if ($Email1 != "") {
				$email = "<a href=\"mailto:" . $Email1 . "\" title=\"Email " . $FirstName . " " . $LastName . "\">" . $Email1 . "</a>";
			}
			elseif ($Email2 != "") {
				$email = "<a href=\"mailto:" . $Email2 . "\" title=\"Email " . $FirstName . " " . $LastName . "\">" . $Email2 . "</a>";
			}
			else {
				$email = $message[59];
			}
		}

		# More HTML output...this is the list
		print("<tr>\n\t<td width=\"5%\" valign=\"top\" class=\"list_row\" align=\"center\">");
		# Is the user an admin?
		if ($is_admin == true) {
			print("<a href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;id=$id\" title=\"Edit this entry...\">$message[45]</a></td>");
		}
		else {
			print("&nbsp;</td>");
		}
		print("\n\t<td width=\"5%\" valign=\"top\" class=\"list_row\" align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?action=view&amp;id=$id\" title=\"View this record\">$message[46]</a></td>\n\t");
		print("<td valign=\"top\" width=\"35%\" class=\"list_row\">$LastName, $FirstName</td>\n\t");
		print("<td valign=\"top\" class=\"list_row\" width=\"35%\">$email</td>\n\t");
		print("<td class=\"list_row\" width=\"20%\">$phone</td>\n</tr>\n");
	}
	mysqli_free_result($result);
	# Finish up the HTML output...close the <table>
	print("<tr>\n\t<td colspan=\"5\" align=\"center\">");

	# Setup a 'Next/Prev' links but only if we've have more
	# records than the specified $max_list
	if ($total_records > $max_list) {

		# if the $pos is greater than 0, spit out a previous link
		if ($_GET['pos'] > 0) {
			$prev = $_GET['pos'] - 1;
			$url = $_SERVER['PHP_SELF'] . "?action=list&amp;pos=$prev";
				if (isset($_GET['rol'])) {
					$url .= "&amp;rol=.".$_GET['rol'];
				}
				print("&laquo; <a href=\"$url\" title=\"Previous Results\">$message[57]</a> |");
			}
		# Print out a link to each page until we reach the last page...
		for ($i = 0; $i < $total_pages; $i++) {
			$url = $_SERVER['PHP_SELF'] . "?action=list&amp;pos=" . $i;
				if (isset($_GET['rol'])) {
					$url .= "&amp;rol=".$_GET['rol'];
				}
				print(" <a href=\"$url\" title=\"Go to page $i\">-&nbsp;$i&nbsp;-</a> ");
			}
		# if the $pos is less than the total # of pages - 1,
		# add 1 to $pos and create a 'next' link
		if ($_GET['pos'] < $total_pages-1) {
			$next = $_GET['pos'] + 1;
			$url = $_SERVER['PHP_SELF'] . "?action=list&amp;pos=$next";
				if (isset($_GET['rol'])) {
					$url .= "&amp;rol=".$_GET['rol'];
				}
				print("| <a href=\"$url\" title=\"Next Results\">$message[58]</a> &raquo;");
			}
		}
	# if we don't have more records than the $max_list,
	# then just spit out a space
	else {
		print("&nbsp;");
	}
	print("</td>\n</tr>\n</table></center><br>");
}

# Search through the entries
######################################################
function search($field, $how, $str) {
	global $con, $img, $errorMsg, $message, $db_table, $is_admin;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Start the HTML output (<table>)
	print("<center><table class=\"viewTable\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#CCFF99\" align=\"center\">\n");
	print("<tr>\n\t<td colspan=\"5\" class=\"nameHeader\">$message[37]</td>\n</tr>\n");

	# Let's setup the query
	$sqlQuery = "SELECT DISTINCT * FROM $db_table WHERE ".$_GET['field']." LIKE";
	# How are we searching the $field
	if ($_GET['how'] == "begin") {
		$sqlQuery .= " '$str%'";
	}
	elseif ($_GET['how'] == "contain") {
		$sqlQuery .= " '%".$_GET['str']."%'";
	}
	elseif ($_GET['how'] == "end") {
		$sqlQuery .= " '%".$_GET['str']."'";
	}
	elseif ($_GET['how'] == "is") {
		$sqlQuery .= " '".$_GET['str']."'";
	}
	else {
		$sqlQuery .= " '%".$_GET['str']."%'";
	}
	$sqlQuery .= " ORDER BY ".$_GET['field'];

	$result = @mysqli_query($con,$sqlQuery) or die($errorMsg[3]);
	# Get the number or results...
	$num_rows = mysqli_num_rows($result);

	# Loop through the results and get the output
	for ($i = 0; $i < $num_rows; $i++) {
		mysqli_data_seek($result, $i);
		$array = mysqli_fetch_array($result);
		$id = $array['id'];
		$FirstName = $array['FirstName'];
		$LastName = $array['LastName'];
		$Email1 = $array['Email1'];
		$HomePh = $array['HomePh'];

		# If there's no HomePh in the database, don't display it
		# display one of the other phone numbers...
		# Doesn't exactly work like it should :/
		if ($HomePh == "" || $HomePh == " ") {
			if ($WorkPh == "" || $WorkPh == " ") {
				$phone = "(M) " . $array['MobilePh'];
			}
			else {
				$phone = "&nbsp;";
			}
		}
		else {
			$phone = "(H) " . $HomePh;
		}
		# More HTML output...this is the list
		print("<tr>\n\t<td width=\"31\" valign=\"top\" class=\"list_row\">");
		# Is the user an admin?
		if ($is_admin == true) {
			print("<a href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;id=$id\" title=\"Edit this entry...\">$message[45]</a></td>");
		}
		else {
			print("&nbsp;</td>");
		}
		print("\n\t<td width=\"31\" valign=\"top\" class=\"list_row\"><a href=\"".$_SERVER['PHP_SELF']."?action=view&amp;id=$id\" title=\"View this record\">$message[46]</a></td>\n\t<td valign=\"top\" width=\"30%\" class=\"list_row\">$LastName, $FirstName</td><td valign=\"top\" class=\"list_row\"><a href=\"mailto:$Email1\">$Email1</a></td><td class=\"list_row\">$phone</td>\n\t</tr>\n");
	}
	# Finish up the HTML output...close the <table>
	print("<tr>\n\t<td colspan=\"5\">");
	print("&nbsp;");
	print("</td>\n</tr>\n</table></center><br>");
}

# View entry
######################################################
function view_entry($id) {
	# Global vars needed by this function
	global $con, $img, $errorMsg, $message, $db_table, $db_auth_table, $is_admin, $userName, $userID;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# The SQL Query...grabs the record for the $id=$id	
	$sqlQuery = "SELECT * FROM $db_table WHERE 1 && id = '$id'";
	$result = @mysqli_query($con,$sqlQuery) or die($errorMsg[3]); 

	# Return the $data array
	$data = mysqli_fetch_array($result);

	# Let's get a more friendly output of variables shall we
	# the regular $data['<fieldName>'] would work, but it's
	# less desireable for what we are doing
	$f_name 	= $data['FirstName'];
	$l_name 	= $data['LastName'];
	$address1	= $data['Address1'];
	$address2	= $data['Address2'];
	$city		= $data['City'];
	$st			= $data['State'];
	$co			= $data['Country'];
	$post		= $data['PostalCode'];
	$h_phone	= $data['HomePh'];
	$w_phone	= $data['WorkPh'];
	$m_phone	= $data['MobilePh'];
	$o_phone	= $data['OtherPh'];
	$web		= $data['WebSite'];
	$email1		= $data['Email1'];
	$email2		= $data['Email2'];
	$icq		= $data['ICQ'];
	$aim		= $data['AIM'];
	$yahoo		= $data['Yahoo'];
	$comments	= $data['Comments'];
	$c_date		= $data['CreationDate'];
	$m_date		= $data['ModifyDate'];
	$who		= $data['ModifiedBy'];

	# This damn comma thing after the city is going to be the death of me
	# sooooo, let's add one if the city IS NOT empty...
	if ($city != "") {
		$city .= ",";
	}
	# The NEW output for HTML...this is going to be more complex I think :/
	print("<center><table class=\"viewTable\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" align=\"center\">\n");
	print("<tr>\n\t<td class=\"nameHeader\" colspan=\"4\">$f_name $l_name</td>\n</tr>\n");

	# If there is not Address2 in the database, don't show a blank line
	if ($address2 != "") {
		print("<tr>\n\t<td class=\"label\" valign=\"top\" width=\"15%\">$message[18]</td>\n\t<td class=\"text\" valign=\"top\" width=\"35%\">$address1<br>$address2<br>$city $st $post<br>$co</td>");
	}
	else {
		print("<tr>\n\t<td class=\"label\" valign=\"top\" width=\"15%\">$message[18]</td>\n\t<td class=\"text\" valign=\"top\" width=\"35%\">$address1<br>$city $st $post<br>$co</td>");
	}

	# The phone section...crude, but works. If there is no phone number, don't list it or the label for it.
	print("\n\t<td class=\"label\" valign=\"top\" width=\"15%\">");
	# These are the labels
	if ($h_phone != "") {
		print("$message[19]<br>");
	}
	if ($w_phone != "") {
		print("$message[20]<br>");
	}
	if ($m_phone != "") {
		print("$message[21]<br>");
	}
	if ($o_phone != "") {
		print("$message[22]<br>");
	}
	# The <table> cell next to the labels that contain the actual phone numbers
	print("</td>\n\t<td class=\"text\" valign=\"top\" width=\"35%\">");
	# These are the numbers
	if ($h_phone != "") {
		print("$h_phone<br>");
	}
	if ($w_phone != "") {
		print("$w_phone<br>");
	}
	if ($m_phone != "") {
		print("$m_phone<br>");
	}
	if ($o_phone != "") {
		print("$o_phone<br>");
	}
	# Close out the phone section
	print("</td>\n</tr>");

	# WebSite display
	print("<tr>\n\t<td class=\"label\" valign=\"top\" width=\"15%\">");
	# do we have a website?
	if ($web != "") {
		print("$message[23]");
	}
	else {
		print("&nbsp;");
	}
	print("</td>\n\t<td class=\"text\" valign=\"top\" width=\"35%\">");
	if ($web != "") {
		print("<a href=\"$web\" target=\"new\" title=\"$web\">$web</a></td>");
	}
	else {
		print("&nbsp;</td>");
	}

	# Email display...
	print("\n\t<td class=\"label\" valign=\"top\" width=\"15%\">");

	# if the person still lives in the ice age and doesn't
	# have an e-mail address...don't show the label for it
	# if email1 is there OR if email1 is NOT there but email2 IS...
	if ($email1 != "" || ($email1 == "" && $email2 != "")) {
		print("$message[24]");
	}
	else {
		print("&nbsp;");
	}
	print("</td>\n\t<td class=\"text\" valign=\"top\" width=\"35%\">");
	if ($email1 != "" || ($email1 == "" && $email2 != "")) {
		if ($email2 == "") {
			print("<a href=\"mailto:$email1\" title=\"E-mail $f_name\">$email1</a>");
		}
		elseif ($email1 == "" && $email2 != "") {
			print("<a href=\"mailto:$email2\" title=\"E-mail $f_name\">$email2</a>");
		}
		else {
			print("<a href=\"mailto:$email1\" title=\"E-mail $f_name\">$email1</a><br><a href=\"mailto:$email2\" title=\"E-mail $f_name\">$email2</a>");
		}
	}
	else {
		print("&nbsp;");
	}

	print("</td>\n</tr>");

	# Various contact means...ICQ, AIM, Yahoo! (Do you, uh, Yahoo?)
	# Alright...this is going to be messy...let's do an array maybe?
	$im = array ("icq" => $icq, "aim" => $aim, "yahoo" => $yahoo);
	# if there's nothing there, then there's no need to go on any
	# further...stop the buck here
	if ($icq == "" && $aim == "" && $yahoo == "") {
		print("");
	}
	# ok, we have one, do the rest...
	else {
		# Separate the key and value of the $im array
		# and output them in HTML...
		while (list($k, $v) = each($im)) {
			# Supress output of null values...
			if ($v != "") {
				# Icon sizes cause the damn ICQ icon is bigger than the others
				$icon_sz = getimagesize($img[$k]);
				print("<tr>\n\t<td class=\"label\" valign=\"top\" width=\"15%\">");
				print("<img src=\"$img[$k]\" $icon_sz[3] align=\"middle\" border=\"0\" alt=\"$k - $v\">");
				print("</td>\n\t<td class=\"text\" width=\"35%\">$v</td>\n\t");
				print("<td class=\"label\" width=\"15%\">&nbsp;</td>\n\t<td class=\"text\" width=\"35%\">&nbsp;</td>\n</tr>\n");
				# print("<img border=\"0\" width=\"18\" height=\"18\" align=\"middle\" alt=\"ICQ - $icq\" src=\"http://online.mirabilis.com/scripts/online.dll?icq=$icq&amp;img=5\">");
			}
		}
	}

	# Do we have any comments?
	if ($comments != "") {	
		print("<tr>\n\t<td class=\"label\">$message[25]</td>\n\t<td colspan=\"3\" class=\"text\">$comments</td>\n</tr>\n");
		print("<tr>\n\t<td colspan=\"4\" class=\"text\">&nbsp;</td>\n</tr>\n");
	}
	# Insert an empty <table> row for shits & giggles
	else {
		print("<tr>\n\t<td colspan=\"4\" class=\"text\">&nbsp;</td>\n</tr>\n");
	}
	# Let's finish this once and forall!
	print("<tr>\n\t<td colspan=\"4\" align=\"center\" class=\"borderTop\" style=\"padding: 6px;\" valign=\"middle\">");
	# Is the user an admin?
	if ($is_admin == true) {
		print("<input type=\"button\" value=\"$message[8]\" class=\"buttons\" onclick=\"javascript:location.href='".$_SERVER['PHP_SELF']."?action=edit&id=$id';\"> <input class=\"buttons\" type=\"button\" value=\"$message[9]\" onclick=\"javascript:location.href='".$_SERVER['PHP_SELF']."?action=delete&id=".$_GET['id']."';\"> ");
	}
	else {
		print("");
	}
	print("<input type=\"button\" class=\"buttons\" value=\"$message[50]\" onclick=\"javascript:location.href='".$_SERVER['PHP_SELF']."';\"><br><a href=\"print.php?id=$id\" target=\"new\">$message[51]</a>");
	print("</td>\n</tr>\n\t");
	print("<tr>\n<td class=\"dimed\" colspan=\"4\">This record was ");
	if (ereg("0000-00-00", $m_date) || $m_date == $c_date) {
		print("created on $c_date ");
	}
	else {
		print("modified on $m_date ");
	}

	if ($is_admin == true) {
		$userResult = mysqli_query($con,"SELECT uid FROM $db_auth_table WHERE username = '$who'") or die($errorMsg[3]);
		$userData = mysqli_fetch_array($userResult);
		$uid = $userData[0];
		$who = ucfirst($who);
		print("by <a href=\"".$_SERVER['PHP_SELF']."?action=mod_user&amp;uid=$uid\" title=\"$message[29] $who\">$who</a></td>\n</tr>\n</table></center>\n<br>");
	}
	else {
		$who = ucfirst($who);
		print("by $who</td>\n</tr>\n</table></center>\n<br>");
	}
}

# Print-friendly record
######################################################
function print_me($id) {
	global $con, $img, $errorMsg, $message, $is_admin, $db_table;
	global $_SERVER;
	global $_GET;
	global $_POST;

	$sqlQuery = "SELECT * FROM $db_table WHERE id = '".$_GET['id']."'";

	$result = @mysqli_query($con,$sqlQuery) or die($errorMsg[3]);

	$array = @mysqli_fetch_array($result);
	header("Content-type: text/html");
	echo "<b>" . $array['FirstName'] . " " . $array['LastName'] . "</b><br>";
	echo $array['Address1'];
	if ($array['Address2'] != "") {
		echo $array['Address2'] . "<br>";
	}
	else {
		echo "<br>";
	}
	echo $array['City'] . ", " . $array['State'] . " " . $array['PostalCode'] . "<br>";
	echo $array['Country'] . "<br><br>";
	echo "<a href=\"javascript:self.close();\" title=\"Close Window\">Close Window</a><br>";
}

# Edit entry
######################################################
function edit_entry($id) {
	# Global vars needed for this function
	global $userName, $action, $submitted, $con, $img, $errorMsg, $message, $db_table, $is_admin;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Check to make sure the person has the authority to do this
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		if ($_POST['submitted'] == "yes") {
			global $who, $id, $FirstName, $LastName, $Address1, $Address2, $City, $State, $PostalCode, $Country;
			global $HomePh, $WorkPh, $MobilePh, $OtherPh, $WebSite, $ICQ, $AIM, $Yahoo, $Email1, $Email2, $Comments;

			# The long-ass SQL Query that updates the record. I suppose this
			# could be automated using the $_POST array, but we haven't 
			# gotten that far yet, have we?
			$sqlQuery = "UPDATE $db_table SET ModifyDate = NOW(), 
					ModifiedBy 		= '".$_POST['who']."', 
					FirstName 		= '".$_POST['FirstName']."', 
					LastName 		= '".$_POST['LastName']."', 
					Address1 		= '".$_POST['Address1']."', 
					Address2 		= '".$_POST['Address2']."', 
					City 			= '".$_POST['City']."', 
					State 			= '".$_POST['State']."', 
					PostalCode 		= '".$_POST['PostalCode']."', 
					Country 		= '".$_POST['Country']."', 
					HomePh 			= '".$_POST['HomePh']."', 
					WorkPh 			= '".$_POST['WorkPh']."', 
					MobilePh 		= '".$_POST['MobilePh']."', 
					OtherPh 		= '".$_POST['OtherPh']."', 
					WebSite 		= '".$_POST['WebSite']."', 
					ICQ 			= '".$_POST['ICQ']."', 
					AIM 			= '".$_POST['AIM']."', 
					Yahoo 			= '".$_POST['Yahoo']."', 
					Email1 			= '".$_POST['Email1']."', 
					Email2 			= '".$_POST['Email2']."', 
					Comments 		= '".$_POST['Comments']."' 
					WHERE id 		= '".$_POST['id']."' 
					LIMIT 1";

			# Send the $sqlQuery
			$result = mysqli_query($con, $sqlQuery) or die($errorMsg[3]);
			$num_rows_affected = mysqli_affected_rows();
			# If the $result is true (meaning, it executed without errors)
			# this lets the user know everything is kosher	
			if ($num_rows_affected = 1) {
				print_msg($message[12], $message[3], $_SERVER['PHP_SELF'] . "?action=view&amp;id=".$_POST['id']);
			}
			# If the $result is not true (meaning there was some kind of error beyond our control)
			# let the user know that something horrible went wrong and that they should feel very bad
			# for breaking the addressbook
			else {
				print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF'] . "?action=view&amp;id=".$_POST['id']);
			}
		}
		else {

			# This query get's the record for editing
			$sqlQuery = "SELECT * FROM $db_table WHERE 1 && id = '$id'";
			$result = mysqli_query($con,$sqlQuery) or
				die($errorMsg[3]);
			$array = mysqli_fetch_array($result);
			# get a count of the number of fields in the table
			# this is easier and less of a pain than creating all the <input>
			# tags for the form...trust me.
			$num_fields = mysqli_num_fields($result);
			# Start the HTML output...the <form> and the <table>
			print("<center><form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
			print("<table width=\"90%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" class=\"viewTable\" align=\"center\">\n");
			print("<tr>\n\t<td colspan=\"2\" class=\"nameHeader\">$message[8]</td>\n</tr>\n");

			# Loop through the fields and get the name, type and length of each one
			# That way we can automate the process a little and save some steps in the
			# longrun.
			for ($i = 0; $i < $num_fields; $i++) {
				$field_name = mysql_field_name($result, $i);
				$field_type = mysql_field_type($result, $i);
				$field_len  = mysql_field_len($result, $i);
				# If the field_len is greater than 25, just limit it's size to 25 chars
				# we don't need 100 character text fields in the HTML ;)
				if ($field_len > 25) {
					$field_size = 25;
				}
				else {
					$field_size = $field_len;
				}
				# If the type of the field is a 'blob' (or text, they're the same for the most part)
				# output a <textarea> instead of a <input type=text>
				if (eregi("blob", $field_type)) {
					print("<tr>\n\t<td width=\"20%\" class=\"label\" valign=\"top\">$field_name:</td>\n<td valign=\"top\"><textarea cols=\"23\" rows=\"8\" name=\"$field_name\">$array[$i]</textarea></td>\n</tr>\n");
				}
				# If the field type is a intiger, do create an <input> tag for it as the id is a 
				# unique number generated by MySQL to identify each record.
				elseif (eregi("int", $field_type)) {
					print("<tr>\n\t<td width=\"20%\" class=\"label\">$field_name:</td>\n<td><input type=\"hidden\" name=\"$field_name\" value=\"$array[$i]\">$array[$i]</td>\n</tr>\n");
				}
				elseif ($field_type == "datetime" || $field_name == "ModifiedBy") {
					print("");
				}
				# Output the HTML for the fields
				else {
					print("<tr>\n\t<td width=\"20%\" class=\"label\">$field_name:</td>\n<td><input type=\"text\" name=\"$field_name\" size=\"$field_size\" maxlength=\"$field_len\" value=\"$array[$i]\"></td>\n</tr>\n");
				}
			}
			# Finish up the <table> and the <form>	
			echo "<tr>\n\t<td>\n<input type=\"hidden\" name=\"who\" value=\"$userName\">\n
				<input type=\"hidden\" name=\"action\" value=\"edit\">\n
				<input type=\"hidden\" name=\"submitted\" value=\"yes\">\n
				</td>\n\t
				<td>
				<input type=\"submit\" class=\"buttons\" name=\"ok\" value=\"$message[56]\"> 
				<input type=\"button\" class=\"buttons\" value=\"$message[49]\" onclick=\"javascript:history.go(-1);\"></a></td>\n</tr>\n";
			print("</table>\n<br>\n</form></center>\n");
		}
	}
}

# Add entry
######################################################
function add_entry($FirstName, $LastName) {
	# Global vars needed for this function
	global $con, $img, $errorMsg, $message, $db_table, $is_admin, $userName;
	global $_SERVER;
	global $_GET;
	global $_POST;

if(empty($FirstName)){$FirstName="";}
if(empty($LastName)){$LastName="";}


	# Does the user have permission to do this?
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		# Again, this would be less of a pain with the $_POST array but alas, this works
		global $who, $submitted, $action, $FirstName, $LastName, $Address1, $Address2, $City, $State, $PostalCode, $Country;
		global $HomePh, $WorkPh, $MobilePh, $OtherPh, $WebSite, $ICQ, $AIM, $Yahoo, $Email1, $Email2, $Comments;

		# if the form hasnt been passed yet...lets get an array of form fields to spit out...
		if (!isset($_POST['submitted'])) {
			# Print out the <form> and <table> start tags
			print("<div align=\"center\"><form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
			print("<table width=\"90%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" class=\"viewTable\" align=\"center\">\n");
			print("<tr>\n\t<td colspan=\"2\" class=\"nameHeader\">$message[13]</td>\n</tr>\n");

			# the Query that gets the table field properties
			$formQuery = "SELECT * FROM $db_table WHERE id = '1'";

			$formResult = @mysqli_query($con,$formQuery) or
				die($errorMsg[3]);
			$form_data = mysqli_fetch_array($formResult);
			$numFields = mysqli_num_fields($formResult);

			# Loop through the fields and get the various properties
			# this is another one of those automated goodies :)
			for ($i = 0; $i < $numFields; $i++) {
				
				$fieldinfo=mysqli_fetch_field_direct($formResult,$i);
				
				$field_type = $fieldinfo->type;
				$field_name = $fieldinfo->name;
				$field_len = $fieldinfo->max_length;
				
				//$field_type = mysql_field_type($formResult, $i);
				//$field_name = mysql_field_name($formResult, $i);
				//$field_len = mysql_field_len($formResult, $i);
				# If the field_len is greater than 25, limit the <input> size to 25
				# We don't need 100 character <input> boxes messing up the look
				if ($field_len > 25) {
					$field_size = 25;
				}
				else {
					$field_size = $field_len;
				}

				# If the field_type is a blob (or text)
				# output a <textarea> instead of a <input>
				if (preg_match("/blob/", $field_type)) {
					print("<tr>\n\t<td width=\"20%\" class=\"label\" valign=\"top\">$field_name:</td>\n<td valign=\"top\"><textarea cols=\"23\" rows=\"8\" name=\"$field_name\"></textarea></td>\n</tr>\n");
				}
				# If the field_type is an intiger
				# do not output a <input> as this is
				# a unique id used to identify the record
				# and is automatically generated by MySQL
				elseif (preg_match("/int/", $field_type)) {
					$field_name = strtoupper($field_name);
					print("<tr>\n\t<td width=\"20%\" class=\"label\">$field_name:</td>\n<td>$message[15]</td>\n</tr>\n");
				}
				# Let's not display the mod/create date and who info...
				elseif(preg_match("/datetime/", $field_type) || $field_name == "ModifiedBy") {
					print("");
				}
				# Everything else looks OK, so output the rest
				# of the HTML as <input> tags
				else {
					print("<tr>\n\t<td width=\"20%\" class=\"label\">$field_name:</td>\n<td><input type=\"text\" name=\"$field_name\" size=\"$field_size\" maxlength=\"$field_len\" value=\"$form_data[$i]\"></td>\n</tr>\n");
				}

			}
			# Finish up the HTML output of the <form> and the <table>
			print("<tr>\n\t<td>&nbsp;\n<input type=\"hidden\" name=\"who\" value=\"$userName\">\n<input type=\"hidden\" name=\"action\" value=\"add\"><input type=\"hidden\" name=\"submitted\" value=\"yes\"></td>\n\t<td><input type=\"submit\" class=\"buttons\" value=\"$message[13]\"> <input type=\"button\" class=\"buttons\" value=\"$message[49]\" onClick=\"javascript:history.go(-1);\"></td>\n</tr>\n");
			print("</table>\n<br>\n</form></div>\n");

		}
		# if the form has been submitted, fill the database with goodness
		elseif ($_POST['submitted'] == "yes") {

			# Another long-ass SQL query that could be less intimidating
			# by the use of $_POST
			$sqlQuery = "INSERT INTO $db_table SET id = '', 
					CreationDate 	= NOW(), 
					ModifyDate 		= NOW(), 
					ModifiedBy 		= '".$_POST['who']."', 
					FirstName 		= '".$_POST['FirstName']."', 
					LastName 		= '".$_POST['LastName']."', 
					Address1 		= '".$_POST['Address1']."', 
					Address2 		= '".$_POST['Address2']."', 
					City 			= '".$_POST['City']."', 
					State 			= '".$_POST['State']."', 
					PostalCode 		= '".$_POST['PostalCode']."', 
					Country 		= '".$_POST['Country']."', 
					HomePh 			= '".$_POST['HomePh']."', 
					WorkPh 			= '".$_POST['WorkPh']."', 
					MobilePh 		= '".$_POST['MobilePh']."', 
					OtherPh 		= '".$_POST['OtherPh']."', 
					WebSite 		= '".$_POST['WebSite']."', 
					ICQ 			= '".$_POST['ICQ']."', 
					AIM 			= '".$_POST['AIM']."', 
					Yahoo 			= '".$_POST['Yahoo']."', 
					Email1 			= '".$_POST['Email1']."', 
					Email2 			= '".$_POST['Email2']."', 
					Comments 		= '".$_POST['Comments']."'";

			# Execute the query
			$result = mysqli_query($con,$sqlQuery) or
				die($errorMsg[3]);

			# If the $result is true (ie. the query went through and the info was added to the database)
			# give the user that warm fuzzy feeling of doing something right for once in his life
			if ($result == true) {
				print_msg($message[14], $message[2], $_SERVER['PHP_SELF']);
			}
			# If the $result is not true (ie. the query fails and no data is passed to the database)
			# let the user know that something horrible has gone wrong and that he/she should
			# go sit in the corner and think about what he/she has done
			else {
				print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF']);
			}
		}
	}
}


# Delete entry - self explanetory I think
######################################################
function delete_entry($id) {
	# Global vars needed for this function
	global $con, $img, $errorMsg, $message, $db_table, $confirmed, $is_admin;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Does the user have permission to do this?
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		# The all-destructing DELETE query (actually, this is limited to 
		# to 1 (one) row, so it's not that destructive)
		$sqlQuery = "DELETE FROM $db_table WHERE 1 && id = '".$_GET['id']."' LIMIT 1";

		# If the deletion has been confirmed, then delete the record
		if ($_GET['confirmed'] == "yes") {
			# Execute the DELETE query
			$result = @mysqli_query($con,$sqlQuery) or
				die($errorMsg[3]);

			# If the $result is true, let the user know that record
			# has been deleted and that the contact has been permenantly
			# removed from their lives...I hope this makes them feel better!
			if ($result == true) {
				print_msg($message[11], $message[4], $_SERVER['PHP_SELF']);
			}
			# If the $result is not true, tell the user that something went wrong
			# and that they can't just delete people from their lives by clicking
			# a button!
			else {
				print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF']);
			}
		}
		# If the deletion has NOT been confirmed, ask the user if they are absolutely SURE
		# that they want to delete the record...KILL KILL KILL!!
		else {
			print("<table class=\"viewTable\" width=\"60%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n");
			print("<tr>\n\t<td class=\"nameHeader\">$message[9]</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\" align=\"center\">$message[5]</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\" align=\"center\"><br><input type=\"button\" class=\"buttons\" value=\"$message[47]\" onclick=\"javascript:location.href='".$_SERVER['PHP_SELF']."?action=delete&id=".$_GET['id']."&confirmed=yes';\"> <input type=\"button\" class=\"buttons\" value=\"$message[48]\" onclick=\"javascript:history.go(-1);\"><br><br></td>\n</tr>");
			print("</table>\n<br>");
		}
	}
}

# Admin Area - For modifying/adding/removing users
######################################################
######################################################

# The admin interface - prints out a set of tools
# to be used by admins
######################################################
function user_admin() {
	global $con, $img, $errorMsg, $message, $is_admin, $db_table, $db_auth_table;
	global $u_user, $u_pass, $u_admin, $uid, $userID;

	# Let's weed out the admins from the grunts now..
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	# OK, the user is an admin...let him in
	else {
		# Let's start the <table> and header <td>
		print("<center><table width=\"90%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\" class=\"viewTable\" align=\"center\">\n");
		print("<tr>\n\t<td class=\"nameHeader\">$message[26]</td>\n</tr>\n");

		# Empty cell for a spacer
		print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");

		# Let's start an 'adduser' first since it's probably the easiest
		# and I'm feeling lazy right now
		print("<tr>\n\t<td class=\"subHeader\">$message[27]</td>\n</tr>\n");
		print("<tr>\n\t<td>\n");
		print("<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n");
		print("<table width=\"90%\" cellpadding=\"4\" celspacing=\"0\" border=\"0\" align=\"center\">\n");
		print("<tr>\n\t<td class=\"label\">$message[16]</td>\n\t<td><input type=\"text\" name=\"u_user\" maxlength=\"15\" size=\"20\"></td>\n</tr>\n");
		print("<tr>\n\t<td class=\"label\">$message[17]</td>\n\t<td><input type=\"password\" name=\"u_pass1\" maxlength=\"15\" size=\"20\"></td>\n</tr>\n");
		print("<tr>\n\t<td class=\"label\">$message[35]</td>\n\t<td><input type=\"password\" name=\"u_pass2\" maxlength=\"15\" size=\"20\"></td>\n</tr>\n");
		print("<tr>\n\t<td class=\"label\">$message[30]</td>\n\t<td><select name=\"u_admin\"><option value=\"NO\" name=\"NO\" selected>NO</option><option value=\"YES\" name=\"YES\">YES</option></select></td>\n</tr>\n");
		print("<tr>\n\t<td class=\"label\">&nbsp;<input type=\"hidden\" name=\"action\" value=\"add_user\"></td>\n<td><input class=\"buttons\" type=\"submit\" name=\"add_user\" value=\"$message[27]\"></td>\n</tr>\n");
		print("</table>\n</form>\n");

		# Empty cell for a spacer
		print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");

		# Header Cell for deleting a user..
		print("<tr><td class=\"subHeader\">$message[28]</td>\n</tr>\n");
		print("<tr><td class=\"text\" align=\"center\">\n");
		# get the list of users in the $db_auth_user table
		$del_result = @mysqli_query($con,"SELECT uid, username FROM $db_auth_table ORDER BY uid") or
			die($errorMsg[3]);

		$del_numrows = @mysqli_num_rows($del_result);
		# Print out the start of the <select>
		print("<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		print("<select name=\"uid\">\n");
		print("<option selected disabled>Select User...</option>\n");
		# Loop the the results to get the <option>
		for ($i = 0; $i < $del_numrows; $i++) {
			mysqli_data_seek($del_result, $i);
			$del_array = @mysqli_fetch_array($del_result);
			$uid = $del_array['uid'];
			$u_user = $del_array['username'];
			if ($uid != $userID) {
				print("<option value=\"$uid\">$u_user</option>\n");
			}
		}
		print("</select>&nbsp;&nbsp;<input class=\"buttons\" type=\"submit\" name=\"del_user\" value=\"$message[28]\">\n<input type=\"hidden\" name=\"action\" value=\"del_user\">\n</form>\n");

		# Empty cell for a spacer
		print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");

		# Edit user...
		print("<tr>\n\t<td class=\"subHeader\">$message[29]</td>\n</tr>\n");
		print("<tr><td class=\"text\" align=\"center\">\n");
		print("<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		print("<select name=\"uid\">\n");
		print("<option selected disabled>Select User...</option>\n");
		# Loop the the results to get the <option>
		for ($i = 0; $i < $del_numrows; $i++) {
			mysqli_data_seek($del_result, $i);
			$del_array = @mysqli_fetch_array($del_result);
			$uid = $del_array['uid'];
			$u_user = $del_array['username'];
			print("<option value=\"$uid\">$u_user</option>\n");
		}
		print("</select>&nbsp;&nbsp;<input class=\"buttons\" type=\"submit\" name=\"mod_user\" value=\"$message[29]\"><input type=\"hidden\" name=\"action\" value=\"mod_user\"></form>\n");

		# Empty cell for a spacer
		print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");

		# Spit out a 'Done' buttons so the user can exit the admin area
		print("<tr>\n\t<td class=\"text\" align=\"center\"><input type=\"button\" class=\"buttons\" value=\"$message[50]\" onclick=\"javascript:location.href='".$_SERVER['PHP_SELF']."';\"></td>\n</tr>\n");

		# The bottom of the table...
		print("\t</td>\n</tr>\n</table></center>\n");
	}
}

# Add User to Database
function adduser($u_user, $u_pass1, $u_pass2, $u_admin) {
	global $is_admin, $con, $db_auth_table, $message, $errorMsg, $action;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Weed out the losers...
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		if (empty($_POST['u_pass1'])) {
			print_msg($message[27], $errorMsg[8], $_SERVER['PHP_SELF']."?action=admin");
			exit;
		}
		elseif (md5($_POST['u_pass1']) != md5($_POST['u_pass2'])) {
			print_msg($message[27], $errorMsg[7], $_SERVER['PHP_SELF']."?action=admin");
			exit;
		}
		else {
			$u_pass = $u_pass1;
		}
		$sqlQuery = "INSERT INTO $db_auth_table (uid, username, password, admin) 
				VALUES ('', '".strtolower($_POST['u_user'])."', MD5('".$u_pass."'), '".$_POST['u_admin']."')";
		$result = mysqli_query($con,$sqlQuery) or
			die($errorMsg[3].mysqli_error($con));
		if ($result == true) {
			print_msg($message[27], $message[32], $_SERVER['PHP_SELF']."?action=admin");
		}
		else {
			print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF']."?action=admin");
		}
	}
}

# Delete user...
function rmuser($uid) {
	global $img, $con, $is_admin, $is_confirmed, $message, $errorMsg, $db_auth_table;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Weed out the losers
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		# Let's get a confirmation....
		if ($_POST['is_confirmed'] == "yes") {
			$sqlQuery = "DELETE FROM $db_auth_table WHERE uid = '".$_POST['uid']."' LIMIT 1";
			$result = @mysqli_query($con,$sqlQuery) or
				die($errorMsg[3]);

			if ($result == true) {
				print_msg($message[28], $message[33], $_SERVER['PHP_SELF']."?action=admin");
			}
			else {
				print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF']."?action=admin");
			}
		}
		else {
			# We need to get the user's name that we are deleting for the confirmation page...
			# we were getting it from the main admin page, but, obviously, it wasn't working...
			$nameQuery = "SELECT username FROM $db_auth_table WHERE uid = '".$_POST['uid']."' LIMIT 1";
			$nameResult = mysqli_query($con,$nameQuery) or
				die($errorMsg[3]);
			$nameData = mysqli_fetch_array($nameResult);
			$user = ucfirst($nameData[0]);

			print('<form action="'.$_SERVER['PHPSELF'].'" method="post">');
			print('<input type="hidden" name="action" value="del_user"><input type="hidden" name="is_confirmed" value="yes"><input type="hidden" name="uid" value="'.$_POST['uid'].'">');
			print("<table class=\"viewTable\" width=\"60%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n");
			print("<tr>\n\t<td class=\"nameHeader\">$message[28]: $user</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\" align=\"center\">$message[31]</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\" align=\"center\"><br><input type=\"submit\" class=\"buttons\" value=\"$message[47]\"> <input type=\"button\" class=\"buttons\" value=\"$message[48]\" onclick=\"javascript:history.go(-1);\"><br><br></td>\n</tr>");
			print("</table>\n");
			print('</form><br>');
		}
	}
}

# Modify User
function moduser($uid) {
	global $img, $message, $errorMsg, $con, $is_admin, $is_confirmed, $db_auth_table;
	global $action, $u_pass1, $u_pass2, $u_admin, $u_name, $uid;
	global $_SERVER;
	global $_GET;
	global $_POST;

	# Weed out the losers...
	if ($is_admin == false) {
		print_msg($errorMsg[5], $errorMsg[6], $_SERVER['PHP_SELF']);
	}
	else {
		if ($_POST['is_confirmed'] == "yes") {
			# Start the query...
			$sqlQuery = "UPDATE $db_auth_table SET admin = '".$_POST['u_admin']."'";
			if (!empty($_POST['u_pass1']) && !empty($_POST['u_pass2'])) {
				if (md5($_POST['u_pass1']) == md5($_POST['u_pass2'])) {
					$sqlQuery .= ", password = MD5('".$_POST['u_pass1']."')";
				}
				else {
					print_msg($message[29], $errorMsg[7], "javascript:history.go(-1);");
					exit;
				}
			}

			$sqlQuery .= " WHERE uid = '".$_POST['uid']."' LIMIT 1";

			# Run the query...
			$result = @mysqli_query($con,$sqlQuery) or die($errorMsg[3]);

			if ($result == true) {
				print_msg($message[29], $message[34], $_SERVER['PHP_SELF']."?action=admin");
				exit;
			}
			else {
				print_msg($message[666], $errorMsg[666], $_SERVER['PHP_SELF']."?action=admin");
				exit;
			}
		}
		else {
			# Print out the moduser interface...
			# First let's get the users info...
			$userSQL = "SELECT * FROM $db_auth_table WHERE uid = '".$_POST['uid']."' LIMIT 1";
			$userResult = @mysqli_query($con,$userSQL) or
				die($errorMsg[3]);
			$userData = mysqli_fetch_array($userResult);
			$u_name = ucfirst($userData['username']);
			$u_admin = $userData['admin'];

			# Start the HTML output...
			print("<center><form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
			# Some hidden inputs
			print("<input type=\"hidden\" name=\"uid\" value=\"".$_POST['uid']."\">\n<input type=\"hidden\" name=\"action\" value=\"mod_user\">\n<input type=\"hidden\" name=\"is_confirmed\" value=\"yes\">\n<input type=\"hidden\" name=\"u_name\" value=\"$u_name\">\n");
			print("<table class=\"viewTable\" width=\"90%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\">\n");
			print("<tr>\n\t<td class=\"nameHeader\">$message[26]</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"subHeader\">$message[29]: $u_name</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\" align=\"center\">\n\n");
			print("<table width=\"80%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n");
			print("<tr>\n\t<td class=\"label\">UID:</td>\n\t<td class=\"text\">".$_POST['uid']."</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"label\">$message[16]</td>\n\t<td class=\"text\">$u_name</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"label\">$message[36]</td>\n<td class=\"text\"><input type=\"password\" name=\"u_pass1\" maxlength=\"15\" size=\"20\"></td>\n</tr>\n");
			print("<tr>\n\t<td class=\"label\">$message[35]</td>\n<td class=\"text\"><input type=\"password\" name=\"u_pass2\" maxlength=\"15\" size=\"20\"></td>\n</tr>\n");
			print("<tr>\n\t<td class=\"label\">$message[30]</td>\n<td class=\"text\">\n");

			print("<select name=\"u_admin\">\n");
			if ($u_admin == "YES") {
				print("<option value=\"YES\" selected>YES</option>\n<option value=\"NO\">NO</option>\n");
			}
			else {
				print("<option value-\"NO\" selected>NO</option>\n<option value=\"YES\">YES</option>\n");
			}
			print("</select>\n");
			print("</td>\n</tr>\n");
			print("<tr>\n\t<td>&nbsp;</td>\n<td class=\"text\"><input class=\"buttons\" type=\"submit\" name=\"nod_user\" value=\"$message[29]\"> <input type=\"button\" class=\"buttons\" value=\"$message[49]\" onclick=\"javascript:history.go(-1);\"></td>\n</tr>\n");
			print("</table>\n");

			print("</td>\n</tr>\n");
			print("<tr>\n\t<td class=\"text\">&nbsp;</td>\n</tr>\n");
			print("</table>\n</form></center>\n");
		}
	}
}
mysqli_close($con);
?>