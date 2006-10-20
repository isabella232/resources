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
?><? echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<eclipse-resources>
<? $categories = $Resources->get_categories();?>
	<categories>
		<? foreach($categories as $category) { ?>
		<category id="<?=$category->id?>">
			<name><?=utf8_encode($category->name)?></name>
		</category>
		<? } ?>
	</categories>
<? $authors = $Resources->get_authors();?>
	<authors>
		<? foreach($authors as $author) { ?>
		<author id="<?=$author->id?>">
			<name><![CDATA[<?= utf8_encode($author->name) ?>]]></name>
			<email><![CDATA[<?= utf8_encode($author->email) ?>]]></email>
			<company><![CDATA[<?= utf8_encode($author->company) ?>]]></company>
			<link><![CDATA[<?= utf8_encode($author->link) ?>]]></link>
		</author>
		<? } ?>
	</authors>	
<? $resources = $Resources->get_resources();?>
	<resources>
		<? foreach($resources as $resource) { ?>
		<resource id="<?=$resource->id?>" type="<?=$resource->type?>">
			<name><![CDATA[<?= utf8_encode($resource->title) ?>]]></name>
			<description><![CDATA[<?= utf8_encode($resource->description)?>]]></description>
			<? foreach ($resource->categories as $category) { ?>
				<category id="<?= $category->id ?>"/>
			<? } ?>
			<? foreach ($resource->links as $link) { ?>
				<link id="<?= $link->id ?>" date="<?= date("M j, Y", $link->date)?>" language="<?=$link->language?>">
					<title><![CDATA[<?= $link->title ?>]]></title>
					<path><![CDATA[<?= $link->path ?>]]></path>
					<? foreach ($link->authors as $author) { ?>
						<author id="<?=$author->id?>"/>
					<? } ?>
				</link>
			<? } ?>
		</resource>
		<? } ?>
	</resources>	
</eclipse-resources>
