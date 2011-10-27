<?php
/*
+ ----------------------------------------------------------------------------------------------------+
|        e107 website system 
|        Email: crytiqal@team-aero.org
|        Organization: Team-Aero Copyright 2011 - www.team-aero.org
|        $Id: admin_functions.php 659 2011-10-09 12:07:49Z crytiqal $
|        License: GNU GENERAL PUBLIC LICENSE - http://www.gnu.org/licenses/gpl.txt
+----------------------------------------------------------------------------------------------------+
*/

// --------------------------------------------------------------------------------------------------+

	function show_main()
	{
		global $sql, $sql2, $ns;

		$text .= "
		<table style='".ADMIN_WIDTH."' class='fborder'>
		<tr>
		<td colspan='3' style='width:70%; text-align:center' class='fcaption'>".FORLAN_28."</td>
		<td style='width:30%; text-align:center' class='fcaption'>".FORLAN_80."</td>
		</tr>";

	// ---------------------------------------------------------------------------------------------+

		if($sql->db_Select("forum_p", "forum_parent_id") < 1)
		{
			show_message("No Parents Yet");	// 		show_message(FORLAN_13);
		}
		else
		{
			$sql->db_Select("forum_p","*", "ORDER BY forum_parent_order", false); 
			while($row = $sql->db_Fetch()) 
			{ 

				$id = $row['forum_parent_id'];	

				$text .= "	<tr><td colspan='4'><br /></td></tr>	
		
						<tr>
							<td><img src='e_PLUGINS/forum/images/parents/".$id.".png' alt='' /></td>
 							<td>(".$id.")&nbsp;<a href='".$row['forum_parent_name']."'>".$row['forum_parent_name']."</a><br />".$row['forum_parent_description']."</td>
 							<td><b>".FORLAN_140.":</b> ".r_userclass_name($row['forum_parent_class'])."&nbsp;&nbsp;<b>".FORLAN_141.":</b> ".r_userclass_name($row['forum_parent_postclass'])."</td>
						<td><a href='".e_SELF."?parents.edit.$id'><img title='edit' src='".img_path('edit.png')."' alt='' /></a><a href='".e_SELF."?parents.delete.$id'><img title='delete' src='' alt='' /></a></td>
				          	</tr>";
 
				$sql2->db_Select("forum_f", "*", "WHERE forum_parent_id='".$row['forum_parent_id']."'", false);
				while($row2 = $sql2->db_Fetch()) 
				{
					$text .= "<tr><td>".$row2['forum_forum_name']."</td></tr>";
					$text .= "<tr><td>".$row2['forum_forum_description']."</td></tr>";
				}
			} 
		}

	// ---------------------------------------------------------------------------------------------+

		$text .= "</table>";

		$ns->tablerender(FORLAN_75, $text);	

	}

