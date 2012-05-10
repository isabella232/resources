/*******************************************************************************
 * Copyright (c) 2012 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Wayne Beaton (Eclipse Foundation)- initial API and implementation
 *******************************************************************************/
CREATE TABLE `resource` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(16) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text,
  `image_path` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=556 DEFAULT CHARSET=latin1;

CREATE TABLE `link` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `type` varchar(16) default NULL,
  `language` char(2) default NULL,
  `path` varchar(255) NOT NULL default '',
  `create_date` date NOT NULL default '0000-00-00',
  `resource_id` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=632 DEFAULT CHARSET=latin1;

CREATE TABLE `author` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `email` varchar(128) default NULL,
  `company` varchar(128) default NULL,
  `link` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=452 DEFAULT CHARSET=latin1;

CREATE TABLE `link_author` (
  `link_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=245 DEFAULT CHARSET=latin1;

CREATE TABLE `resource_category` (
  `resource_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

