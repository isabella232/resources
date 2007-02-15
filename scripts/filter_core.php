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
function getHTTPParameter($name) {
	if(array_key_exists($name, $_GET))
		return $_GET[$name];
	return null;
}

/*
 * This file defines the Filter class. This class is used to filter
 * the list of resources to the minimal set desired by the user.
 * The populate_from_html_request_header() method populates the 
 * receiver with information from the $_GET variable, including:
 * - recent, displays only the most recently changed or added
 *   resources (changed or added in the last six months). By default
 *   all resources are show unless this parameter is set to any value.
 * - type, displays only the resources with the given type. Valid values 
 *    are 'book', 'article', 'publication', 'presentation', 'demo', or 'code'.
 * - category, displays only the resources that are in a category
 *   with the given name.
 * - author, displays only the resources that contain a link authored
 *   by the given author.
 * - since, displays only the resources that have changed since the
 *   provided date (any date that can be parsed by strtotime()).
 * - sortby, determines the order of the resources.
 * 
 * The 'article' type refers to an Eclipse Corner Article; 'publication'
 * refers to articles found in other places.
 */
class Filter {
	var $id;
	var $recent;
	var $type;
	var $category;
	var $author;
	var $since;
	var $sortby;
	
	function Filter() {
	}
	
	function populate_from_html_request_header() {
		$this->id = getHTTPParameter('id');
		$this->recent = getHTTPParameter('recent');
		$this->type = getHTTPParameter('type');
		if (!in_array($this->type, array('article', 'publication', 'webinar', 'podcast', 'book', 'presentation', 'demo', 'code', 'course'))) {
			$this->type = null;
		}
		$this->category = getHTTPParameter('category');
		if (getHTTPParameter('author')) $this->author = html_entity_decode(getHTTPParameter('author'));
		if (getHTTPParameter('since')) 
			$this->since = strtotime(getHTTPParameter('since'));
		else
			$this->since = strtotime("-6 months");
		$this->sortby = $this->get_sort_fields(getHTTPParameter('sort'));
	}
	
	function get_sort_fields($fields) {
		$fields = split(',', $fields);
		$sortby = array();
		foreach($fields as $field) {
			$field = trim($field);
			if (strlen($field) > 0) array_push($sortby, $field);
		}
		if (count($sortby)==0) array_push($sortby, 'date');
		return $sortby;
	}
	
	function sorts_on($field) {
		return array_contains($this->sortby, $field);
	}
	
	function initially_sorts_on($field) {
		if (count($this->sortby) == 0) return false;
		return $this->sortby[0] == $field;
	}
		
	function show_all() {
		if ($this->id) return false;
		if ($this->recent) return false;
		if ($this->type) return false;
		if ($this->category) return false;
		if ($this->author) return false;
		return true;
	}
			
	function show_recent() {
		return $this->recent;
	}
		
	function count($resources) {
		$count = 0;
		foreach($resources as $resource) {
			if ($this->show_resource($resource)) $count++;
		}
		return $count;
	}
	
	function get_url_parameters($sort_field = null) {
		$filter = '';
		$param_separator = '';

		/*
		 * Determine the list of fields to sort on an
		 * in what order. The list does not contain
		 * repeats.
		 */
		$sort = array();
		// If $sort_field is specified, it goes first.
		if ($sort_field) array_push($sort, $sort_field);
		// Add existing sort fields in the order they occur.
		// Do not add if the field is already in the array.
		foreach($this->sortby as $sortby) {
			if (!$sortby) continue; // Don't add blanks
			if (!in_array($sortby, $sort)) array_push($sort, $sortby);
		}
	
		if (count($sort) > 0) {
			$filter.=$param_separator.'sort=';
			$param_separator = '&';
			$separator='';
			foreach($sort as $field) {
				$filter .= "$separator$field";
				$separator=',';
			}
		}
		
		if ($this->category) {
			$filter .= $param_separator."category=$this->category";
			$param_separator = '&';
		}
		
		return $filter;
	}
	
	function get_summary() {
		$summary = '';
		$param_separator = '';
		if ($this->id) return "";
		
		if ($this->type) {
			$summary = "Eclipse ";
			$type = $this->type;
			if ($type == 'code') $type = 'code samples';
			else if ($type == 'demo') $type = 'demonstrations';
			else $type = $type.'s';
			$summary .= $type;
		} else {
			$summary = "All Eclipse resources";
		}
	
		if ($this->author) {
			$summary .= " authored by $this->author";
		}
		
		if ($this->show_recent()) {
			$summary .= " added or changed in the last six months";
		}
				
		if ($this->category) {
			$summary .= " that cover \"$this->category\"";
		} 
				
		return $summary;
	}
}
?>