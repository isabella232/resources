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
	#    This file dumps the current contents of the resources table.
	#****************************************************************************
	

require_once ($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/smartconnection.class.php");

$connection = new DBConnection();
$connection->connect();

dump_table("author");

$connection->disconnect();

function dump_table($name) {
	$result = mysql_query("select * from $name");
	while ($row = mysql_fetch_assoc($result)) {
		$columns = array_keys($row);
		
		$col_names = '';
		$col_values = '';
		$separator = '';
		foreach($columns as $column) {
			$col_names .= $separator . $column;
			$col_values .= $separator . "\"$row[$column]\"";
			$separator=',';
		}
		echo "insert into $name ($col_names) values ($col_values);";
	}
}