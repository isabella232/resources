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

$podcast_url = "http://www.eclipse.org/resources/podcasts.rss";
$podcast_itunes_url = "itpc://www.eclipse.org/resources/podcasts.rss";

?>

<div class="sideitem">
	<h6><a href="podcasts.rss">Podcasts</a></h6>
	<p>Add Eclipse podcasts to <a href="<?= $podcast_itunes_url ?>">iTunes</a>, or
	to a web-based feed reader:</p>
	<p align="center"><a
		href="http://www.podnova.com/add.srf?url=<?= $podcast_url ?>"><img
		alt="Subscribe in podnova"
		src="http://www.podnova.com/img_chicklet_podnova.gif"></a><br />
	<a
	href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url=<?= $podcast_url ?>"><img
	alt="Subscribe in NewsGator Online"
	src="http://www.newsgator.com/images/ngsub1.gif"></a><br />
	<a href="http://www.netvibes.com/subscribe.php?url=<?= $podcast_url ?>"><img
	alt="Add to netvibes"
	src="http://www.netvibes.com/img/add2netvibes.gif"></a><br />
	<a href="http://add.my.yahoo.com/rss?url=<?= $podcast_url ?>"><img
	alt="addtomyyahoo4"
	src="http://us.i1.yimg.com/us.yimg.com/i/us/my/addtomyyahoo4.gif"
	height="17" width="91"></a><br />
	<a href="http://odeo.com/listen/subscribe?feed=<?= $podcast_url ?>"><img
	title="Subscribe to My Odeo Channel" alt="Subscribe to My Odeo Channel"
	src="http://odeo.com/img/badge-channel-black.gif" border="0"></a><br />
	<a href="http://fusion.google.com/add?feedurl=<?= $podcast_url ?>"><img
	alt="Add to Google"
	src="http://buttons.googlesyndication.com/fusion/add.gif" height="17"
	width="104"></a><br />
	<a
	href="http://www.pageflakes.com/subscribe.aspx?url=<?= $podcast_url ?>"><img
	src="http://www.pageflakes.com/subscribe2.gif" border="0"></a></p>
	<p>Or connect the <a href="podcasts.rss">feed</a> yourself.</p>
</div>
