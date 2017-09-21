<?php 
	delete_unused_records();
	include('./_head.php'); 
	echo "<div id='content'>";
		echo "<h1>" . $page->get('headline|title') . "</h1>";
		echo $page->body;
		renderNav($page->children);
	echo "</div>";
	
	echo "<div id='sidebar'>";
		if(count($page->images)) {
	
			// if the page has images on it, grab one of them randomly... 
			$image = $page->images->getRandom();
			
			// resize it to 400 pixels wide
			$image = $image->width(400);
			
			// output the image at the top of the sidebar...
			echo "<img src='$image->url' alt='$image->description' />";
		}
		// output sidebar text if the page has it
		echo $page->sidebar;
	echo "</div>";

	echo $config->servicetype;
	
	include('./_foot.php');
?>



