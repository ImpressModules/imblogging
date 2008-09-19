<?php
/**
* imBlogging version infomation
*
* This file holds the configuration information of this module
*
* @copyright	http://smartfactory.ca The SmartFactory
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		marcan aka Marc-André Lanciault <marcan@smartfactory.ca>
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**  General Information  */
$modversion = array(
  'name'=> _MI_IMBLOGGING_MD_NAME,
  'version'=> 1.0,
  'description'=> _MI_IMBLOGGING_MD_DESC,
  'author'=> "The SmartFactory",
  'credits'=> "INBOX International inc.",
  'help'=> "",
  'license'=> "GNU General Public License (GPL)",
  'official'=> 0,
  'dirname'=> basename( dirname( __FILE__ ) ),

/**  Images information  */
  'iconsmall'=> "images/icon_small.png",
  'iconbig'=> "images/icon_big.png",
  'image'=> "images/icon_big.png", /* for backward compatibility */

/**  Development information */
  'status_version'=> "Beta 1",
  'status'=> "Beta",
  'date'=> "unreleased",
  'author_word'=> "",

/** Contributors */
  'developer_website_url' => "http://smartfactory.ca",
  'developer_website_name' => "The SmartFactory",
  'developer_email' => "info@smartfactory.ca");
$modversion['people']['developers'][] = "[url=http://smartfactory.ca/userinfo.php?uid=1]marcan[/url] (Marc-Andr&eacute; Lanciault)";
$modversion['people']['developers'][] = "[url=http://smartfactory.ca/userinfo.php?uid=112]felix[/url] (F&eacute;lix Tousignant)";
//$modversion['people']['testers'][] = "";
//$modversion['people']['translators'][] = "";
//$modversion['people']['documenters'][] = "";
//$modversion['people']['other'][] = "";
//$modversion['warning'] = _CO_SOBJECT_WARNING_BETA;

/** Administrative information */
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

/** Database information */
$modversion['object_items'][1] = 'post';
$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/** Install and update informations */
$modversion['onInstall'] = "include/onupdate.inc.php";
$modversion['onUpdate'] = "include/onupdate.inc.php";

/** Search information */
$modversion['hasSearch'] = 1;
$modversion['search'] = array (
  'file' => "include/search.inc.php",
  'func' => "imblogging_search");

/** Menu information */
$modversion['hasMain'] = 1;
global $xoopsModule;
if (is_object($xoopsModule) && $xoopsModule->dirname() == 'imblogging') {
	$imblogging_post_handler = xoops_getModuleHandler('post', 'imblogging');
	if ($imblogging_post_handler->userCanSubmit()) {
		$modversion['sub'][1]['name'] = _SUBMIT;
		$modversion['sub'][1]['url'] = 'post.php?op=mod';
	}
}

/** Blocks information */
$modversion['blocks'][1] = array(
  'file' => 'post_recent.php',
  'name' => _MI_IMBLOGGING_POSTRECENT,
  'description' => _MI_IMBLOGGING_POSTRECENTDSC,
  'show_func' => 'imblogging_post_recent_show',
  'edit_func' => 'imblogging_post_edit',
  'options' => '5',
  'template' => 'imblogging_post_recent.html');

/** Templates information */
$modversion['templates'][1] = array(
  'file' => 'imblogging_header.html',
  'description' => 'Module Header');

$modversion['templates'][] = array(
  'file' => 'imblogging_footer.html',
  'description' => 'Module Footer');

$modversion['templates'][]= array(
  'file' => 'imblogging_admin_post.html',
  'description' => 'Post Index');

$modversion['templates'][] = array(
  'file' => 'imblogging_index.html',
  'description' => 'Post Index');

$modversion['templates'][] = array(
  'file' => 'imblogging_single_post.html',
  'description' => 'Single post template');

$modversion['templates'][] = array(
  'file' => 'imblogging_post.html',
  'description' => 'Post page');

/** Preferences information */

// Retrieve the group user list, because the automatic group_multi config formtype does not include Anonymous group :-(
$member_handler =& xoops_getHandler('member');
$groups_array = $member_handler->getGroupList();
foreach($groups_array as $k=>$v) {
	$select_groups_options[$v] = $k;
}

$modversion['config'][1] = array(
  'name' => 'poster_groups',
  'title' => '_MI_IMBLOGGING_POSTERGR',
  'description' => '_MI_IMBLOGGING_POSTERGRDSC',
  'formtype' => 'select_multi',
  'valuetype' => 'array',
  'options' => $select_groups_options,
  'default' =>  '1');

$modversion['config'][] = array(
  'name' => 'posts_limit',
  'title' => '_MI_IMBLOGGING_LIMIT',
  'description' => '_MI_IMBLOGGING_LIMITDSC',
  'formtype' => 'textbox',
  'valuetype' => 'text',
  'default' => 5);

/** Comments information */
$modversion['hasComments'] = 1;

$modversion['comments'] = array(
  'itemName' => 'post_id',
  'pageName' => 'post.php',
  /* Comment callback functions */
  'callbackFile' => 'include/comment.inc.php',
  'callback' => array(
    'approve' => 'imblogging_com_approve',
    'update' => 'imblogging_com_update')
    );

/** Notification information */
$modversion['hasNotification'] = 1;

$modversion['notification'] = array (
  'lookup_file' => 'include/notification.inc.php',
  'lookup_func' => 'imblogging_notify_iteminfo');

$modversion['notification']['category'][1] = array (
  'name' => 'global',
  'title' => _MI_IMBLOGGING_GLOBAL_NOTIFY,
  'description' => _MI_IMBLOGGING_GLOBAL_NOTIFY_DSC,
  'subscribe_from' => array('index.php', 'post.php'));

$modversion['notification']['event'][1] = array(
  'name' => 'post_published',
  'category'=> 'global',
  'title'=> _MI_IMBLOGGING_GLOBAL_POST_PUBLISHED_NOTIFY,
  'caption'=> _MI_IMBLOGGING_GLOBAL_POST_PUBLISHED_NOTIFY_CAP,
  'description'=> _MI_IMBLOGGING_GLOBAL_POST_PUBLISHED_NOTIFY_DSC,
  'mail_template'=> 'global_post_published',
  'mail_subject'=> _MI_IMBLOGGING_GLOBAL_POST_PUBLISHED_NOTIFY_SBJ);

?>