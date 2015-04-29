<div id="top_navogator">

    <?php
    
	foreach($menu as $section => $subsections) {
			
            $current_link = array('controller' => $this->params['controller'], 'action' => $this->params['action']);
	    foreach ($subsections as $title => $link) {
		if($current_link == $link){
                   echo '<p class="navigation_top"><b>'.$section.' >> '.$title.'</b></p>';
                  }                                
	     }		
	}

    ?>
</div>