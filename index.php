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
	# index.php
	#
	# Author: 		Wayne Beaton
	# Date:			February 16, 2006
	#
	# Description:
	#    This file generates the resources page. The contents of the page
	#    can be filtered using URL parameters (see ./scripts/filter_core.php
	#    for more information about the types of parameters.
	#****************************************************************************
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/nav.class.php"); 	
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/menu.class.php"); 	
$App = new App();
$Nav = new Nav();
$Menu = new Menu();
include($App->getProjectCommon()); 

require_once($_SERVER['DOCUMENT_ROOT'] .'/projects/classes/debug.php');
trace_file_info(__FILE__);
	#
	# Begin: page-specific settings.  Change these. 
	$pageTitle 		= "Eclipse Resources";
	$pageKeywords	= "Eclipse, software development, resources, projects, talks";
	$pageAuthor		= "Wayne Beaton";
	
	# Add page-specific Nav bars here
	# Format is Link text, link URL (can be http://www.someothersite.com/), target (_self, _blank), level (1, 2 or 3)
	# $Nav->addNavSeparator("My Page Links", 	"downloads.php");
	# $Nav->addCustomNav("My Link", "mypage.php", "_self", 1);
	# $Nav->addCustomNav("Google", "http://www.google.com/", "_blank", 1);

	# End: page-specific settings
	#	
	
	require_once("scripts/resources.php");
	
	$resources = new Resources();
	
	$filter = new Filter();	
	$filter->populate_from_html_request_header();
	
	$resources_list = $resources->get_resources($filter);
	$count = count($resources_list);
	
	$resources_table = $resources->get_resources_table2($resources_list, $filter);
	$filter_form = $resources->get_filter_form($filter);
	$category_cloud = $resources->get_category_cloud($filter);
	$authors_cloud = $resources->get_author_cloud($filter);
	
	$resources->dispose();
	
	$filter_summary = $filter->get_summary();
	
	if (!$filter->show_all()) $filter_summary .= " <a href=\"index.php\">Show all</a>.";
	
	ob_start();
?>	

	<div id="midcolumn">
		<?php 
			echo "<h1>$pageTitle</h1>";
			echo "<p>$filter_summary ($count resources) 
				<a href=\"resources.rss\"><img src=\"/images/rss2.gif\"/></a></p>";
			echo $resources_table;
			echo get_trace_html();
		?>
	</div>

	<div id="rightcolumn">
		<div class="sideitem">
		<a href="http://live.eclipse.org"><img src="http://live.eclipse.org/files/images/eclipse_live_logo_header.jpg"/></a><br/><font size="-2">For podcasts, webinars, and more...</font>
		
		</div>
		<div class="sideitem">
			<h6>Show what?</h6>
			<?php echo $filter_form ?>
		</div>
		<div class="sideitem">
			<h6>Categories</h6>
			<?php echo $category_cloud ?>
		</div>
		<div class="sideitem">
			<h6>Authors</h6>
			<?php echo $authors_cloud ?>
		</div>
		<?php include "parts/podcasts.php" ?>
	</div>
	
<?php
	$html = ob_get_contents();
	ob_end_clean();
	
	$extraHtmlHeaders = "<link rel=\"alternate\" type=\"application/rss+xml\"title=\"Eclipse Resources\" href=\"/resources/resources.rss\">";
	$App->AddExtraHtmlHeader($extraHtmlHeaders);
	$App->generatePage($theme, $Menu, $Nav, $pageAuthor, $pageKeywords, $pageTitle, $html);
?>