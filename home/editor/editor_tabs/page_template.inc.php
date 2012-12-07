<?php
/************************************************************************/
/* AContent															 */
/************************************************************************/
/* Copyright (c) 2010												   */
/* Inclusive Design Institute										   */
/*																	  */
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License		  */
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/PrivilegesDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');

include('templates/system/page_template.css');

/* Provo ad inserire la pagina che contiene il codice javascript*/
include_once($mod_path['templates_sys'].'Page_templates.js');

global $savant;

$contentDAO = new ContentDAO();
$privilegesDAO = new PrivilegesDAO();
$output = '';

######################################
#	Variables declarations / definitions
######################################
global $_course_id, $_content_id;

$_course_id = $course_id = (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);
$_content_id = $cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id; /* content id of an optional chapter */

if ($cid == 0) {
	$msg->printErrors('SAVE_BEFORE_PROCEED');
	require_once(TR_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// paths settings

$mod_path					= array();
$mod_path['templates']		= realpath(TR_BASE_HREF			. 'templates').'/';
$mod_path['templates_int']	= realpath(TR_INCLUDE_PATH		. '../templates').'/';
$mod_path['templates_sys']	= $mod_path['templates_int']	. 'system/';
$mod_path['page_template_dir']		= $mod_path['templates']		. 'page_template/';
$mod_path['page_template_dir_int']	= $mod_path['templates_int']	. 'page_template/';

// include the file "apply_model" so that he can inherit variables and constants defined by the system
include_once($mod_path['templates_sys'].'Page_template.class.php');

// instantiate the class page_template (which calls the constructor)
$mod = new Page_template($mod_path);

$user_priv = $privilegesDAO->getUserPrivileges($_SESSION['user_id']);
$is_author = $user_priv[1]['is_author'];

// take the list of available valid page_template

$pageTemplateList = array();

$content_page = $content['text'];

$templates = TR_BASE_HREF.'templates/';
$templates_int = TR_INCLUDE_PATH.'../templates/';

// path containing the page_template list
$page_template_dir = $templates.'page_template/';
$page_template_dir_int = $templates_int.'page_template/';

// directory and file systems to be excluded from the page_template list
$excep = array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');

######################################
#	RETURN OUTPUT
######################################

$content_layout = $content['layout']; // Retrieving the value of the layout

$sql="SELECT layout FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
$result=$dao->execute($sql);

if(is_array($result))
{
	foreach ($result as $support) {
		$layout=$support['layout'];
		break;
	}
}

$sql="SELECT text FROM ".TABLE_PREFIX."content WHERE content_id=".$cid."";
$result=$dao->execute($sql);

if(is_array($result))
{
	foreach ($result as $support) {
		$text=$support['text'];
		break;
	}
}

//content-length if less than 24 there is content, being 24 to the div id = "content" that is inserted automatically 
$sup=strlen($text);

// call the function that creates the graphics module selection
// step content length and the value cid-->content_id
$output	= $mod->createUI($sup,$cid);

echo '<link type="text/css" rel="stylesheet" href="'.TR_BASE_HREF.'/themes/default/form.css">';	
echo '<div id="success" style="display:none;">';
echo '<label  class="success_label">Action completed successfully.</label>';
echo '</div>';

echo '<div id="no-cont-pre" style="display:none; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';
echo '<div style="margin-left:10px;">'. _AT("no_content_associated").'</div>';
echo '</div>';

echo '<div id="whit-cont-pre" style="display:none; margin: 10px; margin-top: 20px; margin-bottom: 15px;">';
echo '<div style="margin-left:10px;" style="font-weight:bold;">'. _AT("content_associated") .':</div></div>';

if($sup<=24) { 
	echo '<div style="margin: 10px; margin-top: 10px; margin-bottom: 15px;">';
	echo '<div id="no-cont">'. _AT("no_content_associated") . '</div>';
	echo '</div>';
	$whit_content=0;
	$mod->view_page_templates($whit_content);
} else {
	echo '<link type="text/css" rel="stylesheet" href="'.TR_BASE_HREF.'templates/layout/'.$layout.'/'.$layout.'.css">';
	echo '<div style="margin: 10px; margin-top: 10px; margin-bottom: 15px;">';
	echo '<div id="whit-cont" style="font-weight:bold;">'. _AT("content_associated") .':</div>';
	// I insert a new div to try not to lose the old contents in the case of rescue
	echo '<div id="content-previous">';
		echo $text;
	echo '</div>';
	echo'</div>';
	
	$whit_content=1;
	$mod->view_page_templates($whit_content);
}

// NEW VERSION
if ($output == '') {
	$output = _AT('none_found');
}

// content
echo $output;

?>

<script>
$('.unsaved').css('display','none');
</script>