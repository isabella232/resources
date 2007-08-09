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
require_once('categories_core.php');
require_once('authors_core.php');
require_once('filter_core.php');

class Resources {
  var $connection;

  function Resources() {
 	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/smartconnection.class.php");
  	$this->connection = new DBConnection();
	$this->connection->connect();
  }

  function dispose() {
    $this->connection->disconnect();
  }
  
	function get_recent_resources_summary($maximum) {
		$result = mysql_query("select resource.id, resource.title, max(link.create_date) as link_date from resource, link where resource.id = link.resource_id group by resource.id order by link_date desc limit $maximum");
		if ($error = mysql_error()) {
			return $error;
		}
		
		$html = '<ul>';
		
		while ($row = mysql_fetch_array($result)) {
			$id = $row[0];
			$title = $row[1];
			$date = $row[2];
			$date = strtotime($date);
			$ago = $this->get_time_passed_string($date);
			$html .= "<li><a href=\"/resources/resource.php?id=$id\">$title</a> <span class=\"posted\">$ago</span></li>";
		}
		
		$html .= '</ul>';
		return $html;
	}
	
  function get_pillar_resources_table(&$pillar, $title) {
    $filter = new Filter();
    $filter->category = $pillar;
    $filter->sortby = array('date');

    return $this->get_resources_table($this->get_resources($filter), $filter, $title);
  }
  
