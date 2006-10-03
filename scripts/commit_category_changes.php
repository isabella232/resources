<?php
/*******************************************************************************
 * Copyright (c) 2006 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Wayne Beaton (Eclipse Foundation)- initial API and implementation
 *******************************************************************************/

/*
 * This file contains code that commits changes to categories. 
 * It was created a stopgap measure to facilitate the tagging of resources
 * until a proper UI can be created.
 */
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