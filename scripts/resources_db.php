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
 * This file defines the ResourcesDB class. This class is responsible for
 * reading and writing information from and to the database.
 */
require_once('resources_core.php');
require_once('categories_core.php');
require_once('filter_core.php');

class ResourcesDB {
	
	function ResourcesDB() {
		/*
		 * If we're running on the server, the file will be found. If we
		 * are running locally (i.e. unit test environment), the file will
		 * not be found. If we're running on the server, use the server's
		 * DB connection class to build the connection. Otherwise, build it
		 * the old fashioned way.
		 */
		if (file_exists("/home/data/httpd/eclipse-php-classes/system/dbconnection_rw.class.php")) {
			require_once "/home/data/httpd/eclipse-php-classes/system/dbconnection_rw.class.php";
			$dbc = new DBConnectionRW();
			$dbc->connect();
		} else {
			$dbc = mysql_connect(null, "root", null);
			
			if (!$dbc) {
				echo( "<P>Unable to connect to the database server at this time.</P>" );
				return;
			}
			
			mysql_select_db("local_eclipse", $dbc);
		}
	}
	
	function get_all_resources($filter) {
		$sql = 'select resource.id as resource_id, resource.type as resource_type, resource.title as resource_title, resource.description as resource_description, resource.image_path as resource_image '.
			', category.id as category_id, category.name as category_name' .
			', link.id as link_id, link.type as link_type, link.title as link_title, link.create_date as link_date, link.language as link_language, link.path as link_path'.
			', author.id as author_id, author.name as author_name, author.email as author_email, author.company as author_company, author.link as author_link ' .
			' from resource LEFT JOIN resource_category ON (resource.id = resource_category.resource_id) LEFT JOIN category ON (category.id = resource_category.category_id) LEFT JOIN link ON (link.resource_id = resource.id) LEFT JOIN link_author ON (link.id = link_author.link_id) LEFT JOIN author ON (link_author.author_id = author.id)'.
			' where 1=1';
				
		if ($filter->type) {
			$sql .= " and resource.type = '$filter->type'";			
		}
		
		if ($filter->category) {
			// The following line works with MySQL 4.5+
			// $sql .= " and (resource.id in (select resource.id from resource join resource_category on (resource.id = resource_category.resource_id) join category on (resource_category.category_id = category.id) where category.name = '$filter->category'))";
			
			$resource_ids = $this->get_resource_ids_for_category($filter->category);
			$sql .= " and (resource.id in $resource_ids)";
		}
		
		if ($filter->author) {
			$author = addslashes($filter->author);
			// The following line works with MySQL 4.5+
			// $sql .= " and (resource.id in (select resource.id from resource join link on (link.resource_id = resource.id) join link_author on (link.id = link_author.link_id) join author on (link_author.author_id = author.id) where author.name = '$author'))";
			
			$resource_ids = $this->get_resource_ids_for_author($author);
			$sql .= " and (resource.id in $resource_ids)";
			
		}
	
		if ($filter->recent) {
			$date = strtotime("-6 months");
			$date = date('Y-m-d', $date);
			$sql .= " and DATE_SUB(CURDATE(),INTERVAL 6 MONTH) <= link.create_date";
		}
		
		if (count($filter->sortby) > 0) {
			$sql .= ' order by ';
			$separator = '';
			foreach($filter->sortby as $sort) {
				if ($sort == 'type') $sql .= $separator.'resource.type';
				else if ($sort == 'title') $sql .= $separator.'resource.title';
				else if ($sort == 'date') $sql .= $separator.'link.create_date desc';
				$separator = ', ';
			}
		}
				
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			return null;
		}
		
		$resources_builder = new ResourcesBuilder();
		$resources_builder->build_resources($result);
		