  function get_resources_table(&$resources, &$filter, $label="Technical Resources") {
    ob_start();
    ?>
<table class="resourcesTableHeader" cellspacing="0" width="100%">
<? if ($label != "") { ?>
	<tr>
		<td colspan=4 class="tableHeaderTitle"><?= $label ?></td>
	</tr>
	<? } ?>
	<tr>
		<td width="50%" class="resourcesHeader"
			style="border-left:1px solid black;"><a
			href="?<?=$filter->get_url_parameters('title')?>">Title<?=$this->get_sort_icon($filter, 'title')?></a></td>
		<td width="10%" class="resourcesHeader"><a
			href="?<?=$filter->get_url_parameters('type')?>">Type<?=$this->get_sort_icon($filter, 'type')?></a></td>
		<td width="10%" class="resourcesHeader"><a
			href="?<?=$filter->get_url_parameters('date')?>">Date<?=$this->get_sort_icon($filter, 'date')?></a></td>
		<td width="10%" class="resourcesHeader"
			style="border-right:1px solid black;" align="center">&nbsp;</td>
	</tr>
</table>
<div class="resources">
<table width="100%" class="resourcesTable" cellspacing="0">

<?
$countID = 0;
foreach($resources as $resource) {
  $date = date("M d, Y", $resource->get_date()); // . "<br/><font size=-2>".$this->get_time_passed_string($resource->get_date())."</font>";
  $date = str_replace(" ", "&nbsp;", $date);
  ?>
	<tr class="resourcesData">

		<td width="50%">
		<div class="invisible" id="<?=$countID;?>"><a class="expandDown"
			onclick="t('<?=$countID;?>', '<?=$countID . 'a';?>')"><?=$resource->title?></a>
		<a href="/resources/resource.php?id=<?=$resource->id?>"><img
			src="/resources/images/more.gif" /></a></div>
		</td>
		<td width="10%" align="center" valign="middle" class="paddingLeft"><img
			src="/resources/images/<?=$resource->type;?>.png"
			alt="<?=$resource->type;?>" title="<?=ucwords($resource->type);?>" /></td>
		<td width="10%" align="right"><?= $date ?></td>
		<td width="10%" align="center"><?=$this->get_languages($resource);?></td>
	</tr>
	<tr>
		<td colspan="4">
		<div class="invisible" id="<?=$countID . 'a';?>">
		<div class="item_contents"><?= $this->get_resource_summary($resource) ?>

		</div>
		</div>
		</td>
	</tr>
	<?
	$countID++;
}
?>
</table>
</div>
<?

$html = ob_get_contents();
ob_end_clean();
return $html;
}

function get_sort_icon(&$filter, $field) {
		if ($filter->initially_sorts_on($field)) return '<img src="/resources/images/down.png"/>';
}

/*
 * Return an HTML string containing the list of all
 * languages included in the given resource. The list
 * is a bunch of image tags pointing to flags for the
 * country that best corresponds to the language.
 */
function get_languages(&$resource) {
		$html = '';
		$separator = '';
		$languages = array();

		/*
		 * Handle the case where there are multiple links with the
		 * same language. Basically, build a set containing the
		 * known languages, and then display 'em.
		 */
		foreach($resource->links as $link) {
		  $languages[$link->language] = true;
		}

		foreach($languages as $language => $ignore) {
		  $html .= $separator."<img src=\"images/$language.gif\" alt=\"[$language]\"/>";
		  $separator = '&nbsp;';
		}
		return $html;
}

function get_resource_summary(& $resource) {
		$html = '<table border=\"0\"><tbody><tr><td valign="top">';
		$html .= $resource->description;
		$html .= '<p>';
		$html .= $this->get_resource_categories($resource);
		$html .= '</p>';
		$html .= $this->get_links($resource);
		$html .= '</td>';
//		$html .= $resource->image ? "<td valign=\"top\"><img width=\"100px\" align=\"right\" src=\"$resource->image\"/></td>" : '';
		$html .= '</tr></tbody></table>';
		return $html;
}

function get_resource_categories(&$resource) {
		$html = '<i>Categories:</i> ';
		$separator = '';
		$categories = $resource->categories;
		sort($categories);
		foreach($categories as $category) {
		  $html .= "$separator<a href=\"/resources?category=$category->title\">$category->title</a>";
		  $separator = ', ';
		}

		return $html;
}

function get_links(&$resource) {
		$html = '';
		if (count($resource->links) > 0) {
		  foreach ($resource->links as $link) {
		    $target = '';
		    $external = '';
		    if (!$this->is_local_target($link->path)) {
		      $target = "target=\"_blank\"";
		      $external = '<img align="top" src="/resources/images/external.gif" title="This links outside of eclipse.org."/>';
		    } else {
		      $external = '<img align="top" src="/resources/images/eclipse.gif" title="This is eclipse.org content."/>';
		    }

		    $html .= "<p style=\"margin-left:3em;text-indent:-2em;\"><a $target href=\"$link->path\">";

		    $type = $link->type;
		    if (!$type) $type = $resource->type;
		    if ($type) {
		      $html .= "<img style=\"vertical-align:text-top;\" alt=\"[$type]\" src=\"/resources/images/$type.png\"/> ";
		    }
		     
		    if ($link->title) $html .= $link->title;
		    else $html .= $resource->title;

		    $html .= '</a>';
		     
		    $html .= $external;

		    $html .= " <img src=\"/resources/images/$link->language.gif\"/>";

		    $html .= "<br/>";
		    $html .= date("F Y", $link->get_date());
		     
		    if ($link->authors) {
		      $html .= '<br/>';
		      Author::authors_to_html($link->authors, $html);
		    }
		     
		    $html .= '</p>';
		  }
		}
		return $html;
}

function is_local_target($path) {
		// It's not an external path if it's relative.
		if (strncmp($path, "http:", 5) != 0) return true;

		$host_path = 'http://' . $_SERVER['HTTP_HOST'];
		return strncmp($path, $host_path, strlen($host_path)) == 0;
}

function get_filter_form(& $filter) {

  return <<<EOHTML
			<ul>
				<li style="list-style-image: url('images/all.gif')"><a href=".">Everything</a></li>
				<li style="list-style-image: url('images/new.gif')"><a href="?recent=true">Recent additions</a></li>
				<li style="list-style-image: url('images/eclipse.gif')"><a href="?type=article">Eclipse Corner Articles</a></li>
				<li style="list-style-image: url('images/publication.png')"><a href="?type=publication">Other Articles</a></li>
				<li style="list-style-image: url('images/webinar.png')"><a href="?type=webinar">Webinars</a></li>
				<li style="list-style-image: url('images/podcast.png')"><a href="?type=podcast">Podcasts</a></li>
				<li style="list-style-image: url('images/book.png')"><a href="?type=book">Books</a></li>
				<li style="list-style-image: url('images/presentation.png')"><a href="?type=presentation">Presentations</a></li>
				<li style="list-style-image: url('images/demo.png')"><a href="?type=demo">Demonstrations</a></li>
				<li style="list-style-image: url('images/code.png')"><a href="?type=code">Code Samples</a></li>
				<li style="list-style-image: url('images/course.png')"><a href="?type=course">Courses</a></li>
			</ul>
EOHTML;
}

  function get_category_cloud($filter = null, $type = null, $minimum = 2) {
    if ($filter == null) {
      $filter = new Filter();
    } else {
      $filter = clone($filter);
    }

    $categories = $this->get_categories_with_resource_count($type);

    $category_names = array_keys($categories);

    if (count($categories) == 0) return;

    $max = max($categories);

    $html = '';
    $separator = '';
    foreach($category_names as $category) {
      $count = $categories[$category];
      if ($count < $minimum) continue;
      $size = round($count / $max * 4);

      // Determine a colour for the link.
      $red = round($count / $max * 255);
      $green = 0;
      $blue = 255 - round($count / $max * 255);

      $filter->category = $category;
      $href = $filter->get_url_parameters();
      $html .= "$separator<font size=\"$size\"><a style=\"color: rgb($red,$green,$blue)\" href=\"?$href\">$category</a></font>";
      $separator = ', ';
    }

    return $html;
  }

