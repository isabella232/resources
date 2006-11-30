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
# resources_html.php
#
# Author: 		Wayne Beaton
# Date:			2005-11-07
#
# Description: 
#    This file defines the ResourcesHtml class and a singleton instance of the
#    class, $Resources_HTML. This class includes methods that render
#    resource information as HTML.
#****************************************************************************

require_once("resources_mgr.php");
require_once("filter_core.php");

$Resources_HTML = new ResourcesHtml();

class ResourcesHtml {
	
	function get_pillar_resources_table(&$pillar, $color=null) {
		$filter = new Filter();
		$filter->category = $pillar;
		$filter->sortby = array('date');
		
		return $this->get_resources_table($this->get_resources($filter), $filter);
	}
		
	function get_resources(&$filter) {
		return $GLOBALS['Resources'] -> get_resources($filter);
	}
	
	function get_resources_table(&$resources, &$filter, $label="Technical Resources") {
		ob_start();
		?>
		<table class="resourcesTableHeader" cellspacing="0" width="100%">
		<tr>
			<td colspan=4 class="tableHeaderTitle"><?= $label ?></td>
		</tr>
		<tr>
			<td width="50%" class="resourcesHeader" style="border-left:1px solid black;" <a href="?<?=$filter->get_url_parameters('title')?>">Title<?=$this->get_sort_icon($filter, 'title')?></a></td>
			<td width="10%" class="resourcesHeader"><a href="?<?=$filter->get_url_parameters('type')?>">Type<?=$this->get_sort_icon($filter, 'type')?></a></td>
			<td width="10%" class="resourcesHeader" <a href="?<?=$filter->get_url_parameters('date')?>">Date<?=$this->get_sort_icon($filter, 'date')?></a></td>
			<td width="10%" class="resourcesHeader" style="border-right:1px solid black;" align="center">&nbsp;</td>
		</tr>
		</table>
		<div class="resources">
		<table width="100%" class="resourcesTable" cellspacing="0">
		
		<?
		$countID = 0;
		foreach($resources as $resource) {
			$date = date("M d, Y", $resource->get_date()); // . "<br/><font size=-2>".$this->get_time_passed_string($resource->get_date())."</font>";
			?>
			<tr class="resourcesData">
				
				<td width="50%">
					<div class="invisible" id="<?=$countID;?>">
						<a class="expandDown" onclick="t('<?=$countID;?>', '<?=$countID . 'a';?>')"><?=$resource->title?></a> <a href="/resources/resource.php?id=<?=$resource->id?>"><img src="/resources/images/more.gif"/></a>
					</div>
				</td>
				<td width="10%" align="center" valign="middle" class="paddingLeft"><img src="/resources/images/<?=$resource->type;?>.png" alt="<?=$resource->type;?>" title="<?=ucwords($resource->type);?>"/></td>
				<td width="10%" align="right"><?= $date ?></td>
				<td width="10%" align="center"><?=$this->get_languages($resource);?></td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="invisible" id="<?=$countID . 'a';?>">
					<div class="item_contents">
						<?= $this->get_resource_summary($resource) ?>
						
					</div> 
					</div>
				</td>
			</tr>
			<?
			$countID++;
		}
		?>
		</table>
		</div>
		<?

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function get_resource_summary(& $resource) {
		$html = '<table border=\"0\"><tbody><tr><td valign="top">';
		$html .= htmlentities($resource->description);
		$html .= '<p>';
		$html .= $this->get_resource_categories($resource);
		$html .= '</p>';
		$html .= $this->get_links($resource);
		$html .= '</td>';
		$html .= $resource->image ? "<td valign=\"top\"><img width=\"100px\" align=\"right\" src=\"$resource->image\"/></td>" : ''; 
		$html .= '</tr></tbody></table>';
		return $html;
	}
	
	function get_sort_icon(&$filter, $field) {
		if ($filter->initially_sorts_on($field)) return '<img src="images/down.png"/>';
	}
	
	function get_resource_categories(&$resource) {
		$html = '<i>Categories:</i> ';
		$separator = '';
		$categories = $resource->categories;
		sort($categories);
		foreach($categories as $category) {
			$html .= "$separator<a href=\"/resources?category=$category->title\">$category->title</a>";
			$separator = ', ';
		}
		
		return $html;
	}
	
	/*
	 * Return an HTML string containing the list of all
	 * languages included in the given resource. The list
	 * is a bunch of image tags pointing to flags for the
	 * country that best corresponds to the language.
	 */
	function get_languages(&$resource) {
		$html = '';
		$separator = '';
		$languages = array();
		
		/*
		 * Handle the case where there are multiple links with the
		 * same language. Basically, build a set containing the
		 * known languages, and then display 'em.
		 */
		foreach($resource->links as $link) {
			$languages[$link->language] = true;
		}
		
		foreach($languages as $language => $ignore) {
			$html .= "$separator<img src=\"/resources/images/$language.gif\">";
			$separator = '&nbsp;';
		}
		return $html;
	}
	
	function get_links(&$resource) {
		$html = '';
		if (count($resource->links) > 0) {
			foreach ($resource->links as $link) {
				$html .= "<p style=\"margin-left:3em;text-indent:-2em;\"><a target=\"_blank\" href=\"$link->path\">";
		
				$type = $link->type;
				if (!$type) $type = $resource->type;
				if ($type) {
					$html .= "<img style=\"vertical-align:text-top;\" alt=\"[$type]\" src=\"/resources/images/$type.png\"/> ";
				}
					
				if ($link->title) $html .= $link->title;
				else $html .= $resource->title;
		
				$html .= '</a>';
			
				$html .= " <img src=\"/resources/images/$link->language.gif\"/>";
		
				$html .= "<br/>";
				$html .= date("F Y", $link->get_date());
			
				if ($link->authors) {
					$html .= '<br/>';
					Author::authors_to_html($link->authors, $html);
				}
					
				$html .= '</p>';
			}
		}
		return $html;
	}
	
	function get_recent_resources_summary($maximum) {
		$result = mysql_query("select resource.id, resource.title, max(link.create_date) as link_date from resource, link where resource.id = link.resource_id group by resource.id order by link_date desc");
		if ($error = mysql_error()) {
			return $error;
			return null;
		}
		
		$html = '<ul>';
		
		while ($row = mysql_fetch_array($result)) {
			$id = $row[0];
			$title = $row[1];
			$date = $row[2];
			$date = strtotime($date);
			$ago = $this->get_time_passed_string($date);
			$html .= "<li><a href=\"/resources/resource.php?id=$id\">$title</a> <span class=\"posted\">$ago</span></li>";
			
			if (--$maximum == 0) break;
		}
		
		$html .= '</ul>';
		return $html;
	}
	
	function get_time_passed_string($date) {
		$now = strtotime("now");
		$stringDate = date("I", $now);
		if ($stringDate == 1){
		$now -= 3600; }
		$difference = $now - $date; 
			
		switch ($difference) {
			case $difference > 31536000: // 60*60*24*365
				$ago = floor($difference / 31536000);
				if ($ago == 1)
				{
					return "+$ago&nbsp;year&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;years&nbsp;ago";
				}
			case $difference > 2419200: // 60*60*24*7*4
				$ago = floor($difference / 2419200);
				if ($ago == 1)
				{
					return "+$ago&nbsp;month&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;months&nbsp;ago";
				}
			case $difference > 604800:
				$ago = floor($difference / 604800);
				if ($ago ==1)
				{
					return "+$ago&nbsp;week&nbsp;ago";						
				}
				else 
				{
					return "+$ago&nbsp;weeks&nbsp;ago";
				}
			case $difference > 86400:
				$ago = floor($difference / 84600);
				if ($ago == 1)
				{
					return "+$ago&nbsp;day&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;days&nbsp;ago";						
				}
			case $difference > 3600;
				$ago = floor($difference / 3600);
				if ($ago == 1)
				{
					return "+$ago&nbsp;hour&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;hours&nbsp;ago";						
				}
			case $difference > 0:
				$ago = floor($difference / 60);
				if ($ago == 1)
				{
					return "+$ago&nbsp;minute&nbsp;ago";						
				}
				else
				{
					return "+$ago&nbsp;minutes&nbsp;ago";						
				}
		}
	}
}
?>