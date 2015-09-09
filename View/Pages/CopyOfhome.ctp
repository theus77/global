    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img src="/img/%2BolEmwPvRcK1DfsR_oVyVw/teaser.png" alt="<?php echo __('Nos services');?>">
          <div class="container">
            <div class="carousel-caption">
              <?php echo $this->requestAction('wysiwygs/slug/teaser-services');  ?>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="/img/pwWLRztZSNa4CbiAKph63w/teaser.png" alt="Photothèque">
          <div class="container">
            <div class="carousel-caption">
            	<?php echo $this->requestAction('wysiwygs/slug/teaser-phototheque');  ?>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="/img/M1K0vXiVSMCrHZrVwW9byA/teaser.png" alt="Nos prochains vols">
          <div class="container">
            <div class="carousel-caption">
              <?php echo $this->requestAction('wysiwygs/slug/teaser-prochains-vols');  ?>
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div><!-- /.carousel -->


    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing" id="vol">
	    <div class="row">
	        <h2 class="col-lg-12 homepage"><?php echo __('Nos prochains vols'); ?></h2>
	    </div>
	    
      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="col-md-4">
          <img class="img-circle" src="http://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Province_of_Antwerp_%28Belgium%29_location.svg/577px-Province_of_Antwerp_%28Belgium%29_location.svg.png?uselang=fr" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h3>Province d'Anvers</h2>
          <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna.</p>
          <p><a class="btn btn-default btn-primary" href="#" role="button">À partir de 300€ &raquo;</a></p>
        </div><!-- /.col-md-4 -->
        <div class="col-md-4">
          <img class="img-circle" src="http://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Province_of_Hainaut_%28Belgium%29_location.svg/500px-Province_of_Hainaut_%28Belgium%29_location.svg.png" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h3>Province du Hainaut</h2>
          <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p>
          <p><a class="btn btn-default btn-primary" href="#" role="button">À partir de 400€ &raquo;</a></p>
        </div><!-- /.col-md-4 -->
        <div class="col-md-4">
          <img class="img-circle" src="http://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Province_of_Luxembourg_%28Belgium%29_location.svg/500px-Province_of_Luxembourg_%28Belgium%29_location.svg.png" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h3>Province du Luxembourg</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn btn-default btn-primary" href="#vol" role="button">À partir de 350€ &raquo;</a></p>
        </div><!-- /.col-md-4 -->
      </div><!-- /.row -->


      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div class="row">
     	<div class="col-sm-4 col-md-6 col-lg-8">
     		<h3><?php echo __('Galeries à découvrir'); ?></h3>
      	</div>
        <div class="col-sm-4 col-md-3 col-lg-2">
     		<h3><?php echo __('Facebook'); ?></h3>
     		<h3><?php echo __('Twitter'); ?></h3>
      	</div>
        <div class="col-sm-4 col-md-3 col-lg-2">
     		<h3><?php echo __('Newsletter'); ?></h3>
      	</div>
      </div>
      

      <hr class="featurette-divider">

      <!-- /END THE FEATURETTES -->

