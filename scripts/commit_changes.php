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

function commit_changes() {
	if (!$_GET['submit']) return; // if the user didn't click a submit button, bail out.
	
	$id = $_GET['id'];
		
}

function update_resource($id) {
	
}

function create_resource($id) {
	global $Resources;
	$resource = new Resource();
	$resource->title = $_GET['title'];
	$resource->description = $_GET['description'];
	$resource->type = $_GET['type'];
	$resource->image = $_GET['image'];
	echo "Adding...";
	return $Resources->insert_resource($resource);	
}

?>