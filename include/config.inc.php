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
# Description: Config file for phpAbook
#
# Date: 2016-06-06
# File: config.inc.php
##############################################################################

# Database Info
$db_host = "localhost";				// host where mysql resides
$db_user = "";				// database username
$db_pass = "";				// password for 'username'
$db_db = "phpabook";				// Database that 'username' has access to
$db_table = "ab_records";			// Table where the Abbok is stored. Should be left
									// as is unless this was changed at install time
$db_auth_table = "ab_auth_user";	// This is the table where the authorized users live
									// This should be left be as well, unless changed during install.

# Max number of entries per page
$max_list = "20";

# Default language - this is the default language the phpAbook uses 
# the user can choose the language of their choice at login time for
# any language installed. At the time of this writing only 3 exist:
# en = English, de = German, nl = Netherlands/Dutch
$default_lang = "en";

# Images - the directory in which the images are stored
$img_dir = "images/";		// images directory (note the trailing slash)

# images array - in order to be more portable to different languages
# I have chosen NOT to use images in the layout of phpAbook as of version 0.7b
# The images that are left, are the only ones used in the design...sorry
$img = array (
	"header"	=> $img_dir . "abook_header.gif",
	"icq"		=> $img_dir . "abook_icq.gif",
	"aim"		=> $img_dir . "abook_aim.gif",
	"yahoo"		=> $img_dir . "abook_yahoo.gif"
	);

#     Nothing needs to be changed below this line     #
#######################################################
# include the proper language files
if (isset($_COOKIE["userInfo"]) && $_COOKIE["userInfo"] != "") {
	$userArray = explode(" ", $_COOKIE["userInfo"]);
	$userName = $userArray[0];
	$userID = $userArray[1];
	$userLang = $userArray[2];
	include("include/lang/$userLang/inc.messages.php");
}
else {
	include("include/lang/$default_lang/inc.messages.php");
}
# Version info // Please don't change this
$abook_vers = "phpAbook v9.0 Intermediate";
?>