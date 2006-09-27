<?php


#*****************************************************************************
#
# Article.php
#
# Author: 		Wayne Beaton
# Date:			2005-11-07
#
# Description: This file commits changes made on the edit_resource.php
# page
#
#****************************************************************************

require_once('resources_mgr.php');
require_once('categories_core.php');

function commit_category_changes() {
	if (!$_GET['submit']) return; // if the user didn't click a submit button, bail out.
		
	if (!user_is_authorized()) return "Please provide a valid user id and password.";
	
	$id = $_GET['id'];
		
	process_additions($id, $_GET['add']);
	process_deletions($id, $_GET['remove']);
	process_new_categories($id, $_GET['new']);
	
	return "Changes processed.";
}

function user_is_authorized() {
	$userid = trim($_GET['userid']);
	$password = trim($_GET['password']);
	
	if (!$userid) return false;
	if (!$password) return false;
	
	return true;
}

function process_additions($id, $additions) {
	if (!$additions) return;
	global $Resources;
	foreach($additions as $addition) {
		$Resources->source->insert_resource_category($id, $addition);
	}
}

function process_deletions($id, $deletions) {
	if (!$deletions) return;
	global $Resources;
	foreach($deletions as $deletion) {
		$Resources->source->delete_resource_category($id, $deletion);
	}
}

function process_new_categories($id, $new) {
	if (!$new) return;
	global $Resources;
	$names = split(',', $new);
	foreach($names as $name) {
		$name = trim($name);
		if (!$name) continue;
		$category_id = $Resources->source->insert_category($name);
		$Resources->source->insert_resource_category($id, $category_id);
	}
}

?>