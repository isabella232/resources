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
	require_once("scripts/resources_mgr.php");
	$get = $_GET['get'];
?><? echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; ?>
<? if ($get=='categories') { ?>
	<? $categories = $Resources->get_categories();?>
	<categories>
		<? foreach($categories as $category) { ?>
		<category id="<?=$category->id?>">
			<name><?=utf8_encode($category->name)?></name>
		</category>
		<? } ?>
	</categories>
<? } else if ($get=='resources') {?>
	<? $resources = $Resources->get_resources();?>
	<resources>
		<? foreach($resources as $resource) { ?>
		<resource id="<?=$resource->id?>" type="<?=$resource->type?>">
			<name><![CDATA[<?= utf8_encode($resource->title) ?>]]></name>
			<description><![CDATA[<?= utf8_encode($resource->description)?>]]></description>
			<? foreach ($resource->categories as $category) { ?>
				<category id="<?= $category->id ?>"><?= utf8_encode($category->title) ?></category>
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
							<link><![CDATA[<?= utf8_encode($author->link) ?>]]></link>
						</author>
					<? } ?>
				</link>
			<? } ?>
		</resource>
		<? } ?>
	</resources>	
<? } else if ($get=='resource') {?>
	<? if ($id = $_GET['id']) { ?>
		<? if ($resource = $Resources->get_resource($id)) { ?>
		<resource id="<?=$resource->id?>" type="<?=$resource->type?>">
			<name><![CDATA[<?=$resource->title?>]]></name>
			<description><![CDATA[<?=$resource->description?>]]></description>
			<? foreach ($resource->categories as $category) { ?>
				<category id="<?= $category->id ?>"><?= $category->title ?></category>
			<? } ?>
			<? foreach ($resource->links as $link) { ?>
				<link id="<?= $link->id ?>" date="<?= date("M j, Y", $link->date)?>">
					<title><![CDATA[<?= $link->title ?>]]></title>
					<path><![CDATA[<?= $link->path ?>]]></path>
					<? foreach ($link->authors as $author) { ?>
						<author id="<?=$author->id?>">
							<name><![CDATA[<?= $author->name ?>]]></name>
							<email><![CDATA[<?= $author->email ?>]]></email>
							<company><![CDATA[<?= $author->company ?>]]></company>
							<link><![CDATA[<?= $author->link ?>]]></link>
						</author>
					<? } ?>
				</link>
			<? } ?>
		</resource>
		<? } else { ?>
			<error message="No resource with id=<?=$id?> found."/>
		<? } ?>
	<? } else { ?>
		<error message="You must specify an id for the resource to get."/>
	<? } ?>	
<? } else if ($get=='authors') {?>
	<? $authors = $Resources->get_authors();?>
	<resources>
		<? foreach($authors as $author) { ?>
		<author id="<?=$author->id?>">
			<name><![CDATA[<?= $author->name ?>]]></name>
			<email><![CDATA[<?= $author->email ?>]]></email>
			<company><![CDATA[<?= $author->company ?>]]></company>
			<link><![CDATA[<?= $author->link ?>]]></link>
		</author>
		<? } ?>
	</resources>	

<? } else { ?>
<error message="Please specify what you want to get (categories, authors, resources, resource)."/>
<? } ?>

