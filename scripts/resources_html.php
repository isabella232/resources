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

require_once($_SERVER['DOCUMENT_ROOT']. "/resources/scripts/resources.php");

$Resources_HTML = new ResourcesHtml();

/*
 * @deprecated
 */
class ResourcesHtml {
  /*
   * @deprecated
   */
  function get_pillar_resources_table(&$pillar, $title="") {
    $resources = new Resources();
    $html = $resources->get_pillar_resources_table($pillar, $title);
    $resources->dispose();
    return $html;
  }

  /*
   * @deprecated
   */
  function get_recent_resources_summary($count) {
    $resources = new Resources();
    $html = $resources->get_recent_resources_summary($count);
    $resources->dispose();
    return $html;
  }
}

?>