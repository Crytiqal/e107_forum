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
// -------------------------------------------------------------------------- function show_main() --+
// --------------------------------------------------------------------------------------------------+

	function show_main()
	{
		global $sql, $sql2, $sql3, $ns;

		if($sql->db_Select("forum_p", "forum_parent_id") < 1)
		{
			show_message("No Parents Yet");	// 		show_message(FORLAN_13);
		}
		else
		{

			$text .= "
			<table style='".ADMIN_WIDTH."' class='fborder'>
			<tr>
/*-->*/			<td colspan='3' style='width:70%; text-align:center' class='fcaption'>".FORLAN_28."</td>
/*-->*/			<td style='width:30%; text-align:center' class='fcaption'>".FORLAN_80."</td>
			</tr>
			<tr>
				<td colspan='4'><br /></td>
			</tr>";

	// ----------------------------------------------------------------------------- Show Parents --+

			$sql->db_Select("forum_p","*", "ORDER BY forum_parent_order", false); 
			while($row = $sql->db_Fetch()) 
			{ 
				extract($row);

				$text .= "	<tr>
 							<td colspan='2'>({$forum_parent_id})&nbsp;<a href='".$forum_parent_name."'>".$forum_parent_name."</a><br />".$forum_parent_description."</td>
 /*-->*//*-->*/					<td><b>".FORLAN_140.":</b> ".r_userclass_name($forum_parent_class)."<br />&nbsp;&nbsp;<b>".FORLAN_141.":</b> ".r_userclass_name($forum_parent_postclass)."</td>
							<td>
								<a href='".e_SELF."?parents.edit.$forum_parent_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
								<a href='".e_SELF."?parents.delete.$forum_parent_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
							</td>
				          	</tr>";

				$text .= "	<tr><td colspan='4'><br /></td></tr>"; 

		// ------------------------------------------------------------------------- Show Forums --+

				$sql2->db_Select("forum_f", "*", "WHERE forum_parent_id='".$forum_parent_id."' AND forum_forum_sub='0' ORDER BY forum_forum_order", false);
				while($row2 = $sql2->db_Fetch()) 
				{
					extract($row2);

					$text .= "	<tr>
								<td><img src='".e_PLUGIN."forum/images/forums/".$forum_forum_id.".png' alt='' /></td>
								<td>({$forum_forum_id})&nbsp;<a href='".$forum_forum_name."'>".$forum_forum_name."</a><br />".$forum_forum_description."";

			// ----------------------------------------------------------------- Show Subforums --+

					if($sql3->db_Select("forum_f", "*", "WHERE forum_forum_sub='".$row2['forum_forum_id']."' ORDER BY forum_forum_order", false) > 0)
					{

					while($row3 = $sql3->db_Fetch())
						{
							$subforums .= " ".$row3['forum_forum_name']." <a href='".e_SELF."?subforums.edit.$forum_forum_id.".$row3['forum_forum_id']."'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>&nbsp;<a href='".e_SELF."?subforums.delete.$forum_forum_id.".$row3['forum_forum_id']."'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>&nbsp;,";
						}

					//	$subforums = rtrim($subforums, ', ');
						$subforums .= "	<a href='".e_SELF."?subforums.create.$forum_forum_id'><img title='show' src='".img_path('admin_images/forums_16.png')."' alt='' /></a>";

						$text .= " 	<br /><b>Subforums:</b>".$subforums."</td>
									<td></td>
									<td>
										<a href='".e_SELF."?forums.edit.$forum_forum_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
										<a href='".e_SELF."?forums.delete.$forum_forum_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
									</td>
								</tr>";
					}
					else
					{
						$text .= "		</td>
									<td></td>
									<td>
										<a href='".e_SELF."?forums.edit.$forum_parent_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
										<a href='".e_SELF."?forums.delete.$forum_forum_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
									</td>
								</tr>";
					}

			// -----------------------------------------------------------------------------------+
					
				}

		// ----------------------------------------------------------------------------------------+

				$text .= "	<tr><td colspan='4'><br /><br /><br /></td></tr>";

			} 

	// ---------------------------------------------------------------------------------------------+

			$text .= "</table>";

			$ns->tablerender(FORLAN_75, $text);

			$sql3->db_Close();
			$sql2->db_Close();
			$sql->db_Close();
		}
	}