		return $resources_builder->resources;
	}
	
	/*
	 * This function goes away when we upgrade to MySQL 4.5 or better.
	 * It exists because MySQL 4.0.x doesn't support nested selects
	 * (i.e. subqueries).
	 */
	function get_resource_ids_for_author($author) {
		$sql = "select resource.id from resource join link on (link.resource_id = resource.id) join link_author on (link.id = link_author.link_id) join author on (link_author.author_id = author.id) where author.name = '$author'";
		$result = mysql_query($sql);
		$clause = '(';
		$separator = '';
		while (($row = mysql_fetch_row($result)) != null) {
			$clause.=$separator.$row[0];
			$separator =', ';
		}
		$clause .= ')';
		return $clause;
	}
	
	/*
	 * This function goes away when we upgrade to MySQL 4.5 or better.
	 * It exists because MySQL 4.0.x doesn't support nested selects
	 * (i.e. subqueries).
	 */
	function get_resource_ids_for_category($category) {
		$sql = "select resource.id from resource join resource_category on (resource.id = resource_category.resource_id) join category on (resource_category.category_id = category.id) where category.name = '$category'";
		$result = mysql_query($sql);
		$clause = '(';
		$separator = '';
		while (($row = mysql_fetch_row($result)) != null) {
			$clause.=$separator.$row[0];
			$separator =', ';
		}
		$clause .= ')';
		return $clause;
	}
	
	function get_resource($id) {
		$sql = 'select resource.id as resource_id, resource.type as resource_type, resource.title as resource_title, resource.description as resource_description, resource.image_path as resource_image '.
			', category.id as category_id, category.name as category_name' .
			', link.id as link_id, link.type as link_type, link.title as link_title, link.create_date as link_date, link.language as link_language, link.path as link_path'.
			', author.id as author_id, author.name as author_name, author.email, author.company, author.link ' .
			' from resource LEFT JOIN resource_category ON (resource.id = resource_category.resource_id) LEFT JOIN category ON (category.id = resource_category.category_id) LEFT JOIN link ON (link.resource_id = resource.id) LEFT JOIN link_author ON (link.id = link_author.link_id) LEFT JOIN author ON (link_author.author_id = author.id)'.
			" where resource.id=$id";
		
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			return null;
		}
		
		$resources_builder = new ResourcesBuilder();
		$resources_builder->build_resources($result);
		
		$resources = $resources_builder->resources;
		if (count($resources) == 0) return null;
		return $resources[$id];
	}

	/*
	 * This function returns the id of the resource that occurs
	 * after $id.
	 */
	function get_next_resource_id($id) {
		$sql = "select id from resource where id > $id order by id limit 1";
	
		$result = mysql_query($sql);	
		
		if (!$result) {
			echo mysql_error();
			return null;
		}
		
		if ($row = mysql_fetch_array($result)) {
			return $row[0];
		}
		
		return null;
	}

	/*
	 * This function returns the id of the resource that occurs
	 * before $id.
	 */
	function get_previous_resource_id($id) {
		$sql = "select id from resource where id < $id order by id desc limit 1";
	
		$result = mysql_query($sql);
		
		if (!$result) {
			echo mysql_error();
			return null;
		}
		
		if ($row = mysql_fetch_array($result)) {
			return $row[0];
		}
		return null;
	}
	
	function add_category_to_resource($id, $category) {
		echo $category;
	}
	
	function & get_authors_with_resource_count() {
		$sql = "select author.name, count(resource.id) " .
			"from resource join link on (resource.id = link.resource_id) join link_author on (link.id = link_author.link_id) join author on (link_author.author_id = author.id)" .
			"group by author.name";
		
		$result = mysql_query($sql);
		if (!$result) {
			echo mysql_error();
			return array();
		}
		$authors = array();
		while (($row = mysql_fetch_row($result)) != null) {
			$authors[$row[0]] = $row[1];
		}
		return $authors;
	}

	function get_categories() {	
		$sql = "select id, name from category order by name";
		
		$result = mysql_query($sql);
		
		if (!$result) {
			echo mysql_error();
			return array();
		}
		
		$categories = array();
		while (($row = mysql_fetch_row($result)) != null) {
			$category = new ResourceCategory();
			$category->id = $row[0];
			$category->name = $row[1];
			array_push($categories, $category);
		}
		return $categories;
	}
	
	function get_authors() {	
		$sql = "select id, name, email, company, link from author order by name";
		
		$result = mysql_query($sql);
		
		if (!$result) {
			echo mysql_error();
			return array();
		}
		
		$authors = array();
		while (($row = mysql_fetch_row($result)) != null) {
			$author = new Author();
			$author->id = $row[0];
			$author->name = $row[1];
			$author->email = $row[2];
			$author->company = $row[3];
			$author->company = $row[4];
			array_push($authors, $author);
		}
		return $authors;
	}
	
	function & get_categories_with_resource_count() {
		$sql = "select category.name, count(resource.id) " .
			"from category, resource_category, resource " .
			"where category.id = resource_category.category_id and resource_category.resource_id = resource.id " .
		 	"group by category.name order by upper(category.name)";
		
		$result = mysql_query($sql);
		$categories = array();
		while (($row = mysql_fetch_row($result)) != null) {
			$categories[$row[0]] = $row[1];
		}
		return $categories;
	}
	
	function insert_resource($resource) {
		$title = addslashes(trim($resource->title));
		$description = addslashes(trim($resource->description));
		$sql = "insert into resource (type, title, description, image_path) values ('$resource->type', '$title', '$description', '$resource->image')";
		$this->execute_query($sql);
		$resource_id = mysql_insert_id();
		foreach ($resource->links as $link) {
			$this->insert_link($resource_id, $link);
		}
		foreach ($resource->categories as $category) {
			$category_id = $this->insert_category($category);
			$this->insert_resource_category($resource_id, $category_id);
		}
		return $resource_id;
	}
	
	function insert_link($resource_id, $link) {
		$title = addslashes($resource->title);
		$date = date('Y-m-d', $link->get_date());
		$sql = "insert into link (resource_id, type, title, language, path, create_date) values ($resource_id, '$link->type', '$title', '$link->language', '$link->path', '$date')";
		$this->execute_query($sql);
		$link_id = mysql_insert_id();
		foreach($link->authors as $author) {
			$author_id = $this->insert_author($author);
			$this->insert_link_author($link_id, $author_id);
		}
		return $link_id;
	}
	
	function insert_author($author) {
		$name = addslashes(trim($author->name));
		$result = mysql_query("select id from author where name='$name'");
		$row = mysql_fetch_row($result);
		if ($row) return $row[0];
		$company = addslashes($author->company);
		$sql = "insert into author (name, email, company, link) values ('$name', '$author->email', '$company', '$author->link')";
		$this->execute_query($sql);
		return mysql_insert_id();
	}
	
	function insert_link_author($link_id, $author_id) {
		$sql = "insert into link_author (link_id, author_id) values ($link_id, $author_id)";
		execute_query($sql);
	}
	
	/*
	 * This function inserts the given instance of Category into the database.
	 * A check is first done to see if a category already exists in the database
	 * with the same name. If such a category does exist, the instance's id
	 * is replaced with the value from the database, and the value of the id
	 * is returned.
	 */
	function insert_category(& $category) {
		$name = addslashes(trim($category->name));
		$result = mysql_query("select id from category where id='$name'");
		$row = mysql_fetch_row($result);
		if ($row) {
			$id = $row[0];
			$category->id = $id;
			return $id;
		}
		$sql = "insert into category (name) values ('$name')";
		$this->execute_query($sql);
		$id = mysql_insert_id();
		return $id;
	}
	
	function insert_resource_category($resource_id, $category_id) {
		$sql = "insert into resource_category (resource_id, category_id) values ($resource_id, $category_id)";
		$this->execute_query($sql);
	}

	function delete_resource_category($resource_id, $category_id) {
		$sql = "delete from resource_category where resource_id=$resource_id and category_id=$category_id";
		$this->execute_query($sql);
	}
	
	function execute_query($query) {
		if (!mysql_query($query)) {
			$error = mysql_error();
			echo "Query '$query' failed!<br/><b>$error</b><br/>";
		}
	}
}

