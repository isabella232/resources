<?php

	# Set the theme for your project's web pages.
	# See the Committer Tools "How Do I" for list of themes
	# https://dev.eclipse.org/committers/ 
	$theme = "Phoenix";

	# Define your project-wide Nav bars here.
	# Format is Link text, link URL (can be http://www.someothersite.com/), target (_self, _blank), level (1, 2 or 3)
	$Nav->addNavSeparator("Resources", "index.php");
	$Nav->addCustomNav("Documentation", "http://www.eclipse.org/documentation/", "_self", 1);
	$Nav->addCustomNav("Articles", "/resources/index.php?type=article", "_self", 1);
	$Nav->addCustomNav("Presentations", "/resources/index.php?type=presentation", "_self", 1);
	$Nav->addCustomNav("Books", "/resources/index.php?type=book", "_self", 1);
	$Nav->addCustomNav("Demonstrations", "/resources/index.php?type=demo", "_self", 1);
	$Nav->addCustomNav("Code samples", "/resources/index.php?type=code", "_self", 1);
	$Nav->addCustomNav("Services", "/services", "_self", 1);

?>
