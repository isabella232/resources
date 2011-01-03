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

require_once($_SERVER['DOCUMENT_ROOT'] .'/projects/classes/debug.php');
trace_file_info(__FILE__);

class ResourceCategory {
	var $id;
	var $title;
	
	function ResourceCategory($id=null, $title=null) {
		$this->id = $id;
		$this->title = $title;
	}
}
?>