<?php


#*****************************************************************************
#
# categories_core.php
#
# Author: 		Wayne Beaton
# Date:			2005-11-07
#
# Description: This file defines the Category class.
#
#****************************************************************************

class ResourceCategory {
	var $id;
	var $title;
	
	function ResourceCategory($id=null, $title=null) {
		$this->id = $id;
		$this->title = $title;
	}
}
?>