<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<div class="container dmbs-container">
<!-- start content container -->
<div class="row dmbs-content">

    <?php //left sidebar ?>
    <?php get_sidebar( 'left' ); ?>

    <div class="col-md-<?php devdmbootstrap3_main_content_width(); ?> dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			
			<?php
			$ztu_category = get_post_meta($post->ID, 'ztu_category', true);
			$ztu_subcategory = get_post_meta($post->ID, 'ztu_subcategory', true);
			
			$category = get_terms('ovg_ztu_categories', array('include' => array($ztu_category), 'hide_empty' => 0));
			$category = $category[0]->name;
			$subcategory = get_terms('ovg_ztu_categories', array('include' => array($ztu_subcategory), 'hide_empty' => 0));
			$subcategory = $subcategory[0]->name;
			
			$ztu_price = get_post_meta($post->ID, 'ztu_price', true);
			$ztu_price_type = get_post_meta($post->ID, 'ztu_price_type', true);
			$price_type = get_terms('ovg_ztu_price_type', array('include' => array($ztu_price_type), 'hide_empty' => 0));
			
			$ztu_type = get_post_meta($post->ID, 'ztu_type', true);
			if ($ztu_type == "Заказ") {
				$type = "type1";
			}
			else {
				$type = "type2";
			}
			
			$ztu_video = get_post_meta($post->ID, 'ztu_video', true);
			$ztu_photo = get_post_meta($post->ID, 'ztu_photo', true);
			
			$location = GeoMashupDB::get_object_location( 'ovg_ztu', $post->ID );
			?>
			
            <div class="row row-margin">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">Что надо сделать:</p>
            	</div>
            	<div class="col-md-8 col-sm-8 col-xs-12">
            		<p class="single-descr"><?php the_title();?></p>
            	</div>
            </div>
            
            <div class="row">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">Категория:</p>
            	</div>
            	<div class="col-md-8 col-sm-8 col-xs-12">
            		<p class="single-descr"><?php echo $category;?></p>
            	</div>
            </div>
            
            <div class="row">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">Подкатегория:</p>
            	</div>
            	<div class="col-md-8 col-sm-8 col-xs-12">
            		<p class="single-descr"><?php echo $subcategory;?></p>
            	</div>
            </div>
            
            <div class="row">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">Предложенная стоимость:</p>
            	</div>
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-descr"><?php echo $ztu_price."&nbsp;".$price_type[0]->name;?></p>
            	</div>
            	<div class="col-md-2 col-sm-2 col-xs-12"></div>
            	<div class="col-md-2 col-sm-2 col-xs-12 ztu<?php echo $type;?>">
            		<?php echo $ztu_type;?>
            	</div>
            </div>
			
			
			<div class="row row-margin">
				<div class="col-md-6 col-sm-6 col-xs-12 single-video">
					<?php if ($ztu_video) {
						echo $ztu_video;
						}
					else {
						echo "<img src='".get_stylesheet_directory_uri()."/img/novideo.jpg' />";
					}	
						?>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12 single-photo">
					<div class="row">
					<?php
					if($ztu_photo) {
						foreach ($ztu_photo as $photo) {
							$image_attributes = wp_get_attachment_image_src( $photo );
							$src = $image_attributes[0];
							echo "<div class='col-md-3 col-sm-3 col-xs-12'><img src='".$src."' /></div>";
							}
					}
					else {
							$src = get_stylesheet_directory_uri() . '/img/no-image.png';
							echo "<div class='col-md-3 col-sm-3 col-xs-12'><img src='".$src."' /></div>";
					}
					?>
					</div>
				</div>
			</div>
			
			<div class="row row-margin">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">Где:</p>
            	</div>
            	<div class="col-md-8 col-sm-8 col-xs-12">
            		<p class="single-descr"><?php echo $location;?></p>
            	</div>
            </div>
            
            <div class="row">
            	<div class="col-md-4 col-sm-4 col-xs-12">
            		<p class="single-title">От вас:</p>
            	</div>
            	<div class="col-md-8 col-sm-8 col-xs-12">
            		<p class="single-descr distance">12 км</p>
            	</div>
            </div>
			
			<div class="row row-margin">
				<div class="col-md-12">
					<?php echo GeoMashup::map('height=400&width=100%&zoom=15&add_map_type_control=false');?>
				</div>
			</div>
			
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    </div>

    <?php //get the right sidebar ?>
    <?php get_sidebar( 'right' ); ?>

</div>
<!-- end content container -->
</div>
<?php get_footer(); ?>
