<?php
/*
+ ----------------------------------------------------------------------------------------------------+
|        e107 website system 
|        Email: crytiqal@team-aero.org
|        Organization: Team-Aero Copyright 2011 - www.team-aero.org
|        $Id: admin_menu.php 659 2011-10-09 12:07:49Z crytiqal $
|        License: GNU GENERAL PUBLIC LICENSE - http://www.gnu.org/licenses/gpl.txt
+----------------------------------------------------------------------------------------------------+
*/
include_lan(e_PLUGIN.'forum/languages/'.e_LANGUAGE.'/lan_forum_admin_menu.php');

if (e_QUERY)
{
	$tmp = explode(".", e_QUERY);
	$action = $tmp[0]; //needed by auth.php
	$sub_action = varset($tmp[1]);
	$id = intval(varset($tmp[2], 0));
	unset($tmp);
}

if (!defined('e107_INIT')) { exit; }
global $pageid;

	$menutitle = FAM_LAN_M;//"Menu Title";

	$butname[] = FAM_LAN_M1;//Main
	$butlink[] = "admin_config.php";  
	$butid[] = "main"; 

	$butname[] = FAM_LAN_M2;//parents
	$butlink[] = "admin_config.php?parents.create";  
	$butid[] = "parents";
	
	if($action == "parents"){
		$butname[] = FAM_LAN_MS1;//parents.create
		$butlink[] = "admin_config.php?parents.create";  
		$butid[] = "parents.create";
	
		$butname[] = FAM_LAN_MS2;//parents.edit
		$butlink[] = "admin_config.php?parents.edit.$id";  
		$butid[] = "parents.edit";

		$butname[] = FAM_LAN_MS3;//parents.delete
		$butlink[] = "admin_config.php?parents.delete.$id";  
		$butid[] = "parents.delete";
		}	 

	if(mysql_num_rows(mysql_query("SELECT forum_parent_id FROM ".MPREFIX."forum_p"))){
	$butname[] = FAM_LAN_M3;//forums
	$butlink[] = "admin_config.php?forums.create";  
	$butid[] = "forums";

	if($action == "forums"){
		$butname[] = FAM_LAN_MS1;//forums.create
		$butlink[] = "admin_config.php?forums.create";  
		$butid[] = "forums.create";
	
		$butname[] = FAM_LAN_MS2;//forums.edit
		$butlink[] = "admin_config.php?forums.edit.$id";  
		$butid[] = "forums.edit";

		$butname[] = FAM_LAN_MS3;//forums.delete
		$butlink[] = "admin_config.php?forums.delete.$id";  
		$butid[] = "forums.delete";
		}

	} 

	$butname[] = FAM_LAN_M4;//preferences
	$butlink[] = "admin_config.php?prefs";  
	$butid[] = "prefs";

	$butname[] = FAM_LAN_M5;//moderators
	$butlink[] = "admin_config.php?mods";  
	$butid[] = "mods";

	$butname[] = FAM_LAN_M6;//usermodules
	if(!mysql_num_rows(mysql_query("SELECT user_forum_oldtimestamp FROM ".MPREFIX."user"))){
	$butlink[] = "admin_config.php?usermodules.install";  
     } else {
	$butlink[] = "admin_config.php?usermodules.uninstall";  
	}
	$butid[] = "usermodules";

	for ($i=0; $i<count($butname); $i++) {
        $var[$butid[$i]]['text'] = $butname[$i];
		$var[$butid[$i]]['link'] = $butlink[$i];
	};

    show_admin_menu($menutitle,$pageid, $var);
?>