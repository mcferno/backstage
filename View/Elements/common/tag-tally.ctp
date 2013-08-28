<?php
if(!empty($tag_tally)) :
	$this->append('sidebar-bottom');
	$action = (isset($action) ? $action : $this->request->params['action']);
?>
<ul class="nav nav-pills nav-stacked">
	<li class="nav-header">Tags</li>
	<?php
		$tag_id = false;
		if(isset($this->request->params['named']['tag'])) {
			$tag_id = $this->request->params['named']['tag'];
		}

		foreach ($tag_tally as $tag) {
			$options = array();
			if($tag['Tag']['id'] == $tag_id) {
				$link_name = "{$tag['Tag']['name']} <span class=\"badge badge-custom\">{$tag[0]['count']}</span>";
			} else {
				$link_name = "{$tag['Tag']['name']} <span class=\"badge badge-inverse\">{$tag[0]['count']}</span>";
			}

			
			if(empty($this->request->params['paging'])) {
				$link_tag = $this->Html->link($link_name, array('action' => 'index', 'tag' => $tag['Tag']['id']), array('escape' => false));
			} else {
				$link_tag = $this->Paginator->link($link_name, array('tag' => $tag['Tag']['id'], 'page' => false), array('escape' => false));
			}

			echo $this->Html->tag('li', $link_tag, $options);
		}
	?>
</ul>

<?php 
	$this->end();
endif;