<?php  /*******************************************************************************
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
	# template.php
	#
	# Author: 		Wayne Beaton
	# Date:			February 16, 2006
	#
	# Description:
	#    This file generates a page that displays information about a single
	#    resource. A single parameter, "id", indicating the id of the resource
	#    to display is required. If the parameter is not provided, or it does
	#    not reference a valid id (i.e. a resource with that id cannot be
	#    found), the requestor is redirected to ./index.php.
	#
	#****************************************************************************

require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/nav.class.php"); 	
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/menu.class.php"); 	
$App = new App();	
$Nav = new Nav();	
$Menu = new Menu();		
include($App->getProjectCommon());    

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
	
	/*
	 * Get the value of the 'id' parameter. If it is not
	 * a sequence of digits, bail out and redirect to the
	 * resources root page.
	 */
	$id = $App->getHTTPParameter('id');
	if (!preg_match('/^\d+$/', $id)) {
		header ("Location: /resources");
		exit;
	}
	
	require_once("scripts/resources.php");
	$resources = new Resources();
	$resource = $resources->get_resource($id);
	if (!$resource) {
		header ("Location: /resources");
		exit;
	}
	
	$pageTitle = $resource->title;
	$summary = $resources->get_resource_summary($resource);

	$resources->dispose();
	
	ob_start();
?>
<link rel="stylesheet" type="text/css" href="layout.css" media="screen" />
	
	<div id="midcolumn">
	
		<h1><?= $pageTitle ?></h1>

		<?= $summary ?>
		
	</div>

	<? if ($resource->type == 'podcast') { ?>
	<div id="rightcolumn">
		<? include "parts/podcasts.php" ?>
	</div>
	<? } ?>

<?php
	$html = ob_get_contents();
	ob_end_clean();
	
	$App->generatePage($theme, $Menu, $Nav, $pageAuthor, $pageKeywords, $pageTitle, $html);
?>