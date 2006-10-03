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
 * This file defines the Resource and ResourceLink classes. These classes
 * are used to represent resource information.
 */
require_once("authors_core.php");

class Resource {
	var $id;
	var $type;
	var $title;
	var $description;
	var $categories = array();
	var $links = array();
	var $image;
		
	function get_date() {
		$date = 0;
		foreach($this->links as $link) {
			$link_date = $link->get_date();
			if ($link_date > $date) $date = $link_date;
		}
		return $date;
	}
	
	function has_author($author) {
		foreach($this->links as $link) {
			if ($link->has_author($author)) return true;
		}
		return false;
	}
	
	function get_authors() {
		$authors = array();
		foreach ($this->links as $link) {
			$authors = array_merge($authors, $link->authors);
		}
		return $authors;
	}
	
	function has_category($name) {
		foreach ($this->categories as $category) {
			if (strcasecmp($name, $category->name) == 0) return true;
		}
		return false;
	}
}

class ResourceLink {
	var $id;
	var $title;
	var $type;
	var $language = 'en';
	var $path;
	var $date;
	var $authors = array();
	var $updates = array();
	
	function get_date() {
		$date = $this->date;
		foreach($this->updates as $update) {
			if ($update->date > $date) $date = $update->date;
		}
		return $date;
	}
	
	function has_author($author_name) {
		foreach($this->authors as $author) {
			if (strcasecmp($author_name,$author->name) == 0) return true;
		}
		return false;
	}
}

?>