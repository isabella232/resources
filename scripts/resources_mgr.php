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

#*****************************************************************************
#
# resources_mgr.php
#
# Author: 		Wayne Beaton
# Date:			2005-11-07
#
# Description: 
#    This file defines the ResourcesMgr class along with a singleton instance,
#    $Resources. This class is used as an entry point into the underlying resources
#    storage mechanism (it is a Facade).
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
	
	function get_categories_with_resource_count($type=null) {
		return $this->source->get_categories_with_resource_count($type);
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