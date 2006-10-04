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
	
	function get_pillar_resources_table($pillar, $color=null) {
		$filter = new Filter();
		$filter->category = $pillar;
		$filter->sortby = array('date');
		
		return $this->get_resources_table($this->get_resources($filter), $filter, $color);
	}
	
	function get_resources($filter) {
		return $GLOBALS['Resources'] -> get_resources($filter);
	}
	
	function get_resources_table($resources, $filter) {
		ob_start();
		?>
		<table class="resourcesTableHeader" cellspacing="0" width="100%">
		<tr>
			<td colspan=4 class="tableHeaderTitle">Technical Resources</td>
		</tr>
		<tr>
			<td width="50%" class="resourcesHeader" style="border-left:1px solid black;" <a href="?<?=$filter->get_url_parameters('title')?>">Title<?=$this->get_sort_icon($filter, 'title')?></a></td>
			<td width="10%" class="resourcesHeader"><a href="?<?=$filter->get_url_parameters('type')?>">Type<?=$this->get_sort_icon($filter, 'type')?></a></td>
			<td width="10%" class="resourcesHeader" <a href="?<?=$filter->get_url_parameters('date')?>">Date<?=$this->get_sort_icon($filter, 'date')?></a></td>
			<td width="10%" class="resourcesHeader" style="border-right:1px solid black;" align="center">&nbsp;</td>
		</tr>
		</table>
		<div class="resources">
		<table class="resourcesTable" cellspacing="0">
		
		<?
		$countID = 0;
		foreach($resources as $resource) {
			//if (!$filter->show_resource($resource)) continue;
			$date = date("M d, Y", $resource->date);
			?>
			<tr class="resourcesData">
				
				<td width="50%">
					<div class="invisible" id="<?=$countID;?>">
						<a onclick="t('<?=$countID;?>', '<?=$countID . 'a';?>')"><?=$resource->title?></a> <a href="resource.php?id=<?=$resource->id?>"><img src="images/more.gif"/></a>
					</div>
				</td>
				<td width="10%" align="center" valign="middle" class="paddingLeft"><img src="/resources/images/<?=$resource->type;?>.png" alt="<?=$resource->type;?>" title="<?=ucwords($resource->type);?>"/></td>
				<td width="10%"><?
				//if ($resource->links[0]->date != 0)
				echo str_replace(" ", "&nbsp;", date("F Y", $resource->get_date()));
				?></td>
				<td width="10%" align="center"><?=$this->get_languages($resource);?></td>
			</tr>
			<tr>
				<td colspan="6">
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
		$html .= $resource->image ? "<td valign=\"top\"><img align=\"right\" src=\"$resource->image\"/></td>" : ''; 
		$html .= '</tr></tbody></table>';
		return $html;
	}
	
	function get_sort_icon($filter, $field) {
		if ($filter->initially_sorts_on($field)) return '<img src="images/down.png"/>';
	}
	
	function get_resource_categories($resource) {
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
	function get_languages($resource) {
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
	
	function get_links($resource) {
		$html = '';
		if (count($resource->links) > 0) {
			foreach ($resource->links as $link) {
				$html .= "<p style=\"margin-left:3em;text-indent:-2em;\"><a target=\"_blank\" href=\"$link->path\">";
		
				$type = $link->type;
				if (!$type) $type = $resource->type;
				if ($type) {
					$html .= "<img style=\"vertical-align:text-top;\" alt=\"[$type]\" src=\"images/$type.png\"/> ";
				}
					
				if ($link->title) $html .= $link->title;
				else $html .= $resource->title;
		
				$html .= '</a>';
			
				$html .= " <img src=\"images/$link->language.gif\"/>";
		
				$html .= "<br/>";
				$html .= date("F Y", $link->get_date());
			
				if ($link->authors) {
					$html .= '<br/>';
					Author::authors_to_html($link->authors, & $html);
				}
					
				$html .= '</p>';
			}
		}
		return $html;
	}
}
?>