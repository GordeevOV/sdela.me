<?/**
 * Template Name: Sdela add ztu page

 * Шаблон страницы добавления ЗТУ (addztu-page.php)
 */
 ?>

<?php get_header(); ?>

<?php get_template_part('template-part', 'head'); ?>

<?php //get_template_part('template-part', 'topnav'); ?>


<div class="container dmbs-container">

<!-- start content container -->
<div class="row dmbs-content">

    <?php //left sidebar ?>
    <?php get_sidebar( 'left' ); ?>

    <div class="col-md-<?php devdmbootstrap3_main_content_width(); ?> dmbs-main">

        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

            <h2 class="page-header"><?php the_title() ;?></h2>
            
            
            
            <?php comments_template(); ?>

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