  function get_author_cloud(& $filter) {
    $authors = $this->get_authors_with_resource_count();

    $author_names = array_keys($authors);

    if (count($author_names) == 0) return;

    sort($author_names);

    $max = max($authors);

    $html = '';
    $separator = '';
    foreach($author_names as $author_name) {
      $count = $authors[$author_name];

      if ($count < 2) continue;
      $author_name = htmlentities($author_name);
      if (!$author_name) continue;

      $size = round($count / $max * 4);

      // Determine a colour for the link.
      $red = round($count / $max * 255);
      $green = 0;
      $blue = 255 - round($count / $max * 255);

      $html .= "$separator<font size=\"$size\"><a style=\"color: rgb($red,$green,$blue)\" href=\"?author=$author_name\">$author_name</a></font>";
      $separator = ', ';
    }

    return $html;
  }
	function get_time_passed_string($date) {
		$now = strtotime("now");
		$stringDate = date("I", $now);
		if ($stringDate == 1){
		$now -= 3600; }
		$difference = $now - $date; 
			
		switch ($difference) {
			case $difference > 31536000: // 60*60*24*365
				$ago = floor($difference / 31536000);
				if ($ago == 1)
				{
					return "+$ago&nbsp;year&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;years&nbsp;ago";
				}
			case $difference > 2419200: // 60*60*24*7*4
				$ago = floor($difference / 2419200);
				if ($ago == 1)
				{
					return "+$ago&nbsp;month&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;months&nbsp;ago";
				}
			case $difference > 604800:
				$ago = floor($difference / 604800);
				if ($ago ==1)
				{
					return "+$ago&nbsp;week&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;weeks&nbsp;ago";
				}
			case $difference > 86400:
				$ago = floor($difference / 84600);
				if ($ago == 1)
				{
					return "+$ago&nbsp;day&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;days&nbsp;ago";						
				}
			case $difference > 3600;
				$ago = floor($difference / 3600);
				if ($ago == 1)
				{
					return "+$ago&nbsp;hour&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;hours&nbsp;ago";						
				}
			case $difference > 0:
				$ago = floor($difference / 60);
				if ($ago == 1)
				{
					return "+$ago&nbsp;minute&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;minutes&nbsp;ago";						
				}
		}
	}
  /*
   * ==========================================================
   * Database functions.
   * ==========================================================
   */

  /*
   * This function returns a collection containing all the resources
   * that match the filter.
   */
  function get_resources($filter) {
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
      if ($resource_ids)
      $sql .= " and (resource.id in ($resource_ids))";
    }

    if ($filter->author) {
      $author = addslashes($filter->author);
      // The following line works with MySQL 4.5+
      // $sql .= " and (resource.id in (select resource.id from resource join link on (link.resource_id = resource.id) join link_author on (link.id = link_author.link_id) join author on (link_author.author_id = author.id) where author.name = '$author'))";
       
      $resource_ids = $this->get_resource_ids_for_author($author);
      if ($resource_ids)
      $sql .= " and (resource.id in ($resource_ids))";
       
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
      echo $sql;
      echo mysql_error();
      return null;
    }

    $resources_builder = new ResourcesBuilder();
    $resources_builder->build_resources($result);

    return $resources_builder->resources;
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
   * This function returns an array mapping category names to
   * the number of resources in the category.
   */
  function & get_categories_with_resource_count($type=null) {
    $sql = "select category.name, count(resource.id) " .
    "from category, resource_category, resource " .
    "where category.id = resource_category.category_id and resource_category.resource_id = resource.id";
     
    if ($type) $sql .= " and resource.type = '$type'";

    $sql .= " group by category.name order by upper(category.name)";

    $result = mysql_query($sql);
    $categories = array();
    while (($row = mysql_fetch_row($result)) != null) {
      $categories[$row[0]] = $row[1];
    }
    return $categories;
  }

  /*
   * This function returns an array mapping author names to
   * the number of resources authored.
   */
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

  /*
   * This function goes away when we upgrade to MySQL 4.5 or better.
   * It exists because MySQL 4.0.x doesn't support nested selects
   * (i.e. subqueries).
   */
  function get_resource_ids_for_author($author) {
    $sql = "select resource.id from resource join link on (link.resource_id = resource.id) join link_author on (link.id = link_author.link_id) join author on (link_author.author_id = author.id) where author.name = '$author'";
    $result = mysql_query($sql);
    $clause = '';
    $separator = '';
    while (($row = mysql_fetch_row($result)) != null) {
      //if (!$row[0]) continue;
      $clause.=$separator.$row[0];
      $separator =', ';
    }
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
    $clause = '';
    $separator = '';
    while (($row = mysql_fetch_row($result)) != null) {
      $clause.=$separator.$row[0];
      $separator =', ';
    }
    return $clause;
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