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
	# add_resource.php
	#
	# Author: 		Wayne Beaton
	# Date:			February 16, 2006
	#
	# Description:
	#    This file is used to create a new resource.
	#****************************************************************************

	#
	# Begin: page-specific settings.  Change these. 
	$pageTitle 		= "Add Eclipse Resource";
	$pageKeywords	= "";
	$pageAuthor		= "Wayne Beaton";
	
	# Add page-specific Nav bars here
	# Format is Link text, link URL (can be http://www.someothersite.com/), target (_self, _blank), level (1, 2 or 3)
	# $Nav->addNavSeparator("My Page Links", 	"downloads.php");
	# $Nav->addCustomNav("My Link", "mypage.php", "_self", 1);
	# $Nav->addCustomNav("Google", "http://www.google.com/", "_blank", 1);

	# End: page-specific settings
	#	
	
	require_once("scripts/resources_mgr.php");
	
	require_once("/home/data/httpd/eclipse-php-classes/people/ldapperson.class.php");
	require_once("/home/data/httpd/eclipse-php-classes/system/ldapconnection.class.php");
	
	$LDAPPerson = new LDAPPerson();
	$LDAPPerson = $LDAPPerson->redirectIfNotLoggedIn();
	
	$html = <<<EOHTML
	We're in.
EOHTML;

	$extraHtmlHeaders = "<link rel=\"alternate\" type=\"application/rss+xml\"title=\"Eclipse Resources\" href=\"/resources/resources.rss?$filter_string\">";
	$App->AddExtraHtmlHeader($extraHtmlHeaders);

	# Generate the web page
	$App->generatePage($theme, $Menu, $Nav, $pageAuthor, $pageKeywords, $pageTitle, $html);
	
	
?>