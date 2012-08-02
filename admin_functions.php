<?php
/*
+ ----------------------------------------------------------------------------------------------------+
|        e107 website system 
|        Email: crytiqal@team-aero.org
|        Organization: Team-Aero Copyright 2012 - www.team-aero.org
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
			$text .= "<div style='text-align:center'>
			<table style='".ADMIN_WIDTH."' class='fborder'>
			<tr>
<!-- -->			<td colspan='3' style='width:70%; text-align:center' class='fcaption'>".FORLAN_28."</td>
<!-- -->			<td style='width:30%; text-align:center' class='fcaption'>".FORLAN_80."</td>
			</tr>
			<tr>
				<td colspan='4'><br /></td>
			</tr>";

	// ----------------------------------------------------------------------------- Show Parents --+

			$sql->db_Select("forum_p", "*", "ORDER BY forum_parent_order", false); 
			while($row = $sql->db_Fetch()) 
			{ 
				extract($row);

				$text .= "	<tr>
 							<td colspan='2'>({$forum_parent_id})&nbsp;<a href='p".$forum_parent_id."-".preg_replace('![^a-z0-9]+!i','-',$forum_parent_name)."'>".$forum_parent_name."</a><br />".$forum_parent_description."</td>
 <!-- --><!-- -->					<td><b>".FORLAN_140.":</b> ".r_userclass_name($forum_parent_class)."<br /><b>".FORLAN_141.":</b> ".r_userclass_name($forum_parent_postclass)."</td>
							<td>
								<a href='".e_SELF."?parents.edit.$forum_parent_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
								<a href='".e_SELF."?parents.delete.$forum_parent_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
							</td>
				          	</tr>";

				$text .= "	<tr><td colspan='4'><br /></td></tr>"; 

			// -------------------------------------------------------------------- Show Forums --+

				$sql2->db_Select("forum_f", "*", "WHERE forum_parent_id='".$forum_parent_id."' AND forum_forum_sub='0' ORDER BY forum_forum_order", false);
				while($row2 = $sql2->db_Fetch()) 
				{
					extract($row2);

					$text .= "	<tr>
								<td><img src='".e_PLUGIN."forum/images/forums/".$forum_forum_id.".png' alt='' /></td>
								<td>({$forum_forum_id})&nbsp;<a href='p".$forum_parent_id."f".$forum_forum_id."-".preg_replace('![^a-z0-9]+!i','-',$forum_forum_name)."'>".$forum_forum_name."</a><br />".$forum_forum_description."";

				// ------------------------------------------------------------ Show Subforums --+

					if($sql3->db_Select("forum_f", "*", "WHERE forum_forum_sub='".$row2['forum_forum_id']."' ORDER BY forum_forum_order", false))
					{
						while($row3 = $sql3->db_Fetch())
						{
							$subforums .= " <a href='p".$forum_parent_id."f".$forum_forum_id."s".$row3['forum_forum_id']."-".preg_replace('![^a-z0-9]+!i','-',$row3['forum_forum_name'])."'>".$row3['forum_forum_name']."</a> 
										<a href='".e_SELF."?subforums.edit.$forum_forum_id.".$row3['forum_forum_id']."'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
										<a href='".e_SELF."?subforums.delete.$forum_forum_id.".$row3['forum_forum_id']."'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>,";
						}
					//	$subforums = rtrim($subforums, ', ');
						$subforums .= "	<a href='".e_SELF."?subforums.create.$forum_forum_id'><img title='create' src='".img_path('admin_images/forums_16.png')."' alt='' /></a>";

						$text .= " 	<br /><b>Sub-Forum(s):</b>".$subforums."</td>
									<td></td>
									<td>
										<a href='".e_SELF."?forums.edit.$forum_forum_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
										<a href='".e_SELF."?forums.delete.$forum_forum_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
									</td>
								</tr>";
						unset($subforums);
					}
					else
					{
						$text .= "		</td>
									<td></td>
									<td>
										<a href='".e_SELF."?forums.edit.$forum_forum_id'><img title='edit' src='".img_path('admin_images/edit_16.png')."' alt='' /></a>
										<a href='".e_SELF."?forums.delete.$forum_forum_id'><img title='delete' src='".img_path('admin_images/delete_16.png')."' alt='' /></a>
										<a href='".e_SELF."?subforums.create.$forum_forum_id'><img title='create' src='".img_path('admin_images/sub_forums_16.png')."' alt='' /></a>
									</td>
								</tr>";
					}

				// ------------------------------------------------------------------------------+
					
				}

				$text .= "	<tr><td colspan='4'><br /><br /><br /></td></tr>";
			} 

		// ----------------------------------------------------------------------------------------+

			$text .= "</table></div>";

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
				$sql->db_Close();
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
				$sql->db_Close();
			}
			else
			{
				$sql->db_Close();
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
				$sql->db_Close();
			}
			else
			{
				$sql->db_Close();
				header("location:".e_SELF."?parents.create.$id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Parent name:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_parent_name' size='60' value='$forum_parent_name' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Parent description:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_parent_description' size='60' value='$forum_parent_description' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_parent_class", $forum_parent_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
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
				$sql->db_Close();
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
				$sql->db_Close();
			}
			else
			{
				$sql->db_Close();
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
				$sql->db_Close();
			}
			else
			{
				$sql->db_Close();
				header("location:".e_SELF."?forums.create.$id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Parent:</td>
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
		$sql->db_Close();
		$text .= "</select>
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Forum name:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_name' size='60' value='$forum_forum_name' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Forum description:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_description' size='60' value='$forum_forum_description' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_class", $forum_forum_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
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

	// ---------------------------------------------------------------- Convert forum -> subforum --+

		if (!$sql->db_Select("forum_f", "*", "forum_forum_sub='$id'"))
		{
			$text .= "<br /><br /><div style='text-align:center'>
			<form method='post' action='".e_SELF."?".e_QUERY."'>\n
			<table style='".ADMIN_WIDTH."' class='fborder'>
			<tr>
				<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
				<td style='width:60%' class='forumheader3'>";

			$sql->db_Select("forum_f", "*", "forum_forum_id AND forum_forum_id!='".$id."' AND forum_forum_sub='0'");
			$text .= "<select name='forum_forum_sub' class='tbox'>\n";
			$text .= "<option><b>Convert to Subforum</b></option>\n";
			while (list(, $forum_forum_id_, , $forum_forum_name_) = $sql->db_Fetch())				// list() checks for column placement!
			{
				extract($row);
				if ($forum_forum_id_ == $sub_id)
				{
					$text .= "<option value='$forum_forum_id_' selected='selected'>".$forum_forum_name_."</option>\n";
				}
				else
				{
					$text .= "<option value='$forum_forum_id_'>".$forum_forum_name_."</option>\n";
				}
			}
			$sql->db_Close();

			$text .= "</select>
				</td>
			</tr>
	
			<tr style='vertical-align:top'>
				<td colspan='2'  style='text-align:center' class='forumheader'>
				<input type='hidden' name='e-token' value='".e_TOKEN."' />
				<center><input class='button' type='submit' name='convert_forum' value='convert' /> <input type='checkbox' name='confirm' value='1' /><span class='smalltext'> tick to confirm</span></center>
				</td>
			</tr>
			</table>
			</form>
			</div>";
		}

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
				if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub='0'"))	// Subforum exists but is forum, go edit subforum.
				{
					$sql2->db_Close();
					header("location:".e_SELF."?subforums.edit.$id.$sub_id");
					exit;
				}
				if ($sql2->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!='0'"))	// Subforum exists, go edit subforum.
				{
					$row2 = $sql2->db_Fetch();
					$id = "".$row2['forum_forum_sub']."";
					$sql2->db_Close();
					header("location:".e_SELF."?subforums.edit.$id.$sub_id");
					exit;
				}
			}
			elseif ($sql->db_Select("forum_f", "*", "forum_forum_id=$id AND forum_forum_sub!='0'"))		// Forum exists but is subforum, go edit subforum.
			{
				$row = $sql->db_Fetch();
				$sub_id = $id;
				$id = "".$row['forum_forum_sub']."";
				$sql->db_Close();
				header("location:".e_SELF."?subforums.edit.$id.$sub_id");
				exit;
			}
			else																		// Forum doesn't exist, create forum.
			{
				$sql->db_Close();
				header("location:".e_SELF."?forums.create.$id");
				exit;
			}
		}
		if ($sub_action == "edit" && !$_POST['update_subforum'])
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub=$id"))
			{
				$row = $sql->db_Fetch();
				extract($row);
				$sql->db_Close();
			}
			elseif ($sql->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!=$id"))
			{
				$row = $sql->db_Fetch();
				$id = "".$row['forum_forum_sub']."";
				$sql->db_Close();
				header("location:".e_SELF."?subforums.edit.$id.$sub_id");
				exit;
			}
			else 
			{
				$sql->db_Close();
				header("location:".e_SELF."?subforums.create.$id.$sub_id");
				exit;
			}
		}
		if ($sub_action == "delete" && !$_POST['delete_subforum'])
		{
			if ($sql->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub=$id"))
			{
				$row = $sql->db_Fetch();
				extract($row);
				$sql->db_Close();
			}
			elseif ($sql->db_Select("forum_f", "*", "forum_forum_id=$sub_id AND forum_forum_sub!=$id"))
			{
				$row = $sq2->db_Fetch();
				$id = "".$row['forum_forum_sub']."";
				$sql->db_Close();
				header("location:".e_SELF."?subforums.delete.$id.$sub_id");
				exit;
			}
			else
			{
				$sql->db_Close();
				header("location:".e_SELF."?subforums.create.$id.$sub_id");
				exit;
			}
		}

	// ---------------------------------------------------------------------------------------------+

		$text = "<div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Parent:</td>				
			<td style='width:60%' class='forumheader3'>";

		$sql->db_Select("forum_f", "*", "forum_forum_id AND forum_forum_sub='0'");
		$text .= "<select name='forum_forum_sub' class='tbox'>\n";
		while (list(, $forum_forum_id_, , $forum_forum_name_) = $sql->db_Fetch())				// list() checks for column placement!
		{
			extract($row);
			if ($forum_forum_id_ == $id)
			{
				$text .= "<option value='$forum_forum_id_' selected='selected'>".$forum_forum_name_."</option>\n";
			}
			else
			{
				$text .= "<option value='$forum_forum_id_'>".$forum_forum_name_."</option>\n";
			}
		}
		$sql->db_Close();

		$text .= "</select>
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Subforum name:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_name' size='60' value='$forum_forum_name' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_31."Subforum description:</td>
			<td style='width:60%' class='forumheader3'>
			<input class='tbox' type='text' name='forum_forum_description' size='60' value='$forum_forum_description' maxlength='250' />
			</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_23.":<br /><span class='smalltext'>(".FORLAN_24.")</span></td>
			<td style='width:60%' class='forumheader3'>".r_userclass("forum_forum_class", $forum_forum_class, 'off', 'nobody,public,member,admin,classes')."</td>
		</tr>

		<tr>
<!-- -->		<td style='width:40%' class='forumheader3'>".FORLAN_142.":<br /><span class='smalltext'>(".FORLAN_143.")</span></td>
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
		
	// ---------------------------------------------------------------- Convert subforum -> forum --+

		$text .= "<br /><br /><div style='text-align:center'>
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
		<table style='".ADMIN_WIDTH."' class='fborder'>
		<tr>
			<td style='width:40%' class='forumheader3'>".FORLAN_31.":</td>
			<td style='width:60%' class='forumheader3'>";

		$sql->db_Select("forum_p", "*", "forum_parent_id");
		$text .= "<select name='forum_parent_id' class='tbox'>\n";
		$text .= "<option><b>Convert to Forum</b></option>\n";
		while (list($forum_parent_id_, $forum_parent_name_) = $sql->db_Fetch())							// list() checks for column placement!
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
		$sql->db_Close();

		$text .= "</select>
			</td>
		</tr>

		<tr style='vertical-align:top'>
			<td colspan='2'  style='text-align:center' class='forumheader'>
			<input type='hidden' name='e-token' value='".e_TOKEN."' />
			<center><input class='button' type='submit' name='convert_subforum' value='convert' /> <input type='checkbox' name='confirm' value='1' /><span class='smalltext'> tick to confirm</span></center>
			</td>
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
		global $ns;

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
// ----------------------------------------------------------------------- function info_parents() --+
// --------------------------------------------------------------------------------------------------+

	function info_parents($parent_id)
	{
		global $sql;

		$forums = $sql->db_Count("forum_f", "(*)", "WHERE forum_parent_id='$parent_id' AND forum_forum_sub='0'");
		$subforums = $sql->db_Count("forum_f", "(*)", "WHERE forum_parent_id='$parent_id' AND forum_forum_sub!='0'");
		$info .= "<b>".$forums." Forums<br /> ".$subforums." Sub-Forums</b><br /><br /><ul>";

		$sql->db_Select("forum_f", "*", "forum_parent_id='$parent_id' AND forum_forum_sub='0'");
		while($row = $sql->db_Fetch())
		{
			$info .= info_forums($row);
		}
		$sql->db_Close();

		$info .="</ul>";
		return $info;
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------ function info_forums() --+
// --------------------------------------------------------------------------------------------------+

	function info_forums($row)
	{
		global $sql2;

		$forum_id = $row['forum_forum_id'];
		$forum_name = $row['forum_forum_name'];

/* Thread- and Postcount function */

		$forum_info = "<li>Forum ".$forum_id." [".$forum_name."] has ";
		$forum_info .= info_threads_posts($forum_id);
		$forum_info .= "<br />";

