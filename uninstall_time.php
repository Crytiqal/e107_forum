<?php

require_once("../../class2.php");

mysql_query("ALTER TABLE ".MPREFIX."user DROP user_forum_oldtimestamp, DROP user_forum_newtimestamp, DROP user_forum_newposts") or die(mysql_error());

?>