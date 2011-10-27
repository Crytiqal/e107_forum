<?php

require_once("../../class2.php");

mysql_query("ALTER TABLE ".MPREFIX."user ADD( user_forum_oldtimestamp int(10) unsigned NOT NULL default '0', user_forum_newtimestamp int(10) unsigned NOT NULL default '0', user_forum_newposts int(10) unsigned NOT NULL default '0')") or die(mysql_error());

?>