<?php  																														require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/nav.class.php"); 	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/menu.class.php"); 	$App 	= new App();	$Nav	= new Nav();	$Menu 	= new Menu();		include($App->getProjectCommon());    # All on the same line to unclutter the user's desktop'
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
	
	require_once("scripts/resources_mgr.php");
	require_once("scripts/resources_html.php");
	require_once("scripts/filter_core.php");
	require_once("scripts/filter_html.php");
	
	$filter = new Filter();	
	$filter->populate_from_html_request_header();
	//$sort = $_GET['sort'];
	
	$resources_list = $Resources->get_resources($filter);
	$count = count($resources_list);
	
	$resources_table = $Resources_HTML->get_resources_table($resources_list, $filter);
	$filter_form = $Filters_HTML->get_filter_form($filter);
	$category_cloud = $Filters_HTML->get_category_cloud($filter);
	$authors_cloud = $Filters_HTML->get_author_cloud($filter);
	
	$filter_summary = $filter->get_summary();
	
	if (!$filter->show_all()) $filter_summary .= " <a href=\"index.php\">Show all</a>.";
	
	$podcast_url = "http://www.eclipse.org/resources/podcasts.rss";
	$podcast_itunes_url = "itpc://www.eclipse.org/resources/podcasts.rss";
	
	# Paste your HTML content between the EOHTML markers!	
	$html = <<<EOHTML
	<link rel="stylesheet" type="text/css" href="layout.css" media="screen" />
		<script language="javascript">
		function t(i, j) {
			var e = document.getElementById(i);
			var f = document.getElementById(j);
			var t = e.className;
			if (t.match('invisible')) { t = t.replace(/invisible/gi, 'visible'); }
			else { t = t.replace(/visible/gi, 'invisible'); }
			e.className = t;
			f.className = t;
		}
		</script>	
	<div id="midcolumn">
		<h1>$pageTitle</h1>
		<p>$filter_summary 
			$count resources.
			<a href="resources.rss"><img src="/images/rss2.gif"/></a></p>
		$resources_table
	</div>

	<div id="rightcolumn">
		<div class="sideitem">
			<h6>Show what?</h6>
			$filter_form
		</div>
		<div class="sideitem">
			<h6>Categories</h6>
			$category_cloud
		</div>
		<div class="sideitem">
			<h6>Authors</h6>
			$authors_cloud
		</div>
		<div class="sideitem">
			<h6><a href="podcasts.rss">Podcasts</a></h6>
			<p>Add Eclipse podcasts to <a href="$podcast_itunes_url">iTunes</a>, or to a
			web-based feed reader:</p>
			<p align="center">
				<a href="http://www.podnova.com/add.srf?url=$podcast_url"><img alt="Subscribe in podnova" src="http://www.podnova.com/img_chicklet_podnova.gif"></a><br/>
				<a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url=$podcast_url"><img alt="Subscribe in NewsGator Online" src="http://www.newsgator.com/images/ngsub1.gif"></a><br/>
				<a href="http://www.netvibes.com/subscribe.php?url=$podcast_url"><img alt="Add to netvibes" src="http://www.netvibes.com/img/add2netvibes.gif"></a><br/>
				<a href="http://add.my.yahoo.com/rss?url=$podcast_url"><img alt="addtomyyahoo4" src="http://us.i1.yimg.com/us.yimg.com/i/us/my/addtomyyahoo4.gif" height="17" width="91"></a><br/>
				<a href="http://odeo.com/listen/subscribe?feed=$podcast_url"><img title="Subscribe to My Odeo Channel" alt="Subscribe to My Odeo Channel" src="http://odeo.com/img/badge-channel-black.gif" border="0"></a><br/>
				<a href="http://fusion.google.com/add?feedurl=$podcast_url"><img alt="Add to Google" src="http://buttons.googlesyndication.com/fusion/add.gif" height="17" width="104"></a><br/>
				<a href="http://www.pageflakes.com/subscribe.aspx?url=$podcast_url"><img src="http://www.pageflakes.com/subscribe2.gif" border="0"></a>
			</p>
			<p>Or connect the <a href="podcasts.rss">feed</a> yourself.</p>
		</div>
	</div>
EOHTML;

	$extraHtmlHeaders = "<link rel=\"alternate\" type=\"application/rss+xml\"title=\"Eclipse Resources\" href=\"/resources/resources.rss\">";
	$App->AddExtraHtmlHeader($extraHtmlHeaders);

	# Generate the web page
	$App->generatePage($theme, $Menu, $Nav, $pageAuthor, $pageKeywords, $pageTitle, $html);
	
	
?>