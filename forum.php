<?php
/*
+ ----------------------------------------------------------------------------------------------------+
|        e107 website system 
|        Email: crytiqal@team-aero.org
|        Organization: Team-Aero Copyright 2011 - www.team-aero.org
|        $Id: forum.php 659 2011-10-09 12:07:49Z crytiqal $
|        License: GNU GENERAL PUBLIC LICENSE - http://www.gnu.org/licenses/gpl.txt
+----------------------------------------------------------------------------------------------------+
*/

// -------------------------------------------------------- Don't change anything below this line! --+
/*
if (!isset($pref['plug_installed']['forum']))
{
	header('Location: '.e_BASE.'index.php');
	exit;
}
*/

if(!defined("e107_INIT")) { require_once("../../class2.php"); }
if(!defined("USER_WIDTH")){ define("USER_WIDTH","width:95%"); }
include_lan(e_PLUGIN.'forum/languages/'.e_LANGUAGE.'/lan_forum.php');

$sql = new db;		// Parents
$sql2 = new db;		// Forums
$sql3 = new db;		// Sub-Forums
$sql4 = new db;		// Threads
$sql5 = new db;		// Posts

// --------------------------------------------------------------------------------------------------+

$forum_forum_name = "Splash Damage Forums";
$forum_forum_welcome = "Welcome to the Splash Damage Forums.";

// --------------------------------------------------------------------------------------------------+

require_once(HEADERF);
// require_once(forum_nav.php);
$text .= "<a href='".e_PLUGIN."forum/forum.php'><img src='".e_PLUGIN."forum/images/navbit-home.png' alt='' /></a>";


// --------------------------------------------------------------------------------------------------+


		global $sql, $sql2, $sql3, $ns;

		if($sql->db_Select("forum_p", "forum_parent_id") < 1)
		{
			show_message("No forums yet, please check back soon!");	// 		show_message(FORLAN_13);
		}
		else
		{
			$text .= "
			<div id='pagetitle'>
				<h1>$forum_forum_name</h1>
				<p class='description' id='welcomemessage'>$forum_forum_welcome</p>
			</div>
			<div id='content'>
			<ol class='floatcontainer' id='forums'>";

	// ----------------------------------------------------------------------------- Show Parents --+

			$sql->db_Select("forum_p", "*", "ORDER BY forum_parent_order", false); 
			while($row = $sql->db_Fetch()) 
			{ 
				extract($row);

				$text .= "	<li class='forumbit_nopost L1' id='cat".$forum_parent_id."'>
						<div class='forum_parent'>
							<h2>
								<span class='forum_title'><a href='forum_viewforum.php/p".$forum_parent_id."-".preg_replace('![^a-z0-9]+!i','-',$forum_parent_name)."'>".$forum_parent_name."</a></span>
								<span class='forum_numposts'>Posts</span>
								<span class='forum_lastpost'>Last Post</span>
								<a class='collapse' id='".$forum_parent_id."' href='forum.php#top'><img title='' alt='' src='images/buttons/collapse_40b.png'/></a>
							</h2>
							<div class='forum_rowdata'>
								<p class='parentdescription'>".$forum_parent_description."</p>
							</div>
						</div>
						<ol class='childforum' id='c_cat".$forum_parent_id."'>";

			// -------------------------------------------------------------------- Show Forums --+

				$sql2->db_Select("forum_f", "*", "WHERE forum_parent_id='".$forum_parent_id."' AND forum_forum_sub='0' ORDER BY forum_forum_order", false);
				while($row2 = $sql2->db_Fetch()) 
				{
					extract($row2);

					$text .= "	<li class='forumbit_post' ".$forum_poststatus." L2' id='".$forum_forum_id."'>
							<div class='forum_forum'>
								<img title='Double-click this icon to mark this forum and its content as read' class='forumicon' id='forum_status_icon".$forum_forum_id."' alt='' src='".e_PLUGIN."forum/images/forums/".$forum_forum_id.".png' />
								<div class='forumdata'>
									<div class='datacontainer'>
										<div class='titleline'>
											<h2 class='forumtitle'>
												<a href='forumdisplay.php/p".$forum_parent_id."f".$forum_forum_id."-".preg_replace('![^a-z0-9]+!i',$forum_forum_name)."'>".$forum_forum_name."</a>
											</h2>
										</div>
										<p class='forumdescription'>".$forum_forum_description."</p>";

				// ------------------------------------------------------------ Show Subforums --+

						if($sql3->db_Select("forum_f", "*", "WHERE forum_forum_sub='".$row2['forum_forum_id']."' ORDER BY forum_forum_order", false))
						{
							while($row3 = $sql3->db_Fetch())
							{
								$subforums .= " <a href='p".$forum_parent_id."f".$forum_forum_id."s".$row3['forum_forum_id']."-".preg_replace('![^a-z0-9]+!i','-',$row3['forum_forum_name'])."'>".$row3['forum_forum_name']."</a>";
							}
						//	$subforums = rtrim($subforums, ', ');

							$text .= " 	<p class='subforums'><b>Sub-Forum(s):</b>".$subforums."</p>";
							unset($subforums);
						}

					$text .= "			</div>
								</div>
							</div>
							</li>";

				// ------------------------------------------------------------------------------+
					
				}
                     $text .= " </ol>
                                </li>
                                <br />";
			} 

		// ----------------------------------------------------------------------------------------+

			$text .= "</ol>
                          </div>";

			$ns->tablerender(FORLAN_75, $text);

			$sql3->db_Close();
			$sql2->db_Close();
			$sql->db_Close();
		}
// --------------------------------------------------------------------------------------------------+

require_once(FOOTERF);

?>