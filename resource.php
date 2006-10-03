<?php  																														require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/nav.class.php"); 	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/menu.class.php"); 	$App 	= new App();	$Nav	= new Nav();	$Menu 	= new Menu();		include($App->getProjectCommon());    # All on the same line to unclutter the user's desktop'

	#*****************************************************************************
	#
	# template.php
	#
	# Author: 		Wayne Beaton
	# Date:			February 16, 2006
	#
	# Description:
	#
	#
	#****************************************************************************

	#
	# Begin: page-specific settings.  Change these. 
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
	
	$id = $_GET['id'];
	if ($id) $resource = $Resources->get_resource($id);
	if (!$resource) {
		header ("Location: /resources");
		exit;
	}
	
	$pageTitle 		= "Eclipse Resource: $resource->title";
	$summary = $Resources_HTML->get_resource_summary($resource);
	
	# Paste your HTML content between the EOHTML markers!	
	$html = <<<EOHTML
	<link rel="stylesheet" type="text/css" href="layout.css" media="screen" />
	
	<div id="midcolumn">
	
		<h1>$pageTitle</h1>

		$summary
		
	</div>


EOHTML;

	# Generate the web page
	$App->generatePage($theme, $Menu, $Nav, $pageAuthor, $pageKeywords, $pageTitle, $html);
	
	
?>