class ResourcesBuilder {
	var $resources = array();

	function &build_resources(&$result) {
		while (($row = mysql_fetch_assoc($result)) != null) {
			$resource = &$this->find_or_build_resource($row);
			$this->append_category($resource, $row);
			$this->append_link($resource, $row);
		}
		return $resources;
	}
	
	function &find_or_build_resource(&$row) {
		$key = $row['resource_id'];
		$resource = &$this->resources[$key];
		//if ($resource) echo "Found $resource->title<br>";
		if (!$resource) {
			$resource = &$this->build_resource($row);
			$this->resources[$key] = &$resource;		
			//echo "Built $resource->title<br>";
		}
		return $resource;
	}
	
	function &build_resource(&$row) {
		$resource = new Resource();
		$resource->id = $row['resource_id'];
		$resource->type = $row['resource_type'];
		$resource->title = $row['resource_title'];
		$resource->description = $row['resource_description'];
		$resource->image = $row['resource_image'];
		
		return $resource;
	}
	
	function append_category(&$resource, $row) {			
		$category_id = $row['category_id'];	
		$category_name = $row['category_name'];
		if ($category_name == null) return;
		foreach($resource->categories as $category) {
			if ($category->id == $category_id) return;
		}
		array_push($resource->categories, new ResourceCategory($category_id, $category_name));
	}

	function append_link(&$resource, &$row) {			
		$link_id = $row['link_id'];
		if ($link_id == null) return;
		$link = &$resource->links[$link_id];
		if (!$link) {
			$link = &$this->build_link($row);
			$resource->links[$link_id] = &$link;
		}
		$this->append_author($link, $row);
	}
	
	function &build_link(&$row) {
		$link = new ResourceLink();
		$link->id = $row['link_id'];
		$link->language = $row['link_language'];
		$link->title = $row['link_title'];
		$link->path = $row['link_path'];
		$link->type = $row['link_type'];
		$link->date = strtotime($row['link_date']);
		
		return $link;
	}
	
	function append_author(&$link, &$row) {
		$author_id = $row['author_id'];
		if ($author_id == null) return;
		$author = &$link->authors[$author_id];
		if (!$author) {
			$author = &$this->build_author($row);
			$link->authors[$author_id] = &$author;
		}
	}
	
	function &build_author(&$row) {
		$author = new Author();
		$author->id = $row['author_id'];
		$author->name = $row['author_name'];
		$author->email = $row['author_email'];
		$author->company = $row['author_company'];
		$author->link = $row['author_link'];
		
		return $author;
	}
}
?>