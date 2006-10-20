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
 * This file defines the Filter class. This class is used to filter
 * the list of resources to the minimal set desired by the user.
 * The populate_from_html_request_header() method populates the 
 * receiver with information from the $_GET variable, including:
 * - recent, displays only the most recently changed or added
 *   resources (changed or added in the last six months). By default
 *   all resources are show unless this parameter is set to any value.
 * - type, displays only the resources with the given type. Valid values 
 *    are 'book', 'article', 'presentation', 'demo', or 'code'.
 * - category, displays only the resources that are in a category
 *   with the given name.
 * - author, displays only the resources that contain a link authored
 *   by the given author.
 * - since, displays only the resources that have changed since the
 *   provided date (any date that can be parsed by strtotime()).
 * - sortby, determines the order of the resources.
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
		$this->id = $_GET['id'];
		$this->recent = $_GET['recent'];
		$this->type = $_GET['type'];
		if (!in_array($this->type, array('article', 'book', 'presentation', 'demo', 'code'))) {
			$this->type = null;
		}
		$this->category = $_GET['category'];
		if ($_GET['author']) $this->author = html_entity_decode($_GET['author']);
		if ($_GET['since']) 
			$this->since = strtotime($_GET['since']);
		else
			$this->since = strtotime("-6 months");
		$this->sortby = $this->get_sort_fields($_GET['sort']);
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
	
	function get_url_parameters($sort_field = null, $category=null, $author=null) {
		$filter = '';
		$param_separator = '';
		if ($this->id) return "id=$this->id";
		
		if ($this->show_recent()) {
			$filter .= $param_separator."recent=true";
			$param_separator = '&';
		}
		
		if ($this->types) {
			foreach($this->types as $type) {
				if (!$type) continue;
				$filter .= $param_separator."type[$count]=$type";
				$param_separator = '&';
			}
		}
		
		if ($category) {
			$filter .= $param_separator."category=$category";
			$param_separator = '&';
		} else if ($this->category) {
			if (!$author) { // only if the author is not explicitly specified.
				$filter .= $param_separator."category=$this->category";
				$param_separator = '&';
			}
		}
			
		if ($author) {
			$filter .= $param_separator."author=$author";
			$param_separator = '&';
		} else if ($this->author) {
			if (!$category) { // only if the category is not explicitly specified.
				$filter .= $param_separator."author=$this->author";
				$param_separator = '&';
			}
		}
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
		
		$summary .= '.';
		
		return $summary;
	}
}
?>