// --------------------------------------------------------------------------------------------------+

	function create_parents($sub_action, $id)
	{
		global $sql, $ns;

	// ---------------------------------------------------------------------------------------------+

		if($sub_action == "create")
		{
			if($sql->db_Select("forum_p", "*", "forum_parent_id='".$id."'"))
			{
				header("location:".e_SELF."?parents.edit.$id");
				exit;
			}
		}
		if($sub_action == "edit" && !$_POST['update_parent'])
		{
			if($sql->db_Select("forum_p", "*", "forum_parent_id='".$id."'"))
			{
				$row = $sql->db_Fetch();
				extract($row);
			}
			else
			{
				header("location:".e_SELF."?parents.create.$id");
				exit;
			}
		}
		if($sub_action == "delete" && !$_POST['delete_parent'])
		{
			if($sql->db_Select("forum_p", "*", "forum_parent_id='".$id."'"))
			{
				$row = $sql->db_Fetch();
				extract($row);
			}
			else
			{
				header("location:".e_SELF."?parents.create.$id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
		<td style='width:60%' class='forumheader3'>
		<input class='tbox' type='text' name='forum_parent_name' size='60' value='$forum_parent_name' maxlength='250' />
		</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
		<td style='width:60%' class='forumheader3'>
		<input class='tbox' type='text' name='forum_parent_description' size='60' value='$forum_parent_description' maxlength='250' />
		</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
		<td style='width:60%' class='forumheader3'>".r_userclass("forum_parent_class", $forum_parent_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
		<td style='width:60%' class='forumheader3'>".r_userclass("forum_parent_postclass", $forum_parent_postclass, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr style='vertical-align:top'>
		<td colspan='2'  style='text-align:center' class='forumheader'>
		<input type='hidden' name='e-token' value='".e_TOKEN."' />";

		if ($sub_action == "create")
		{
			$text .= "<input class='button' type='submit' name='submit_parent' value='submit' />";
		}
		elseif ($sub_action == "delete")
		{
			$text .= "<input class='button' type='submit' name='delete_parent' value='delete' />";
		}
		else
		{
			$text .= "<input class='button' type='submit' name='update_parent' value='update' />";
		}

		$text .= "</td>
		</tr>
		</table>
		</form>
		</div>";

		$ns->tablerender(FORLAN_75, $text);
	}

// --------------------------------------------------------------------------------------------------+

	function create_forums($sub_action, $id)
	{
		global $sql, $ns;

	// ---------------------------------------------------------------------------------------------+

		if ($sub_action == "create")
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$id"))
			{
				header("location:".e_SELF."?forums.edit.$id");
				exit;
			}
		}
		if ($sub_action == "edit" && !$_POST['update_forum'])
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$id"))
			{
				$row = $sql->db_Fetch();
				extract($row);
			}
			else
			{
				header("location:".e_SELF."?forums.create.$id");
				exit;
			}
		}
		if ($sub_action == "delete" && !$_POST['delete_forum'])
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$id"))
			{
				$row = $sql->db_Fetch();
				extract($row);
			}
			else
			{
				header("location:".e_SELF."?forums.create.$id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
		<td style='width:60%' class='forumheader3'>
		<input class='tbox' type='text' name='forum_forum_name' size='60' value='$forum_forum_name' maxlength='250' />
		</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
		<td style='width:60%' class='forumheader3'>
		<input class='tbox' type='text' name='forum_forum_description' size='60' value='$forum_forum_description' maxlength='250' />
		</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
		<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_class", $forum_forum_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
		<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_postclass", $forum_parent_postclass, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr style='vertical-align:top'>
		<td colspan='2'  style='text-align:center' class='forumheader'>
		<input type='hidden' name='e-token' value='".e_TOKEN."' />";

		if ($sub_action == "create")
		{
			$text .= "<input class='button' type='submit' name='submit_forum' value='submit' />";
		}
		elseif ($sub_action == "delete")
		{
			$text .= "<input class='button' type='submit' name='delete_forum' value='delete' />";
		}
		else
		{
			$text .= "<input class='button' type='submit' name='update_forum' value='update' />";
		}

		$text .= "</td>
		</tr>
		</table>
		</form>
		</div>";

		$ns->tablerender(FORLAN_75, $text);
	}

// --------------------------------------------------------------------------------------------------+

	function show_prefs(){}

// --------------------------------------------------------------------------------------------------+

	function show_mods(){}

// --------------------------------------------------------------------------------------------------+

	function show_usermodules($sub_action)
	{
		global $sql, $ns;

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'><tr><td>";

	// ---------------------------------------------------------------------------------------------+
	
		if($sub_action == "install")
		{
			$text .= "<input class='button' type='submit' name='install_usermodules' value='install' />";
		}
		else if($sub_action == "uninstall")
		{
			$text .= "<input class='button' type='submit' name='uninstall_usermodules' value='deinstall' />";
		}
		else
		{
			$message = "No action specified!";
			show_message($message);
		}

	// ---------------------------------------------------------------------------------------------+

		$text .= "
		</tr></td></table>
		</form>
		</div>";

		$ns->tablerender(FORLAN_75, $text);

	}

// --------------------------------------------------------------------------------------------------+
	function show_message($message)
	{
		global $ns;
		$ns->tablerender("", "<div style='text-align:center'><b>".$message."</b></div>");
	}

// --------------------------------------------------------------------------------------------------+

if(isset($_POST['submit_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Insert("forum_p", "'".$id."', '".$_POST['forum_parent_name']."', '".$_POST['forum_parent_description']."', '".$_POST['forum_parent_class']."', '{$_POST['forum_parent_postclass']}', '0'");
	mysql_query("ALTER TABLE e107_forum_p ORDER BY forum_parent_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_13);
	$action = "create";
}

if(isset($_POST['update_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Update("forum_p", "forum_parent_name='".$_POST['forum_parent_name']."', forum_parent_description='".$_POST['forum_parent_description']."', forum_parent_class='".$_POST['forum_parent_class']."', forum_parent_postclass='{$_POST['forum_parent_postclass']}'  WHERE forum_parent_id=$id");
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "edit";
}

if(isset($_POST['delete_parent']))
{
	$sql->db_Delete("forum_p", "forum_parent_id='$id'");
	mysql_query("ALTER TABLE e107_forum_p AUTO_INCREMENT = 1, ORDER BY forum_parent_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "main";
}

if(isset($_POST['install_usermodules']))
{
	mysql_query("ALTER TABLE e107_user ADD( user_forum_oldtimestamp int(10) unsigned NOT NULL default '0', user_forum_newtimestamp int(10) unsigned NOT NULL default '0', user_forum_newposts int(10) unsigned NOT NULL default '0')") or die(mysql_error());
}

if(isset($_POST['uninstall_usermodules']))
{
	mysql_query("ALTER TABLE e107_user DROP user_forum_oldtimestamp, DROP user_forum_newtimestamp, DROP user_forum_newposts") or die(mysql_error());
}

// --------------------------------------------------------------------------------------------------+


?>