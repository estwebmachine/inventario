<div id="accordion">
	<?php
		//$menu es creado en AppController::beforeFilter
		$counter = 0;
		foreach($menu as $section => $subsections) {
			
			$current_link = array('controller' => $this->params['controller'], 'action' => $this->params['action']);
			$id = ( array_search($current_link, $subsections) !== false )? 'id="accordion-selected"' : '';
			echo '<div>';
			echo '<h3><a href="#" rel="' . $counter . '" ' . $id . '>' . $section . '</a></h3>';
			echo '<div class="submenu"><ul>';
			$last = end($subsections);
			foreach ($subsections as $title => $link) {
				$style = ($link == $last)? ' style="border-bottom: none;padding-bottom:0px;" ' : ' ';
                                if($current_link == $link){
                                    $class = ' class="current_submenu"';
                                }else{
                                    $class = ' ';
                                }
				echo '<li'.$class .$style. '>' . $html->link($title, $link) . '</li>';
                                
			}
			echo '</ul></div></div>';
			$counter++;
		}
	?>
</div>