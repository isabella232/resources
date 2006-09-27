<?php
	#*****************************************************************************
	#
	# get_resources.rss
	#
	# Author: 		Wayne Beaton
	# Date:			2006-09-21
	#
	# Description:
	#****************************************************************************
	header('Content-type: text/xml');
	require_once("scripts/resources_mgr.php");
	$get = $_GET['get'];
?>
<? if ($get=='categories') { ?>
	<? $categories = $Resources->get_categories();?>
	<categories>
		<? foreach($categories as $category) { ?>
		<category id="<?=$category->id?>">
			<name><![CDATA[<?=$category->name?>]]></name>
		</category>
		<? } ?>
	</categories>
<? } else if ($get=='resources') {?>
	<? $resources = $Resources->get_resources();?>
	<resources>
		<? foreach($resources as $resource) { ?>
		<resource id="<?=$resource->id?>" type="<?=$resource->type?>">
			<name><![CDATA[<?=$resource->title?>]]></name>
		</resource>
		<? } ?>
	</resources>	
<? } else if ($get=='resource') {?>
	<? if ($id = $_GET['id']) { ?>
		<? if ($resource = $Resources->get_resource($id)) { ?>
		<resource id="<?=$id?>"  type="<?=$resource->type?>" image="<?=$resource->image?>">
			<title><![CDATA[<?=$resource->title?>]]></title>
			<description><![CDATA[<?=$resource->description?>]]></description>
			<? foreach ($resource->categories as $category) { ?>
				<category name="<?= $category ?>"/>
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

