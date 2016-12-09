<?/**
 * Template Name: Sdela main page

 * Шаблон главной страницы (main-page.php)
 */
 ?>
 <?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php // get_template_part('template-part', 'topnav'); ?>
<?php
            global $dm_settings;?>
<div class="dmbs-mainpage">
	<div class="container dmbs-container">
		<div class="row dmbs-content">
			<div class="col-md-6 col-xs-12 mainpage_header">
				<h3><?php echo $dm_settings['mainpage_header'] ?></h3>
				<p><?php echo $dm_settings['mainpage_text'] ?></p>
				<a type="button" class="btn btn-danger"><?php echo $dm_settings['mainpage_buttontext'] ?><img src="<?php echo get_template_directory_uri();?>/img/button.png"/></a>
			</div>
		</div>
	</div>
</div>

<!-- start content container -->
<div class="container dmbs-container">
<div class="row dmbs-content">

    <?php //left sidebar ?>
    <?php get_sidebar( 'left' ); ?>

    <div class="col-md-<?php devdmbootstrap3_main_content_width(); ?> dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

            <!--<h2 class="page-header"><?php the_title() ;?></h2>-->
            
            <div class="row">
            <?php $args = array('numberposts' => 6, 'post_type' => 'ovg_mainpage');
        	$myposts = get_posts( $args );
        	
        	$i=1;
        	foreach( $myposts as $post ){ 
        		setup_postdata($post);
        	?>
				<div class="col-md-6 col-xs-12">
					<div class="main-page-img">
						<?php echo get_the_post_thumbnail();?>
					</div>
					<div class="main-page-header">
						<h3><?php echo get_the_title();?></h3>
					</div>
					<div class="main-page-text">
						<p><?php echo get_the_content();?></p>
					</div>
				</div>
			<?php
			if($i%2 == 0) {
				echo "</div><div class='row'>";
			}
			$i++;
			}
			?>
			</div>
        <?php endwhile; ?>
        <?php else: ?>

            <?php get_404_template(); ?>

        <?php endif; ?>

    </div>

    <?php //get the right sidebar ?>
    <?php get_sidebar( 'right' ); ?>

</div>
</div>
<!-- end content container -->

<?php get_footer(); ?>
