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
	# description.php
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

	require_once("scripts/resources.php");
	
	$resources = new Resources();
	$id = $_GET['id'];
	if ($id) $resource = $resources->get_resource($id);
	if (!$resource) {
		header ("Location: /resources");
		exit;
	}
	echo $resources->get_resource_summary($resource);

	$resources->dispose();
?>