<?php

#*****************************************************************************
#
# filter_html.php
#
# Author: 		Wayne Beaton
# Date:			2006-07-19
#
# Description: This file contains functions that generate the filter form
# for display on a resources page.
#
#****************************************************************************

require_once('filter_core.php');

$Filters_HTML = new FilterHtml();

class FilterHtml {
function get_filter_form(& $filter) {	
	$recent = $filter->show_recent() ? 'checked' : false;
	$article = $filter->show_type('article') ? 'checked' : false;
	$presentation = $filter->show_type('presentation') ? 'checked' : false;
	$book = $filter->show_type('book') ? 'checked' : false;
	$demo = $filter->show_type('demo') ? 'checked' : false;
	$code = $filter->show_type('code') ? 'checked' : false;
	
	return <<<EOHTML
			<a href="index.php">Show all</a>
			<hr/>
			<form action="index.php" method="GET">				
				<input type="checkbox" name="recent" value="true" $recent>Recent only</input><br/>

				<hr/>
				<em>Types</em><br/>
				<p style=\"line-height:1;margin:0;margin-left:2em;text-indent:-2em;\"><input type="checkbox" name="type[0]" value="article" $article>Articles</input></p>
				<p style=\"line-height:1;margin:0;margin-left:2em;text-indent:-2em;\"><input type="checkbox" name="type[1]" value="presentation" $presentation>Presentations</input></p>
				<p style=\"line-height:1;margin:0;margin-left:2em;text-indent:-2em;\"><input type="checkbox" name="type[2]" value="book" $book>Books</input></p>
				<p style=\"line-height:1;margin:0;margin-left:2em;text-indent:-2em;\"><input type="checkbox" name="type[3]" value="demo" $demo>Demonstrations</input></p>
				<p style=\"line-height:1;margin:0;margin-left:2em;text-indent:-2em;\"><input type="checkbox" name="type[4]" value="code" $code>Code samples</input></p>

				<p align="center"><INPUT TYPE="reset"/>&nbsp;<INPUT TYPE="submit" VALUE="Submit"/></p>
			</form>
EOHTML;
}

function get_category_cloud(& $filter) {
	global $Resources;
	$categories = $Resources->get_categories_with_resource_count();
	
	$category_names = array_keys($categories);
	
	if (count($categories) == 0) return;
	
	$max = max($categories);
	
	$html = '';
	$separator = '';
	foreach($category_names as $category) {
		$count = $categories[$category];
		if ($count == 0) continue;
		$size = round($count / $max * 4);
		
		// Determine a colour for the link.
		$red = round($count / $max * 255);
		$green = 0; 
		$blue = 255 - round($count / $max * 255);
		
		$link = $filter->get_url_parameters(null, $category);
		$html .= "$separator<font size=\"$size\"><a style=\"color: rgb($red,$green,$blue)\" href=\"?$link\">$category</a></font>";
		$separator = ', ';
	}
		
	return $html;
}

function get_author_cloud(& $filter) {
	global $Resources;
	$authors = $Resources->get_authors_with_resource_count();
	
	$author_names = array_keys($authors);
	
	if (count($author_names) == 0) return;
	
	sort($author_names);
	
	$max = max($authors);
	
	$html = '';
	$separator = '';
	foreach($author_names as $author_name) {
		$count = $authors[$author_name];
		
		if ($count == 0) continue;
		$author_name = htmlentities($author_name);
		if (!$author_name) continue;
		
		$background = !$background;
		
		$size = round($count / $max * 4);
		
		// Determine a colour for the link.
		$red = round($count / $max * 255);
		$green = 0; 
		$blue = 255 - round($count / $max * 255);
		
		$link = $filter->get_url_parameters(null, null, $author_name);
	//	if ($background) $html .= "<span style=\"background: #fefacc\">";
		$html .= "$separator<font size=\"$size\"><a style=\"color: rgb($red,$green,$blue)\" href=\"?$link\">$author_name</a></font>";
		//if ($background) $html .= "</span>";
		$separator = ', ';
	}
		
	return $html;
}

}
?>