/* Sub-Forums */

		$sql2->db_Select("forum_f", "*", "forum_forum_sub='$forum_id'");
		while($row2 = $sql2->db_Fetch())
		{
			$forum_info .= info_subforums($row2);
		}
		$sql2->db_Close();

		$forum_info .= "</li>";
		return $forum_info;
	}

// --------------------------------------------------------------------------------------------------+
// --------------------------------------------------------------------- function info_subforums() --+
// --------------------------------------------------------------------------------------------------+

	function info_subforums($row2)
	{

		$subforum_id = $row2['forum_forum_id'];
		$subforum_name = $row2['forum_forum_name'];

/* Thread- and Postcount function */
		
		$subforum_info = "<i>Sub-Forum ".$subforum_id." [".$subforum_name."] has ";
		$subforum_info .= info_threads_posts($subforum_id);
		$subforum_info .= "</i><br /><br />";

		return $subforum_info;
	}


// --------------------------------------------------------------------------------------------------+
// ----------------------------------------------------------------- function info_threads_posts() --+
// --------------------------------------------------------------------------------------------------+
	
	function info_threads_posts($id)
	{
		global $sql3;

		unset($threads);
		unset($posts);
		$threads = 0;
		$posts = 0;

		$threads = ".$threads." + $sql3->db_Count("forum_t", "(*)", "WHERE forum_forum_id='$id'");

		$sql3->db_Select("forum_t", "*", "WHERE forum_forum_id='$id'");
		while($row3 = $sql3->db_Fetch())
		{
			$thread_id = $row3['forum_thread_id'];
			$posts = ".$posts." + $sql3->db_Count("forum_posts", "(*)", "WHERE forum_thread_id='$thread_id'");
		}
		$sql3->db_Close();

		$thread_post_info = "".$threads." threads, ".$posts." posts";
		return $thread_post_info;
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------ function del_parents() --+
// --------------------------------------------------------------------------------------------------+

	function del_parents($parent_id)
	{
		global $sql;

		$sql->db_Select("forum_f", "*", "forum_parent_id='$parent_id' AND forum_forum_sub='0'");
		while($row = $sql->db_Fetch())
		{
			del_forums($row['forum_forum_id']);
		}
		$sql->db_Delete("forum_p", "forum_parent_id='$parent_id'");
		$sql->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------- function del_forums() --+
// --------------------------------------------------------------------------------------------------+

	function del_forums($forum_id)
	{
		global $sql2, $sql3;

		$sql3->db_Select("forum_t", "*", "forum_forum_id='$forum_id'");
		while($row3 = $sql3->db_Fetch())
		{
		 	del_threads_posts($row3['forum_thread_id']);
		}
		$sql3->db_Close();

		$sql2->db_Select("forum_f", "*", "forum_forum_sub='$forum_id'");
		while($row2 = $sql2->db_Fetch())
		{
			del_subforums($row2['forum_forum_id']);
		}
		$sql2->db_Delete("forum_f", "forum_forum_id='$forum_id'");
		$sql2->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// ---------------------------------------------------------------------- function del_subforums() --+
// --------------------------------------------------------------------------------------------------+

	function del_subforums($subforum_id)
	{
		global $sql3;

		$sql3->db_Select("forum_t", "*", "forum_forum_id='$subforum_id'");
		while($row3 = $sql3->db_Fetch())
		{
			del_threads_posts($row3['forum_thread_id']);
		}
		$sql3->db_Delete("forum_f", "forum_forum_id='$subforum_id'");
		$sql3->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------ function del_threads_posts() --+
// --------------------------------------------------------------------------------------------------+

	function del_threads_posts($thread_id)
	{
		global $sql4, $sql5;

		$sql5->db_Delete("forum_posts", "forum_thread_id='$thread_id'");
		$sql5->db_Close();
		$sql4->db_Delete("forum_t", "forum_thread_id='$thread_id'");
		$sql4->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------ function del_threads() --+
// --------------------------------------------------------------------------------------------------+

	function del_threads($thread_id)
	{
		global $sql4, $sql5;

		$sql5->db_Delete("forum_posts", "forum_thread_id='$thread_id'");
		$sql5->db_Close();
		$sql4->db_Delete("forum_t", "forum_thread_id='$thread_id'");
		$sql4->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// -------------------------------------------------------------------------- function del_posts() --+
// --------------------------------------------------------------------------------------------------+

	function del_posts($post_id)
	{
		global $sql5;

		$sql5->db_Delete("forum_posts", "forum_post_id='$post_id'");
		$sql5->db_Close();
	}

// --------------------------------------------------------------------------------------------------+
// ------------------------------------------------------------------------------- Form Processing --+
// --------------------------------------------------------------------------------------------------+

if(isset($_POST['submit_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Insert("forum_p", "'{$id}', '{$_POST['forum_parent_name']}', '{$_POST['forum_parent_description']}', '{$_POST['forum_parent_class']}', '{$_POST['forum_parent_postclass']}', '0'");
	mysql_query("ALTER TABLE e107_forum_p ORDER BY forum_parent_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_13);
	$action = "create";
}
if(isset($_POST['update_parent']))
{
	$_POST['forum_parent_name'] = $tp->toDB($_POST['forum_parent_name']);
	$_POST['forum_parent_description'] = $tp->toDB($_POST['forum_parent_description']);
	$sql->db_Update("forum_p", "forum_parent_name='{$_POST['forum_parent_name']}', forum_parent_description='{$_POST['forum_parent_description']}', forum_parent_class='{$_POST['forum_parent_class']}', forum_parent_postclass='{$_POST['forum_parent_postclass']}'  WHERE forum_parent_id={$id}");
	mysql_query("ALTER TABLE e107_forum_p ORDER BY forum_parent_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "edit";
}
if(isset($_POST['delete_parent']))
{
	if ($_POST['confirm'])
	{
		del_parents($id);
		show_message(FORLAN_14);
		$action = "main";
	}
	else
	{
		$info = "<b>The parent has the following info:</b><br />";
		$info .= info_parents($id);

		$warning = "
		<table>
		<tr>
			<td style='width:50%'>$info</td>
			<td style='width:50%; text-align:center'><b>You cannot retrieve these once deleted!</b></td>
		</tr>
		</table>";

		$confirm = "
		<div style='text-align:center'>
		<b>Confirm delete operation</b>
		<br />
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
			<input type='hidden' name='e-token' value='".e_TOKEN."' />
			<center><input class='button' type='submit' name='delete_parent' value='Confirm Delete' /> <input type='checkbox' name='confirm' value='1' /><span class='smalltext'> tick to confirm</span></center>
		</form>
		</div>";

		$ns->tablerender("", $warning);
		$ns->tablerender("Confirm Delete", $confirm);

		require_once(e_ADMIN."footer.php");
	}
}

	// ---------------------------------------------------------------------------------------------+
	// ---------------------------------------------------------------------------------------------+
	// ---------------------------------------------------------------------------------------------+

if(isset($_POST['submit_forum']))
{
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);
	$sql->db_Insert("forum_f", "'{$_POST['forum_parent_id']}', '{$id}', '0', '{$_POST['forum_forum_name']}', '{$_POST['forum_forum_description']}', '{$_POST['forum_forum_class']}', '{$_POST['forum_forum_postclass']}', '0', '0'");
	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message("Forum created");	// FORLAN_13
	$action = "create";
}
if(isset($_POST['update_forum']))
{
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);
	$sql->db_Update("forum_f", "forum_parent_id='{$_POST['forum_parent_id']}', forum_forum_name='{$_POST['forum_forum_name']}', forum_forum_description='{$_POST['forum_forum_description']}', forum_forum_class='{$_POST['forum_forum_class']}', forum_forum_postclass='{$_POST['forum_parent_postclass']}'  WHERE forum_forum_id={$id}");

/* Sub-Forums */

	$sql2->db_Select("forum_f", "*", "forum_forum_sub='{$id}'");
	while($row2 = $sql->db_Fetch())
	{
		$sql2->db_Update("forum_f", "forum_parent_id='{$_POST['forum_parent_id']}'  WHERE forum_forum_sub='{$id}'");
	}
	$sql2->db_Close();

	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "edit";
}
if(isset($_POST['delete_forum']))
{
	if ($_POST['confirm'])
	{
		$row['forum_forum_id'] = $id;
		$row['forum_forum_name'] = $_POST['forum_forum_name'];
		del_forums($id);
		show_message(FORLAN_14);
		$action = "main";
	}
	else
	{
		$row['forum_forum_id'] = $id;
		$row['forum_forum_name'] = $_POST['forum_forum_name'];
		$info = "<b>The forum has the following info:</b><br />";
		$info .= info_forums($row);

		$warning = "
		<table>
		<tr>
			<td style='width:50%'>$info</td>
			<td style='width:50%; text-align:center'><b>You cannot retrieve these once deleted!</b></td>
		</tr>
		</table>";

		$confirm = "
		<div style='text-align:center'>
		<b>Confirm delete operation</b>
		<br />
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
			<input type='hidden' name='e-token' value='".e_TOKEN."' />
			<center><input class='button' type='submit' name='delete_forum' value='Confirm Delete' /> <input type='checkbox' name='confirm' value='1' /><span class='smalltext'> tick to confirm</span></center>
		</form>
		</div>";

		$ns->tablerender("", $warning);
		$ns->tablerender("Confirm Delete", $confirm);

		require_once(e_ADMIN."footer.php");
	}
}
if(isset($_POST['convert_forum']))
{
	if ($_POST['confirm'])
	{
		$sql->db_Update("forum_f", "forum_parent_id='0', forum_forum_sub='{$_POST['forum_forum_sub']}'  WHERE forum_forum_id={$id}");
		$sql->db_Close();
		show_message(FORLAN_14);
		$action = "main";
	}
	else 
	{ 
		show_message("Please tick the confirm box to convert this forum into a subforum"); 
	}
}

	// ---------------------------------------------------------------------------------------------+
	// ---------------------------------------------------------------------------------------------+
	// ---------------------------------------------------------------------------------------------+

if(isset($_POST['submit_subforum']))
{
	$sql->db_Select("forum_f", "*", "forum_forum_id='".$_POST['forum_forum_sub']."'");
	$row = $sql->db_Fetch();
	$_POST['forum_parent_id'] = $row['forum_parent_id'];
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);

	$sql->db_Insert("forum_f", "'{$_POST['forum_parent_id']}', '{$sub_id}', '{$_POST['forum_forum_sub']}', '{$_POST['forum_forum_name']}', '{$_POST['forum_forum_description']}', '{$_POST['forum_forum_class']}', '{$_POST['forum_forum_postclass']}', '0', '0'");
	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message("Subforum created");	// FORLAN_13
	$action = "create";
}
if(isset($_POST['update_subforum']))
{
	$sql->db_Select("forum_f", "*", "forum_forum_id='".$_POST['forum_forum_sub']."'");
	$row = $sql->db_Fetch();
	$_POST['forum_parent_id'] = $row['forum_parent_id'];
	$_POST['forum_forum_name'] = $tp->toDB($_POST['forum_forum_name']);
	$_POST['forum_forum_description'] = $tp->toDB($_POST['forum_forum_description']);

	$sql->db_Update("forum_f", "forum_parent_id='{$_POST['forum_parent_id']}', forum_forum_sub='{$_POST['forum_forum_sub']}', forum_forum_name='{$_POST['forum_forum_name']}', forum_forum_description='{$_POST['forum_forum_description']}', forum_forum_class='{$_POST['forum_forum_class']}', forum_forum_postclass='{$_POST['forum_parent_postclass']}'  WHERE forum_forum_id={$sub_id}");
	mysql_query("ALTER TABLE e107_forum_f ORDER BY forum_forum_id ASC") or die (mysql_error());
	$sql->db_Close();
	show_message(FORLAN_14);
	$action = "edit";
}
if(isset($_POST['delete_subforum']))
{
	if ($_POST['confirm'])
	{
		$row['forum_forum_id'] = $sub_id;
		$row['forum_forum_name'] = $_POST['forum_forum_name'];
		del_subforums($sub_id);
		show_message(FORLAN_14);
		$action = "main";
	}
	else
	{
		$row['forum_forum_id'] = $sub_id;
		$row['forum_forum_name'] = $_POST['forum_forum_name'];
		$info = "<b>The subforum has the following info:</b><br />";
		$info .= info_subforums($row);

		$warning = "
		<table>
		<tr>
			<td style='width:50%'>$info</td>
			<td style='width:50%; text-align:center'><b>You cannot retrieve these once deleted!</b></td>
		</tr>
		</table>";

		$confirm = "
		<div style='text-align:center'>
		<b>Confirm delete operation</b>
		<br />
		<form method='post' action='".e_SELF."?".e_QUERY."'>\n
			<input type='hidden' name='e-token' value='".e_TOKEN."' />
			<center><input class='button' type='submit' name='delete_subforum' value='Confirm Delete' /> <input type='checkbox' name='confirm' value='1' /><span class='smalltext'> tick to confirm</span></center>
		</form>
		</div>";

		$ns->tablerender("", $warning);
		$ns->tablerender("Confirm Delete", $confirm);

		require_once(e_ADMIN."footer.php");
	}
}
if(isset($_POST['convert_subforum']))
{
	if ($_POST['confirm'])
	{
		$sql->db_Update("forum_f", "forum_parent_id='{$_POST['forum_parent_id']}', forum_forum_sub='0'  WHERE forum_forum_id={$sub_id}");
		$sql->db_Close();
		show_message(FORLAN_14);
		$action = "main";
	}
	else 
	{ 
		show_message("Please tick the confirm box to convert this subforum into a forum"); 
	}
}

// --------------------------------------------------------------------------------------------------+
// --------------------------------------------------------------------------- Module Installation --+
// --------------------------------------------------------------------------------------------------+

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


?>