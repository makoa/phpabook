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
# Description: Common Labels/Messages (English)
#
# Date: 2016-06-06
# File: en/inc.messages.php
##############################################################################
if(!isset($u_user)){$u_user="";}
if(!isset($str)){$str="";}

$message = array (
	# Welcome Messages
	1 => "Welcome to Abook",
	# Confirmation Messages
	2 => "Contact information has been added.",
	3 => "Contact information has been updated.",
	4 => "This contact has been removed from the address book.",
	5 => "Are you sure you want to delete this contact? This action is <b>NOT</b> reversable.",
	# Labels
	6 => "Please Login",
	7 => "Contact List",
	8 => "Edit Contact",
	9 => "Delete Contact",
	10 => "Search Abook",
	11 => "Contact Deleted",
	12 => "Contact Updated",
	13 => "Add Contact",
	14 => "Contact Added",
	15 => "Automatically Generated",
	16 => "Username:",
	17 => "Password:",
	18 => "Address:",
	19 => "Home Phone:",
	20 => "Work Phone:",
	21 => "Mobile Phone:",
	22 => "Other Phone:",
	23 => "Web Site:",
	24 => "E-Mail:",
	25 => "Comments:",
	# Admin Area Messages
	26 => "User Administration",
	27 => "Add User",
	28 => "Delete User",
	29 => "Modify User",
	30 => "Admin User:",
	31 => "Are you sure you want to delete this user? This action is <b>NOT</b> reversable.",
	32 => "User <b>$u_user</b> has been added to the authorized user list",
	33 => "The user <b>$u_user</b> has been removed from the list of authorized user list.",
	34 => "User <b>$u_user</b> has been modified.",
	35 => "Confirm Password:",
	36 => "New Password:",
	37 => "Search Results: $str",
	38 => "Find contact(s) who's",
	39 => "contains",
	40 => "starts with",
	41 => "ends with",
	42 => "is",
	43 => "Login",
	44 => "Logout",
	45 => "Edit",
	46 => "View",
	47 => "YES",
	48 => "NO",
	49 => "Cancel",
	50 => "Done",
	51 => "Print Address",
	52 => "Print All",
	53 => "Search",
	54 => "Language",
	55 => "Show All",
	56 => "OK",
	57 => "Previous",
	58 => "Next",
	59 => "None",

	# unknown error...DOH!
	666 => "Unknown Error!"
	);
$errorMsg = array (
	# Database Errors
	1 => "Unable to connect to the database server on '<i>$db_host</i>'",
	2 => "Unable to select database '<i>$db_db</i>' on '<i>$db_host</i>'",
	3 => "Unable to perform query.",
	# Login Errors
	4 => "<b>Invalid Login:</b> Please enter a valid Username and Password.",
	# Permission errors
	5 => "Access Denied",
	6 => "Sorry, you are not an admin and can not perform this action. Please talk to the owner of phpAbook for permission to complete this action.",
	7 => "The passwords don't match...<br><a href=\"javascript:history.go(-1);\">Back</a>",
	8 => "You really shouldn't use a null password...<br><a href=\"javascript:history.go(-1);\">Back</a>",

	# Unkown Error...DOH!
	666 => "An unknown error has occured. Please contact <a href=\"mailto:gilneim@hotmail.com\">gilneim@hotmail.com</a> with detailed information on how you got this error."
	);
$rolArray = array(
	1 => "A",
	2 => "B",
	3 => "C",
	4 => "D",
	5 => "E",
	6 => "F",
	7 => "G",
	8 => "H",
	9 => "I",
	10 => "J",
	11 => "K",
	12 => "L",
	13 => "M",
	14 => "N",
	15 => "O",
	16 => "P",
	17 => "Q",
	18 => "R",
	19 => "S",
	20 => "T",
	21 => "U",
	22 => "V",
	23 => "W",
	24 => "X",
	25 => "Y",
	26 => "Z"
	);

?>