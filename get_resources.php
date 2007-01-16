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
	# get_resources.rss
	#
	# Author: 		Wayne Beaton
	# Date:			2006-09-21
	#
	# Description:
	#    This file implements a RESTful web service that provides information
	#    about resources, authors, and categories.
	#****************************************************************************
	
	header('Content-type: text/xml; charset=utf-8');
	$what = 'categories,authors,resources';
	if (array_key_exists('what', $_GET)) $what = $_GET['what'];
	
	$what = split(',', $what);
	
	require_once("scripts/resources_mgr.php");
?><? echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<library xmlns="http://www.eclipse.org/library" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.eclipse.org/library library.xsd ">

<? if (in_array('categories', $what)) { ?>
	<? $categories = $Resources->get_categories();?>
	<? foreach($categories as $category) { ?>
	<category id="<?=$category->id?>">
		<title><?=utf8_encode($category->title)?></title>
	</category>
	<? } ?>
<? } ?>


<? if (in_array('authors', $what)) { ?>
	<? $authors = $Resources->get_authors();?>
	<? foreach($authors as $author) { ?>
	<author id="<?=$author->id?>">
		<name><![CDATA[<?= utf8_encode($author->name) ?>]]></name>
		<email><![CDATA[<?= utf8_encode($author->email) ?>]]></email>
		<company><![CDATA[<?= utf8_encode($author->company) ?>]]></company>
		<url><![CDATA[<?= utf8_encode($author->link) ?>]]></url>
	</author>
	<? } ?>
<? } ?>


<? if (in_array('resources', $what)) { 
	require_once('scripts/filter_core.php'); 
	$filter = new Filter();
	$filter->populate_from_html_request_header();
	
	$resources = $Resources->get_resources($filter); ?>
	
	<? foreach($resources as $resource) { ?>
	<resource id="<?=$resource->id?>" type="<?=$resource->type?>">
		<name><![CDATA[<?= utf8_encode($resource->title) ?>]]></name>
		<description><![CDATA[<?= utf8_encode($resource->description)?>]]></description>
		<? foreach ($resource->categories as $category) { ?>
			<category id="<?=$category->id?>">
				<title><?=utf8_encode($category->title)?></title>
			</category>
		<? } ?>
		<? foreach ($resource->links as $link) { ?>
			<link id="<?= $link->id ?>" date="<?= date("M j, Y", $link->date)?>" language="<?=$link->language?>">
				<title><![CDATA[<?= $link->title ?>]]></title>
				<path><![CDATA[<?= $link->path ?>]]></path>
				<? foreach ($link->authors as $author) { ?>
					<author id="<?=$author->id?>">
						<name><![CDATA[<?= utf8_encode($author->name) ?>]]></name>
						<email><![CDATA[<?= utf8_encode($author->email) ?>]]></email>
						<company><![CDATA[<?= utf8_encode($author->company) ?>]]></company>
						<url><![CDATA[<?= utf8_encode($author->link) ?>]]></url>
					</author>
				<? } ?>
			</link>
		<? } ?>
	</resource>
	<? } ?>
<? } ?>
</library>
