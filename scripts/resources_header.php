<table class="resourcesTableHeader" cellspacing="0" width="100%">
<? if ($label != "") { ?>
	<tr>
		<td colspan=4 class="tableHeaderTitle"><?= $label ?></td>
	</tr>
	<? } ?>
	<tr>
		<td width="50%" class="resourcesHeader"
			style="border-left:1px solid black;"><a
			href="?<?=$filter->get_url_parameters('title')?>">Title<?=$this->get_sort_icon($filter, 'title')?></a></td>
		<td width="10%" class="resourcesHeader"><a
			href="?<?=$filter->get_url_parameters('type')?>">Type<?=$this->get_sort_icon($filter, 'type')?></a></td>
		<td width="10%" class="resourcesHeader"><a
			href="?<?=$filter->get_url_parameters('date')?>">Date<?=$this->get_sort_icon($filter, 'date')?></a></td>
		<td width="10%" class="resourcesHeader"
			style="border-right:1px solid black;" align="center">&nbsp;</td>
	</tr>
</table>
<div class="resources">
<table width="100%" class="resourcesTable" cellspacing="0">