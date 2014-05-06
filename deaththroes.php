<?php  																														require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/nav.class.php"); 	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/menu.class.php"); 	$App 	= new App();	$Nav	= new Nav();	$Menu 	= new Menu();		include($App->getProjectCommon());    # All on the same line to unclutter the user's desktop'
/*******************************************************************************
 * Copyright (c) 2014 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Wayne Beaton (Eclipse Foundation)- initial API and implementation
 *******************************************************************************/

	//include("scripts/articles.php");
	require_once("../resources/scripts/resources.php");
	
	$resources = new Resources();
	$filter = new Filter();
	if (@$_GET['articles']) $filter->type = 'article';
	
	$resources = $resources->get_resources($filter);
	
	foreach($resources as $resource) {
		echo "==$resource->title==\n$resources->description\n";
		foreach($resource->links as $link) {
			$title = $link->title ? $link->title : $resource->title;
			$path = $link->path;
			if (preg_match('/^\//', $path)) path = "http://www.eclipse.org$path";
			echo "*[$link->path $link->title]\n";
		}
		echo "\n\n";
	}
