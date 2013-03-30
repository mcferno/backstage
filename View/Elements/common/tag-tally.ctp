<?php 
if(!empty($tag_tally)) :
	$this->start('sidebar-bottom');
	$action = (isset($action) ? $action : $this->request->params['action']);
?>
<ul class="nav nav-list tags-tally">
	<li class="nav-header">Tags</li>
	<?php
		$tag_id = false;
		if(isset($this->request->params['named']['tag'])) {
			$tag_id = $this->request->params['named']['tag'];
		}

		$tag_url = array(
			'action' => $action
		);
		if(!empty($this->request->params['pass'][0])) {
			$tag_url[] = $this->request->params['pass'][0];
		}

		foreach ($tag_tally as $tag) {
			$options = array();
			if($tag['Tag']['id'] == $tag_id) {
				$options['class'] = 'active';
			}
			$tag_url['tag'] = $tag['Tag']['id'];
			echo $this->Html->tag('li', $this->Html->link("{$tag['Tag']['name']} <span class=\"badge badge-inverse\">{$tag[0]['count']}</span>", $tag_url, array('escape' => false)), $options);
		}
	?>
</ul>

<?php 
	$this->end();
endif;