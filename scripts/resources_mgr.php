<?php

#*****************************************************************************
#
# Article.php
#
# Author: 		Wayne Beaton
# Date:			2005-11-07
#
# Description: This file defines the Article class which is used to define,
# and render as html, articles.
#
#****************************************************************************
require_once('resources_core.php');
require_once('resources_db.php');

$Resources = new ResourcesMgr();

class ResourcesMgr {
	var $source;

	function ResourcesMgr () {
		$this->source = new ResourcesDb();
		//$this->source = new ResourcesXML();
	}
	
	function get_resources($filter=null) {
		if (!$filter) $filter = new Filter();
		return $this->source->get_all_resources($filter);
	}
	
	function get_resource($id) {
		return $this->source->get_resource($id);
	}
		
	function get_authors_with_resource_count() {
		return $this->source->get_authors_with_resource_count();
	}
	
	function get_authors() {
		return $this->source->get_authors();
	}
	
	function get_categories_with_resource_count() {
		return $this->source->get_categories_with_resource_count();
	}
	
	function get_categories() {
		return $this->source->get_categories();
	}
	
	function get_next_resource_id($id) {
		return $this->source->get_next_resource_id($id);
	}

	function get_previous_resource_id($id) {
		return $this->source->get_previous_resource_id($id);
	}
	
	function add_category_to_resource($id, $category) {
		return $this->source->add_category_to_resource($id, $category);
	}
}
?>