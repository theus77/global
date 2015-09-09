<div class="container">
      <div class="row">
      	<h2>Demande de prix pour "<?php 
										if(isset($properties[$version['Version']['modelId']]['ObjectName'])){
											echo $properties[$version['Version']['modelId']]['ObjectName'];
										}
										else {
											echo $version['Version']['name'];
										}
									?>"</h2>
      </div>
      <div class="row">&nbsp;
      </div>
      <div class="row">
  		<div class="col-md-6">
  			<div>
	  		<form class="form-horizontal" id="target">
<fieldset>

<legend>Remplissez le formulaire suivant: </legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">Votre nom</label>  
  <div class="col-md-8">
  <input id="name" name="name" type="text" placeholder="Nom complet" class="form-control input-md" required="">
  <span class="help-block">Prénom et nom ou nom d'entreprise</span>  
  </div>
</div>

<!-- Prepended checkbox -->
<div class="form-group">
  <label class="col-md-4 control-label" for="company">Entreprise</label>
  <div class="col-md-8">
    <div class="input-group">
      <span class="input-group-addon">     
          <input type="checkbox">     
      </span>
      <input id="company" name="company" class="form-control" type="text" placeholder="TVA">
    </div>
    <p class="help-block">Numéro de TVA si besoin</p>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="address">Adresse</label>
  <div class="col-md-8">                     
    <textarea class="form-control" id="address" name="address"></textarea>
  </div>
</div>

<!-- Select Multiple -->
<div class="form-group">
  <label class="col-md-4 control-label" for="usage">Type d'usage</label>
  <div class="col-md-8">
    <select id="usage" name="usage" class="form-control" multiple="multiple">
      <option value="private">Particulier</option>
      <option value="internal">Communication interne</option>
      <option value="all-rigth">Droit d'exploitation commerciale</option>
    </select>
  </div>
</div>

<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="support">Livraison</label>
  <div class="col-md-8">
  <div class="checkbox">
    <label for="support-0">
      <input type="checkbox" name="support" id="support-0" value="file">
      Fichier numérique HD
    </label>
	</div>
  <div class="checkbox">
    <label for="support-1">
      <input type="checkbox" name="support" id="support-1" value="print">
      Impression
    </label>
	</div>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="print">Type d'impression</label>
  <div class="col-md-8">
    <select id="print" name="print" class="form-control">
      <option value="toile-40cm">Choisir un support au besoin</option>
      <option value="15-10">Toile 40cm</option>
      <option value="">Photo 15x10</option>
    </select>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="comment">Commentaires</label>
  <div class="col-md-8">                     
    <textarea class="form-control" id="comment" name="comment"></textarea>
  </div>
</div>

<!-- Button -->
<div class="form-group">

  <div class="col-md-12">
    <button id="send" name="send" class="btn btn-info">Demande de prix</button>
  </div>
</div>

</fieldset>
</form>
  			</div>
  		</div>
  		
  		<div class="col-md-6">
			<?php if(count($version['Keyword']) > 0) : ?>
  				<div class="">
				<h3>Mots clés de la série</h3>
				<ul id="keywordList">
					<?php foreach ($version['Keyword'] as &$keyword) :?>
						<?php echo $this->Html->tag('li', 
								$this->Html->link(
									$keyword['name'],
									array('action' => 'gallery', 'keywordId' => $keyword['modelId'])
								)												
							); ?>
					<?php endforeach; ?>
				</ul>
				</div>
			<?php endif; ?>
			<?php if(count($version['PlaceForVersion']) > 0) : ?>
				<div class="">
				<h3>Emplacements de la série</h3>
				<ul id="placesList">
					<?php foreach ($version['PlaceForVersion'] as &$place) :?>
						<?php echo $this->Html->tag('li', 
								$this->Html->link(
									$places[$place['placeId']],
									array('action' => 'gallery', 'placeId' => $place['placeId'])
								)												
							); ?>
					<?php endforeach; ?>
				</ul>
				</div>
			<?php endif; ?>
			<div class="">
				<h3>Détails de la série</h3>
				<ul id="detailList">
					<li>Photographe: <?php 
						if(isset($properties[$version['Version']['modelId']]['Byline'])){
							echo $properties[$version['Version']['modelId']]['Byline'];
						}
						else {
							echo __('Simon Schmitt');
						}
					?></li>
							<li>Date: <?php echo date("d M Y", $version['Version']['unixImageDate']); ?></li>
					<li>Dimensions: <?php 
						if(isset($properties[$version['Version']['modelId']]['PixelSize'])){
							echo $properties[$version['Version']['modelId']]['PixelSize'];
						}
						else {
							echo __('Non défini');
						}
					?></li>
				</ul>
			</div>

			<div>
				<h3>Photographies de la série</h3>
				<?php 
				foreach ($version['Stack'] as &$member){
					echo $this->Html->tag('div',
						$this->html->image('thumbnails/'.$member['Version']['encodedUuid'].'/thumbs.png', array('alt' => '')),
						array('class' => 'thumbnail col-xs-3 col-sm-2 col-md-4  col-lg-3 ')
					);
				}
				
				?>
			</div>
  		</div>
  	</div>
  </div>
