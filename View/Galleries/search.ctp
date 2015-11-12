<div class="container">
	<div class="row" id="sortableGallery">
			<?php echo $this->element('galleries', array('galleries', $galleries)); ?>
	</div>
	<div class="row">
	<?php
		echo $this->Paginator->counter(array(
			'format' => __('Page {:page} sur {:pages}, {:current} résultats sur un total de {:count}, de {:start} à {:end}')
		));
	?>
	</div>
			<nav>
		  <ul class="pagination">
			<?php echo $this->Paginator->numbers(array(
					'tag' => 'li',
					'separator' => '',
					'currentClass' => 'active',
					'first' => '«',
					'last' => '»',
					'currentTag' => 'a'
					
			)); ?>
		  </ul>
		</nav>
</div>