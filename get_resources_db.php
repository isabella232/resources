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
	#    This file dumps the current contents of the resources database.
	#****************************************************************************
	
header('Content-type: text/plain');

require_once($_SERVER['DOCUMENT_ROOT'] .'/projects/classes/debug.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/app.class.php");	
$App = new App();

$connection = new DBConnection();
$connection->connect();

dump_table("resource");
dump_table("link");
dump_table("author");
dump_table("link_author");
dump_table("category");
dump_table("resource_category");

$connection->disconnect();

function dump_table($name) {
	global $App;
	$result = $App->eclipse_sql("select * from $name");
	while ($row = mysql_fetch_assoc($result)) {
		$columns = array_keys($row);
		
		$col_names = '';
		$col_values = '';
		$separator = '';
		foreach($columns as $column) {
			$value = mysql_real_escape_string($row[$column]);
			$col_names .= $separator . $column;
			$col_values .= $separator . "\"$value\"";
			$separator=',';
		}
		echo "insert into $name ($col_names) values ($col_values);\n";
	}
}