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

// The Author class represents individual authors of an article.
class Author {
	var $id;
	var $name;
	var $email;
	var $company;
	var $link;

	// Render the Author as html.
	function to_html(& $html) {

		// If an email address is provided, include a link.
		if ($this->email) {
			// Screw up the address a bit so that it can't be harvested
			$address = str_replace("@", "_*NOSPAM*_", $this->email);
			$html .= "<a href=\"mailto:$address\">";
		}

		// The author name must be provided.
		$html .= str_replace(' ', '&nbsp;', htmlentities($this->name));

		// If an email address is provided, then close off the link.
		if ($this->email) {
			$html .= "</a>";
		}

		if ($this->company) {
			$html .= ' (';
			
			// If a link is provided, display it.
			if ($this->link) {
				$html .= "<a href=\"$this->link\" target=\"_blank\">";
			}
		
			// If company information is provided, display it.
			if ($this->company) {
				$html .= trim(htmlentities($this->company));
			}
		
			// If a link is provided, close off the link.
			if ($this->link) {
				$html .= "</a>";
			}
			$html .= ')';
		}
	}
	
	// Render the authors to html.
	function authors_to_html($authors, & $html) {
		$count = count($authors);
		$more_than_two = $count > 2;
		$separator = 'by ';
		
		foreach ($authors as $author) {
			if (strlen(trim($author)) == 0) $author = "bal";
			$html .= $separator;
			if (--$count == 1) $separator = ($more_than_two ? ',' : '') .' and ';
			else $separator = ', ';
			$html .= $author->to_html($html);
		}
	}
}
	

?>