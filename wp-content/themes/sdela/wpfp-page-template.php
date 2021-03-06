<?php
    $wpfp_before = "";
    echo "<div class='wpfp-span'>";
    if (!empty($user)) {
        if (wpfp_is_user_favlist_public($user)) {
            $wpfp_before = "$user's Favorite Posts.";
        } else {
            $wpfp_before = "$user's list is not public.";
        }
    }

    if ($wpfp_before):
        echo '<div class="wpfp-page-before">'.$wpfp_before.'</div>';
    endif;

    if ($favorite_post_ids) {
		$favorite_post_ids = array_reverse($favorite_post_ids);
        $post_per_page = wpfp_get_option("post_per_page");
        $page = intval(get_query_var('paged'));

        $qry = array('post__in' => $favorite_post_ids, 'posts_per_page'=> $post_per_page, 'orderby' => 'post__in', 'paged' => $page);
        // custom post type support can easily be added with a line of code like below.
        $qry['post_type'] = array('post','page', 'ovg_ztu');
        query_posts($qry);
        
        //echo "<ul>";
        while ( have_posts() ) : the_post();
        ?>
<?php global $post;?>
<div class="post hentry ivycat-post row">
	<?php
	$ztu_type = get_post_meta($post->ID, 'ztu_type', true);
	if ($ztu_type == "Заказ") {
		$type = "type1";
	}
	else {
		$type = "type2";
	}
	
	$ztu_price = get_post_meta($post->ID, 'ztu_price', true);
	$ztu_price_type = get_post_meta($post->ID, 'ztu_price_type', true);
	$price_type = get_terms('ovg_ztu_price_type', array('include' => array($ztu_price_type), 'hide_empty' => 0));
	$ztu_photo = get_post_meta($post->ID, 'ztu_photo', true);
	if($ztu_photo) {
		$image_attributes = wp_get_attachment_image_src( $ztu_photo[0]);
		$src = $image_attributes[0];
	}
	else {
		$src = get_stylesheet_directory_uri() . '/img/no-image.png';
	}
	$ztu_category = get_post_meta($post->ID, 'ztu_category', true);
	$ztu_subcategory = get_post_meta($post->ID, 'ztu_subcategory', true);
	
	$category = get_terms('ovg_ztu_categories', array('include' => array($ztu_category), 'hide_empty' => 0));
	$category = $category[0]->name;
	$subcategory = get_terms('ovg_ztu_categories', array('include' => array($ztu_subcategory), 'hide_empty' => 0));
	$subcategory = $subcategory[0]->name;
	$location = GeoMashupDB::get_object_location( 'ovg_ztu', $post->ID );
	
	$author_id = $post->post_author;
	$author = get_userdata($author_id);
	
	$attachment_id = get_user_meta( $author_id, 'avatar_manager_custom_avatar', true );
	$custom_avatar = get_post_meta( $attachment_id, '_avatar_manager_custom_avatar', true );
	
	$options = avatar_manager_get_options();
	$size = $options['default_size'];
	$src_av = avatar_manager_generate_avatar_url( $attachment_id, $size );
	
	$ztu_begin = get_post_meta($post->ID, 'ztu_begin', true);
	?>
	<div class="col-md-12 list-ztu <?php echo $type;?>">
		<!-- This is the output of the post TITLE -->
		<div class="row">
			<div class="col-md-10">
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			</div>
			<div class="col-md-2 list-price">
				<?php echo $ztu_price."&nbsp;".$price_type[0]->name;?>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-3 col-sm-3 col-xs-12">
				<img  src="<?php echo $src?>" />
				<div class="row">
					<div class="col-md-4 list-count">
						№<?php echo $post->ID?>
					</div>
					<div class="col-md-4 list-count">
						Фото - <?php echo count($ztu_photo);?>
					</div>
				</div>
			</div>
			<div class="col-md-7 col-sm-7 col-xs-12">
				<div class="ztu<?php echo $type;?>">
					<?php echo $ztu_type;?>
				</div>
				<div class="ztu-cat">
					<?php echo $category.",&nbsp;".$subcategory;?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php //echo "<pre>".print_r($location->address)."</pre>";?>
					</div>
				</div>
				<div class="row user-details">
					<div class="col-md-2">
						<img class="img-circle avatar avatar-<?php echo $size;?> photo avatar-default" src="<?php echo $src_av;?>" />
					</div>
					<div class="col-md-10">
						<?php echo $author->last_name."&nbsp;".$author->first_name;?>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-12">
				<div class="row distance-wrap">
					<div class="col-md-12">
						<span class="fromyou">От вас</span>
						<span class="distance">&nbsp;&nbsp;7.6км</span>
					</div>	
				</div>
				<div class="row time-wrap">
					<div class="col-md-12">
						<span class="fromyou">Дата</span>
						<span class="time">&nbsp;&nbsp;<?php echo $ztu_begin;?></span>
					</div>	
				</div>
				<div class="row action-button">
					<div class="col-md-12">
						<div class="list-popup" id="list-popup-<?php echo $post->ID;?>">
							<?php
							$tmp_post = $post;
							$args = array(
							'numberposts' => -1,
							'post_type' => 'ovg_action',
							'orderby'     => 'title ',
							'order'       => 'DESC',
							);
							$actions = get_posts( $args );
							
							foreach ($actions as $action) {
								$icon = get_post_meta($action->ID, "actions_icons", true);
								if ($action->ID == 76) {
									echo "<div class='row'>
									<div class = 'col-md-12 list-popup-row'>
									<i class='fa fa-".$icon."'></i>&nbsp;".wpfp_link()."
									</div>
									</div>
								";
								}
								else {
									echo "<div class='row'>
									<div class = 'col-md-12 list-popup-row'>
									<i class='fa fa-".$icon."'></i>&nbsp;".$action->post_title."
									</div>
									</div>
								";
								}
								
							}
							
							$post = $tmp_post;
							?>
						</div>
						<span class="action" data-action="<?php echo $post->ID;?>">+</span>
					</div>
				</div>
			</div>
		</div>
		
		<!-- This is the output of the EXCERPT -->
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>

		
	</div>
</div>
<?php
        endwhile;
        //echo "</ul>";

        echo '<div class="navigation">';
            if(function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
            <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
            <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
            <?php }
        echo '</div>';

        wp_reset_query();
    } else {
        $wpfp_options = wpfp_get_options();
        echo "<ul><li>";
        echo $wpfp_options['favorites_empty'];
        echo "</li></ul>";
    }

    echo '<p>'.wpfp_clear_list_link().'</p>';
    echo "</div>";
    wpfp_cookie_warning();
    
?>

<script>
	jQuery(".action").click(function(){
		popupid = jQuery(this).data('action');
		//alert(popupid);
		jQuery('#list-popup-'+popupid).show();
	});
	
	function ActionPopupHide() {
		jQuery(".list-popup").hide();
	}
	
	jQuery(document).mouseup(function (e){ // событие клика по веб-документу
		//alert("!!!");
		var div = jQuery(".list-popup"); // тут указываем ID элемента
		//alert("!!!");
		if (!div.is(e.target) // если клик был не по нашему блоку
	    	&& div.has(e.target).length === 0) { // и не по его дочерним элементам
			ActionPopupHide(); // скрываем его
		}
	});
</script>
