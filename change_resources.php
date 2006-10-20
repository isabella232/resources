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
	# change_resources.php
	#
	# Author: 		Wayne Beaton
	# Date:			2006-10-18
	#
	# Description:
	#    This file implements a RESTful web service that is used to update
	#    resources information.
	#****************************************************************************
	header('Content-type: text/xml; charset=utf-8');
	
	require_once("scripts/resources_mgr.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/eclipse.org-common/system/xml_sax_parsing.php");
	
	$user_id = $_SERVER['HTTP_USERID'];
	$password = $_SERVER['HTTP_PASSWORD'];

function error($where, $message) {
	//if (!$message) return;
	echo "<error message=\"$where: $message\"/>";
}

function comment($message) {
	echo "<comment>$message</comment>";
}

function validate_user($user_id, $password) {
	if (needs_login()) {
		error('validate_user', 'Invalid user credentials.');
		$ldap_import = '/home/data/httpd/eclipse-php-classes/people/ldapperson.class.php';
		if (file_exists($ldap_import)) {
			require_once($ldap_import);
			$LDAPPerson = new LDAPPerson();
			$LDAPPerson->setUid("SYSTEM");
			if ($LDAPPerson->signin($_uid, $_password))
				$LDAPPerson->signout();
				return true;
			}
		}
			
		return false;
	} else return true;
}

class ChangeFileHandler extends XmlFileHandler {
	function get_root_element_handler() {
		echo "<changes>";
		if (validate_user($user_id, $password)) {
			return new ChangeRootHandler($this->filter);
		} else {
			return new DoNothingHandler();
		}
	}
	
	function end_root_element_handler($handler) {
		echo "</changes>";
	}
}

class ChangeRootHandler extends XmlElementHandler {
	function & get_process_changes_handler($attributes) {
		return new ProcessChangesHandler($attributes);
	}
}

class ProcessChangesHandler extends XmlElementHandler {
	function & get_next($name, $attributes) {
		$class_name = str_replace("-", "", $name) . "handler";
		if (class_exists($class_name)) {
			return new $class_name($attributes);
		} else {
			echo "<error message=\"Unhandled command '$name'.\"/>";
			return new DoNothingHandler();
		}
	}
}

class AddResourceHandler extends XmlElementHandler {
	function AddResourceHandler($attributes) {
		mysql_query("insert into resource (type, title, description, image_path) values ('other', 'Title', 'Description', '')");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			$id = mysql_insert_id();
			echo "<resource-added id=\"$id\"/>";
		}
	}
}

class SetPropertyHandler extends SimpleTextHandler {
	var $id;
	function SetPropertyHandler($attributes) {
		$this->id = $attributes['ID'];
	}
	
	function end($name) {
		parent::end($name);
		$this->set_property($this->id, $this->text);
	}
}

class SetResourceDescriptionHandler extends SetPropertyHandler {	
	function set_property($id, $value) {
		mysql_query("update resource set description='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<resource-description-changed id=\"$id\"/>";
		}
	}
}

class SetResourceNameHandler extends SetPropertyHandler {
	function set_property($id, $value) {
		mysql_query("update resource set title='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<resource-name-changed id=\"$id\"/>";
		}
	}
}

class SetResourceTypeHandler extends SetPropertyHandler {
	function set_property($id, $value) {
		if (!in_array($value, array('article', 'book', 'demo', 'code', 'presentation', 'other'))) {
			echo "<error message=\"Type must be one of 'article', 'book', 'demo', 'code', 'presentation', or 'other'.\"/>";
			return;
		}
		mysql_query("update resource set type='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<resource-type-changed id=\"$id\"/>";
		}
	}
}

class SetResourceImageHandler extends SetPropertyHandler {
	function set_property($id, $value) {		
		mysql_query("update resource set image_path='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<resource-image-changed id=\"$id\"/>";
		}
	}
}

class SetLinkLanguageHandler extends SetPropertyHandler {
	function set_property($id, $value) {		
		mysql_query("update link set language='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<link-language-changed id=\"$id\"/>";
		}
	}
}

class SetLinkTitleHandler extends SetPropertyHandler {
	function set_property($id, $value) {		
		mysql_query("update link set title='$value' where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<link-title-changed id=\"$id\"/>";
		}
	}
}

class SetLinkDateHandler extends SetPropertyHandler {
	function set_property($id, $value) {
		$date = strtotime($value);
		echo "<comment>$value => $date</comment>";	
		mysql_query("update link set create_date=from_unixtime($date) where id=$id");
		$error = mysql_error();
		if ($error) {
			echo "<error message=\"$error\"/>";
		} else {
			echo "<link-date-changed id=\"$id\"/>";
		}
	}
}

class AddResourceLinkHandler extends XmlElementHandler {
	function AddResourceLinkHandler($attributes) {
		$resource_id = $attributes['ID'];
		$link_id = $attributes['LINK-ID'];
		
		if (!$resource_id) {
			echo "<error message=\"add-resource-link: The resource id must be provided.\"/>";
			return;
		}
		if ($link_id) {
			$query = "update link set resource_id=$resource_id where id=$link_id";
			//echo "<comment><![CDATA[$query]]></commment>";
			mysql_query($query);
			$error = mysql_error();
			if ($error) {
				echo "<error message=\"add-resource-link: $error\"/>";
			} else {
				echo "<link-updated resource_id=\"$resource_id\" link_id=\"$link_id\"/>";
			}
		} else {
			$date = date(strtotime('today'));
			mysql_query("insert into link (resource_id, type, title, language, path, create_date) values ($resource_id, '', '', 'en', '/path', from_unixtime($date))");
			$error = mysql_error();
			if ($error) {
				echo "<error message=\"add-resource-link: $error\"/>";
			} else {
				$id = mysql_insert_id();
				echo "<link-added id=\"$id\"/>";
			}
		}
	}
}

class RemoveResourceHandler extends XmlElementHandler {
	function RemoveResourceHandler($attributes) {
		$resource_id = $attributes['ID'];
		comment("Removing resource $resource_id");
		mysql_query("delete from resource where id=$resource_id");
		$error = mysql_error();
		if ($error) {
			error('remove-resource', $error);
		}
		comment("Removing links connected to resource $resource_id");
		mysql_query("delete from link where resource_id=$resource_id");
		$error = mysql_error();
		if ($error) {
			error('remove-resource', $error);
		}
		echo "<resource-removed id=\"$resource_id\"/>";
	}
}

class RemoveResourceLinkHandler extends XmlElementHandler {
	function RemoveResourceLinkHandler($attributes) {
		$link_id = $attributes['LINK-ID'];
		comment("Removing link $link_id");
		//mysql_query("delete from link where id=$link_id");
		// TODO Need to work out how to better handle the 'move' case.
		mysql_query("update link set resource_id=0 where id=$link_id");
		$error = mysql_error();
		if ($error) {
			error('remove-resource-link', $error);
		}
		echo "<resource-link-removed id=\"$link_id\"/>";
	}
}


$GLOBALS[handler] = & new ChangeFileHandler();
$GLOBALS[handler]->initialize();
$file = fopen('php://input','r');
$content = fread($file, $_SERVER['CONTENT_LENGTH']);
fclose($file); 

$parser = xml_parser_create();
xml_set_element_handler($parser, 'sax_start_handler', 'sax_end_handler');
xml_set_character_data_handler($parser, 'sax_data_handler');
xml_parse($parser, $content);

?>