// --------------------------------------------------------------------------------------------------+
// --------------------------------------------------------------------- function create_parents() --+
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
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_parent_name' size='60' value='$forum_parent_name' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_parent_description' size='60' value='$forum_parent_description' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_parent_class", $forum_parent_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
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
// ---------------------------------------------------------------------- function create_forums() --+
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
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>";

		$sql->db_Select("forum_p", "*", "forum_parent_id");
		$text .= "<select name='forum_parent_id' class='tbox'>\n";
		while (list($forum_parent_id_, $forum_parent_name_) = $sql->db_Fetch())				// list() checks for column placement!
		{
			extract($row);
			if ($forum_parent_id_ == $forum_parent_id)
			{
				$text .= "<option value='$forum_parent_id_' selected='selected'>".$forum_parent_name_."</option>\n";
			}
			else
			{
				$text .= "<option value='$forum_parent_id_'>".$forum_parent_name_."</option>\n";
			}
		}
		$text .= "</select>
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_name' size='60' value='$forum_forum_name' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_description' size='60' value='$forum_forum_description' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_class", $forum_forum_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
		<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_postclass", $forum_forum_postclass, 'off', 'nobody,public,member,admin,classes')."</td>
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
// ------------------------------------------------------------------- function create_subforums() --+
// --------------------------------------------------------------------------------------------------+

	function create_subforums($sub_action, $id, $sub_id)
	{
		global $sql, $sql2, $ns;

	// ---------------------------------------------------------------------------------------------+

		if ($sub_action == "create")
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$id AND forum_forum_sub='0'"))			// Forum exists and is parent, continue.
			{

	// ---------------------------------------------------------------------------------------------+

				if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub='0'"))	// Subforum exists but is forum, go edit subforum.
				{
					header("location:".e_SELF."?subforums.edit.$id.$sub_id");
					exit;
				}
				if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!='0'"))	// Subforum exists, go edit subforum.
				{
					$row2 = $sql2->db_Fetch();
					$id = "".$row2['forum_forum_sub']."";
					header("location:".e_SELF."?subforums.edit.$id.$sub_id");
					exit;
				}

	// ---------------------------------------------------------------------------------------------+

			}
			elseif ($sql->db_Select("forum_f", "*", "forum_forum_id=$id AND forum_forum_sub!='0'"))		// Forum exists but is subforum, go edit subforum.
			{
				$row = $sql->db_Fetch();
				$sub_id = $id;
				$id = "".$row['forum_forum_sub']."";
				header("location:".e_SELF."?subforums.edit.$id.$sub_id");
				exit;
			}
			else																		// Forum doesn't exist, create forum.
			{
				header("location:".e_SELF."?forums.create.$id");
				exit;
			}
		}
		if ($sub_action == "edit" && !$_POST['update_subforum'])
		{
			if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub=$id"))
			{
				$row2 = $sql2->db_Fetch();
				extract($row2);
			}
			elseif ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!=$id"))
			{
					$row2 = $sql2->db_Fetch();
					$id = "".$row2['forum_forum_sub']."";
					header("location:".e_SELF."?subforums.edit.$id.$sub_id");
					exit;
			}
			else 
			{
				header("location:".e_SELF."?subforums.create.$id.$sub_id");
				exit;
			}
		}
		if ($sub_action == "delete" && !$_POST['delete_subforum'])
		{
			if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub=$id"))
			{
				$row2 = $sql2->db_Fetch();
				extract($row2);
			}
			elseif ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!=$id"))
			{
					$row2 = $sql2->db_Fetch();
					$id = "".$row2['forum_forum_sub']."";
					header("location:".e_SELF."?subforums.delete.$id.$sub_id");
					exit;
			}
			else
			{
				header("location:".e_SELF."?subforums.create.$id.$sub_id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>				
			<td style='width:60%' class='forumheader3'>";

		$sql->db_Select("forum_f", "*", "forum_forum_id AND forum_forum_sub='0'");
		$text .= "<select name='forum_forum_id' class='tbox'>\n";
		while (list(, $forum_forum_id_, , $forum_forum_name_) = $sql->db_Fetch())				// list() checks for column placement!
		{
			extract($row);
			if ($forum_forum_id_ == $forum_forum_id)
			{
				$text .= "<option value='$forum_forum_id_' selected='selected'>".$forum_forum_name_."</option>\n";
			}
			else
			{
				$text .= "<option value='$forum_forum_id_'>".$forum_forum_name_."</option>\n";
			}
		}
		$text .= "</select>
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_name' size='60' value='$forum_forum_name' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_description' size='60' value='$forum_forum_description' maxlength='250' />
			</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_class", $forum_forum_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
/*-->*/		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_postclass", $forum_forum_postclass, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr style='vertical-align:top'>
			<td colspan='2'  style='text-align:center' class='forumheader'>
			<input type='hidden' name='e-token' value='".e_TOKEN."' />";

		if ($sub_action == "create")
		{
			$text .= "<input class='button' type='submit' name='submit_subforum' value='submit' />";
		}
		elseif ($sub_action == "delete")
		{
			$text .= "<input class='button' type='submit' name='delete_subforum' value='delete' />";
		}
		else
		{
			$text .= "<input class='button' type='submit' name='update_subforum' value='update' />";
		}

		$text .= "</td>
		</tr>
		</table>
		</form>
		</div>";

		$ns->tablerender(FORLAN_75, $text);
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------- function show_prefs() --+
// --------------------------------------------------------------------------------------------------+

	function show_prefs(){}

// --------------------------------------------------------------------------------------------------+
// -------------------------------------------------------------------------- function show_mods() --+
// --------------------------------------------------------------------------------------------------+

	function show_mods(){}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------- function show_usermodules() --+
// --------------------------------------------------------------------------------------------------+

	function show_usermodules($sub_action)
	{
		global $sql, $ns;

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>
		<tr>
			<td>
			<input type='hidden' name='e-token' value='".e_TOKEN."' />";

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

		$text .= "</td>
		</tr>
		</table>
		</form>
		</div>";

		$ns->tablerender(FORLAN_75, $text);

	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------------- Form Processing --+
// --------------------------------------------------------------------------------------------------+

if(isset($_POST['submit_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Insert("forum_p", "'".$id."', '".$_POST['forum_parent_name']."', '".$_POST['forum_parent_description']."', '".$_POST['forum_parent_class']."', '".$_POST['forum_parent_postclass']."', '0'");
	mysql_query("ALTER TABLE e107_forum_p ORDER BY forum_parent_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_13);
	$action = "create";
}
if(isset($_POST['update_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Update("forum_p", "forum_parent_name='".$_POST['forum_parent_name']."', forum_parent_description='".$_POST['forum_parent_description']."', forum_parent_class='".$_POST['forum_parent_class']."', forum_parent_postclass='".$_POST['forum_parent_postclass']."'  WHERE forum_parent_id=$id");
	mysql_query("ALTER TABLE e107_forum_p ORDER BY forum_parent_id ASC") or die (mysql_error());
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
	// ---------------------------------------------------------------------------------------------+

if(isset($_POST['submit_forum']))
{
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);
	$sql->db_Insert("forum_f", "'".$_POST['forum_parent_id']."', '0', '".$id."', '".$_POST['forum_forum_name']."', '".$_POST['forum_forum_description']."', '".$_POST['forum_forum_class']."', '".$_POST['forum_forum_postclass']."', '0', '0'");
	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message("Forum created");	// FORLAN_13
	$action = "create";
}
if(isset($_POST['update_forum']))
{
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);
	$sql->db_Update("forum_f", "forum_parent_id='".$_POST['forum_parent_id']."', forum_forum_name='".$_POST['forum_forum_name']."', forum_forum_description='".$_POST['forum_forum_description']."', forum_forum_class='".$_POST['forum_forum_class']."', forum_forum_postclass='".$_POST['forum_parent_postclass']."'  WHERE forum_forum_id=$id");
	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "edit";
}
if(isset($_POST['delete_forum']))
{
	$sql->db_Delete("forum_f", "forum_forum_id='$id'");
	mysql_query("ALTER TABLE e107_forum_f AUTO_INCREMENT = 1, ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "main";
}
	// ---------------------------------------------------------------------------------------------+

if(isset($_POST['install_usermodules']))
{
	mysql_query("ALTER TABLE e107_user ADD( user_forum_oldtimestamp int(10) unsigned NOT NULL default '0', user_forum_newtimestamp int(10) unsigned NOT NULL default '0', user_forum_newposts int(10) unsigned NOT NULL default '0')") or die(mysql_error());
	show_message(FORLAN_13);
}
if(isset($_POST['uninstall_usermodules']))
{
	mysql_query("ALTER TABLE e107_user DROP user_forum_oldtimestamp, DROP user_forum_newtimestamp, DROP user_forum_newposts") or die(mysql_error());
	show_message(FORLAN_13);
}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------------ Global functions --+
// --------------------------------------------------------------------------------------------------+

	function show_message($message)
	{
		global $ns;
		$ns->tablerender("", "<div style='text-align:center'><b>".$message."</b></div>");
	}

	function img_path($img)
	{
		$img = "".e_PLUGIN."forum/images/".$img."";
		return $img;
	}

// --------------------------------------------------------------------------------------------------+


?>