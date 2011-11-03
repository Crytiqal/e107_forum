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

// -------------------------------------------------------- Don't change anything below this line! --+

if(!empty($_POST) && !isset($_POST['e-token']))
{
	// set e-token so it can be processed by class2
	$_POST['e-token'] = ''; // TODO - regenerate token value just after access denied?
}
require_once("../../class2.php");
include_lan(e_PLUGIN.'forum/languages/'.e_LANGUAGE.'/lan_forum_admin.php');

if (!getperms("P"))
{
	header("location:".e_BASE."index.php");
	exit;
}

if (e_QUERY)
{
	$tmp = explode(".", e_QUERY);
	$action = $tmp[0]; //needed by auth.php
	$sub_action = varset($tmp[1]);
	$id = varset($tmp[2]);
	$sub_id = intval(varset($tmp[3], 0));
	unset($tmp);
}

require_once(e_ADMIN.'auth.php');
require_once(e_HANDLER."userclass_class.php");
require_once(e_HANDLER."form_handler.php");
require_once(e_HANDLER."ren_help.php");

// ------------------------------------------------------------------------------- Forum Functions --+

$sql = new db;		// Parents
$sql2 = new db;		// Forums
$sql3 = new db;		// Sub-Forums
$sql4 = new db;		// Threads
$sql5 = new db;		// Posts


require_once(e_PLUGIN."forum/admin_functions.php");

// ---------------------------------------------------------------------------------- Page Actions --+		

if ($action == '') { $action = "main"; }	// !important

if ($action == 'main') {
	show_main();
}
if ($action == 'parents') { 
	create_parents($sub_action, $id);
}
if ($action == 'forums') { 
	create_forums($sub_action, $id);
}
if ($action == 'subforums') { 
	create_subforums($sub_action, $id, $sub_id);
}
if ($action == 'prefs') { 
	show_prefs();
}
if ($action == 'mods') { 
	show_mods();
}

if ($action == 'usermodules') { 
	show_usermodules($sub_action);
}

// -------------------------------------------------------- Don't change anything above this line! --+		

// --------------------------------------------------------------------------------------------------+		

require_once(e_ADMIN."footer.php");

?>