<?php


#*****************************************************************************
#
# Article.php
#
# Author: 		Wayne Beaton
# Date:			2006-07-18
#
# Description: This file contains the definition of a resources filter class.
#
#****************************************************************************

class Filter {
	var $id;
	var $recent;
	var $types;
	var $category;
	var $author;
	var $since;
	var $sortby;
	
	function Filter() {
	}
	
	function populate_from_html_request_header() {
		$this->id = $_GET['id'];
		$this->recent = $_GET['recent'];
		$this->types = $_GET['type'];
		if ($this->types) if (!is_array($this->types)) $this->types = array($this->types);
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
		return $sortby;
	}
	
	function sorts_on($field) {
		return array_contains($this->sortby, $field);
	}
	
	function initially_sorts_on($field) {
		if (count($this->sortby) == 0) return false;
		return $this->sortby[0] == $field;
	}
	
	function show_type($type) {
		if (!$this->types) return true;
		if (count($this->types) == 0) return true;
		return in_array($type, $this->types);
	}
	
	function show_all() {
		if ($this->id) return false;
		if ($this->recent) return false;
		if ($this->types) return false;
		if ($this->category) return false;
		if ($this->author) return false;
		return true;
	}
	
	function show_resource($resource) {
		if ($this->id) {
			if ($this->id == $resource->id) return true;
			else return false;
		}
		
		if (!$this->show_type($resource->type)) return false;
		if (!$this->is_recent($resource)) return false;
		
		if ($this->category) {
			if (!$resource->has_category($this->category)) return false;
		}
		
		if ($this->author) {
			if (!$resource->has_author($this->author)) return false;
		}
		return true;
	}
	
/*	function show_category($category) {
		if (!$this->has_categories()) return false;
		return in_array($category->id, $this->categories);
	}*/
	
/*	function has_categories() {		
		if (!$this->categories) return false;
		if (count($this->categories) == 0) return false;
		return true;
	}*/
	
	function show_recent() {
		return $this->recent;
	}
	
	function is_recent($resource) {
		if (!$this->recent) return true;
		return $resource->get_date() > $this->since;
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
		
		if ($this->types) {
			$summary = "Eclipse ";
			$count = count($this->types);
			$separator = '';
			foreach($this->types as $type) {
				if (!$type) continue;
				if ($type == 'code') $type = 'code samples';
				else $type = $type.'s';
				
				$summary .= $separator.$type;
				$separator = --$count == 1 ? ', and ' : ', ';
			}
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