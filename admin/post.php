<?php
/**
* Admin page to manage posts
*
* List, add, edit and delete post objects
*
* @copyright	http://smartfactory.ca The SmartFactory
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		marcan aka Marc-André Lanciault <marcan@smartfactory.ca>
* @version		$Id$
*/

/**
 * Edit a Blog Post
 *
 * @param int $post_id Postid to be edited
*/
function editpost($post_id = 0) {
	global $imblogging_post_handler, $icmsModule, $icmsAdminTpl, $xoTheme, $icmsUser;

	$postObj = $imblogging_post_handler->get($post_id);

	if (!$postObj->isNew()){
		$icmsModule->displayAdminMenu(0, _AM_IMBLOGGING_POSTS . " > " . _CO_ICMS_EDITING);
		$postObj->loadCategories();
		$sform = $postObj->getForm(_AM_IMBLOGGING_POST_EDIT, 'addpost');
		$sform->assign($icmsAdminTpl);

	} else {
		$postObj->setVar('post_uid', $icmsUser->uid());
		$icmsModule->displayAdminMenu(0, _AM_IMBLOGGING_POSTS . " > " . _CO_ICMS_CREATINGNEW);
		$sform = $postObj->getForm(_AM_IMBLOGGING_POST_CREATE, 'addpost');
		$sform->assign($icmsAdminTpl);

	}
	$icmsAdminTpl->assign('postid', $post_id);
	$icmsAdminTpl->display('db:imblogging_admin_post.html');
}
/*
$icmsOnDemandPreload[] = array(
	'module'=>'imtagging',
	'filename'=>'jquery.php'
);
$icmsOnDemandPreload[] = array(
	'module'=>'imtagging',
	'filename'=>'imtaggingadmincss.php'
);
*/
include_once "admin_header.php";

/** IcmsOndemandPreload is not supported prior to 1.2 Alpha. This is a workaround */
if (ICMS_VERSION_BUILD < 25) {
	$icmsAdminTpl->assign('imblogging_jquery_inc', '<script type="text/javascript" src="' . ICMS_LIBRARIES_URL . '/jquery/jquery.js"></script><link rel="stylesheet" type="text/css" media="all" href="' . ICMS_URL . '/modules/imtagging/module.css" />');
}

$imblogging_post_handler = icms_getModuleHandler('post');
/** Use a naming convention that indicates the source of the content of the variable */
$clean_op = '';
/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = array('mod','changedField','addpost', 'addcategory', 'del', 'view', '');

if (isset($_GET['op'])) $clean_op = htmlentities($_GET['op']);
if (isset($_POST['op'])) $clean_op = htmlentities($_POST['op']);

/** Again, use a naming convention that indicates the source of the content of the variable */
$clean_post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0 ;
$clean_post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : $clean_post_id;
$clean_category_pid = isset($_POST['category_pid']) ? (int) $_POST['category_pid'] : 0 ;

/**
 * in_array() is a native PHP function that will determine if the value of the
 * first argument is found in the array listed in the second argument. Strings
 * are case sensitive and the 3rd argument determines whether type matching is
 * required
*/
if (in_array($clean_op, $valid_op, true)) {
  switch ($clean_op) {
  	case "addcategory":
  		// the logger needs to be disabled in an AJAX request
  		$xoopsLogger->disableLogger();

		// adding the new category
		$imtagging_category_handler = icms_getModuleHandler('category', 'imtagging');
		$categoryObj = $imtagging_category_handler->create();
		$categoryObj->setVar('category_title', $_POST['category_title']);
		$categoryObj->setVar('category_pid', $clean_category_pid);
		$imtagging_category_handler->insert($categoryObj);

		// rebuild the ImtaggingCategoryTreeElement control
		$postObj = $imblogging_post_handler->get($clean_post_id);

		include_once ICMS_ROOT_PATH . "/class/xoopsformloader.php";
		include_once ICMS_ROOT_PATH . '/modules/imtagging/class/form/elements/imtaggingcategorytreeelement.php';
		$category_tree_element = new ImtaggingCategoryTreeElement($postObj, 'categories');
		echo $category_tree_element->render();
		exit;
  	break;
  	case "mod":
  	case "changedField":

  		icms_cp_header();

  		editpost($clean_post_id);
  		break;
  	case "addpost":
          include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
          $controller = new IcmsPersistableController($imblogging_post_handler);
  		  $controller->storeFromDefaultForm(_AM_IMBLOGGING_POST_CREATED, _AM_IMBLOGGING_POST_MODIFIED);

  		break;

  	case "del":
  	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
        $controller = new IcmsPersistableController($imblogging_post_handler);
  		$controller->handleObjectDeletion();

  		break;

  	case "view" :
  		$postObj = $imblogging_post_handler->get($clean_post_id);

  		icms_cp_header();
  		icms_adminMenu(1, _AM_IMBLOGGING_POST_VIEW . ' > ' . $postObj->getVar('post_title'));

  		icms_collapsableBar('postview', $postObj->getVar('post_title') . $postObj->getEditItemLink(), _AM_IMBLOGGING_POST_VIEW_DSC);

  		$postObj->displaySingleObject();

  		icms_close_collapsable('postview');

  		icms_collapsableBar('postview_posts', _AM_IMBLOGGING_POSTS, _AM_IMBLOGGING_POSTS_IN_POST_DSC);

  		$criteria = new CriteriaCompo();
  		$criteria->add(new Criteria('post_id', $clean_post_id));

  		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
  		$objectTable = new IcmsPersistableTable($imblogging_post_handler, $criteria);
  		$objectTable->addColumn(new IcmsPersistableColumn('post_date', _GLOBAL_LEFT, 150));
  		$objectTable->addColumn(new IcmsPersistableColumn('post_message'));
  		$objectTable->addColumn(new IcmsPersistableColumn('post_uid', _GLOBAL_LEFT, 150));
  		$objectTable->addColumn( new IcmsPersistableColumn('counter'));
  		$objectTable->setDefaultSort('post_published_date');
  		$objectTable->setDefaultOrder('DESC');
  		$objectTable->addIntroButton('addpost', 'post.php?op=mod&post_id=' . $clean_post_id, _AM_IMBLOGGING_POST_CREATE);

  		$objectTable->render();

  		icms_close_collapsable('postview_posts');

  		break;

  	default:

  		icms_cp_header();

  		$icmsModule->displayAdminMenu(0, _AM_IMBLOGGING_POSTS);

  		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
  		$objectTable = new IcmsPersistableTable($imblogging_post_handler);
  		$objectTable->addColumn(new IcmsPersistableColumn('post_title', _GLOBAL_LEFT));
  		$objectTable->addColumn(new IcmsPersistableColumn('post_published_date', 'center', 150));
  		$objectTable->addColumn(new IcmsPersistableColumn('post_uid', 'center', 150));
  		$objectTable->addColumn(new IcmsPersistableColumn('post_status', 'center', 150));
  		$objectTable->addColumn(new IcmsPersistableColumn('counter'));
  		$objectTable->setDefaultSort('post_published_date');
  		$objectTable->setDefaultOrder('DESC');

  		$objectTable->addIntroButton('addpost', 'post.php?op=mod', _AM_IMBLOGGING_POST_CREATE);
  		$objectTable->addQuickSearch(array('post_title', 'post_content'));

  		$objectTable->addFilter('post_status', 'getPost_statusArray');
  		$objectTable->addFilter('post_uid', 'getPostersArray');

  		$icmsAdminTpl->assign('imblogging_post_table', $objectTable->fetch());

  		$icmsAdminTpl->display('db:imblogging_admin_post.html');
  		break;
  		
  }
  icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */
