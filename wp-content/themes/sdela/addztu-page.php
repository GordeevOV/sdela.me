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


    <div class="col-md-<?php devdmbootstrap3_main_content_width(); ?> dmbs-main">
		<form  method="post" action="<?php bloginfo('template_url'); ?>/add_post_handler.php" class="add-form form-horizontal">
			<div class="row">
				<div class="col-md-8 col-sm-8 col-xs-6"></div>
				<div class="col-md-4 col-sm-4 col-xs-6 zakaz">
					
					
					<?php 
					$types = get_terms('ovg_ztu_type', array('orderby' => 'name', 'fields' => 'names', 'hide_empty' => 0));
						//print_r($types);
					$i = 0;
					foreach($types as $type):
					?>
					<div class="type-ztu" id="type<?php echo $i;?>">	
					<input type="radio" name="inputType" value="<?php echo $type?>" <?php if($ztu_type == $type) echo 'checked'; ?>><?php echo $type?>
					</div>
							
					<?php 
					$i++;
					endforeach;?>
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-8 col-xs-12">
					<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Что надо сделать</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control" id="inputTitle" name="inputTitle">
					    </div>
				  	</div>
				</div>
				<div class="col-md-6 col-sm-4 col-xs-12">
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-8 col-xs-12">
				  	<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Категория</label>
					    <div class="col-sm-6">
					      <select class="form-control" id="inputCat" name="inputCat">
					      <option value="0">Добавить категорию</option>
					      <?php 
						$categories = get_terms('ovg_ztu_categories', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0, 'parent' => 0));
						//print_r($cities);
						foreach($categories as $category_id=>$category_name):
					?>
					
						<option value="<?php echo $category_id?>" <?php if($ztu_category == $category_id) echo 'selected'; ?>><?php echo $category_name?></option>
						
						
					<?php endforeach;?>
							</select>
					    </div>
				  	</div>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<input type="text" class="form-control" id="inputNewCategory" name="inputNewCategory" placeholder="Новая категория">
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-8 col-xs-12">
				  	<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Подкатегория</label>
					    <div class="col-md-6 col-sm-6 col-xs-12">
					      <select class="form-control" id="inputSubCat" name="inputSubCat">
					      <option value="0">Добавить подкатегорию</option>
					      <?php
					
								if ($ztu_category) {
								$subcategories = get_terms('ovg_ztu_categories', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0, 'parent' => $ztu_category));
								//print_r($cities);
								foreach($subcategories as $subcategory_id=>$subcategory_name):
							?>
							
								<option value="<?php echo $subcategory_id?>" <?php if($ztu_subcategory == $subcategory_id) echo 'selected'; ?>><?php echo $subcategory_name?></option>
								
								
							<?php endforeach;
							}
							?>
							</select>
					    </div>
				  	</div>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<input type="text" class="form-control" id="inputNewSubCategory" name="inputNewSubCategory" placeholder="Новая подкатегория">
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-8 col-xs-12">
				  	<div class="form-group">
					    <label for="inputPrice" class="col-sm-6 control-label">Стоимость работы</label>
					    <div class="col-sm-6">
					    	<div class="col-sm-6 inputPrice">
					      		<input type="text" class="form-control" id="inputPrice" name="inputPrice">
					      	</div>
					      	<div class="col-sm-6 inputPriceType">
					      		<select class="form-control" id="inputPriceType" name="inputPriceType">
							<?php 
								$pricetypes = get_terms('ovg_ztu_price_type', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0, 'parent' => 0));
								//print_r($cities);
								foreach($pricetypes as $pricetype_id=>$pricetype_name):
							?>
							
								<option value="<?php echo $pricetype_id?>" <?php if($ztu_pricetype == $pricetype_id) echo 'selected'; ?>><?php echo $pricetype_name?></option>
								
								
							<?php endforeach;?>
							
							</select>
							</div>
					    </div>
				  	</div>
				</div>
				
			</div>
			
			<div class="row add-images">
				<div class="col-md-3 col-sm-4 col-xs-12">
					<label for="inputImage" class="col-sm-6 control-label">Изображения</label>
					<button type="submit" class="add_image_button button btn btn-default" id="inputImage">Добавить изображения</button>							</div>
				<div class="col-md-9 col-sm-8 col-xs-12">
					<?php
					$w=126;
					$h=90;
					$default = get_stylesheet_directory_uri() . '/img/no-image.png';
					if( $photo ) {
						$image_attributes = wp_get_attachment_image_src( $photo, array($w, $h) );
						$src = $image_attributes[0];
					} else {
						$src = $default;
					}
					?>
					<input type="hidden" id="default_img" value="<?php echo $default?>" />
					<div class="row image-wrapper">
						<div class="col-md-3 col-sm-4 col-xs-6">
							<img data-src="<?php echo $default?>" src="<?php echo $src?>" width="<?php echo $w?>px" />
							<div>
								<input type="hidden" name="ztumetabox_number[0]" class="ztumetabox_number" value="0" />
								<input type="hidden" name="ztumetabox_photo[0]" id="ztumetabox_photo[0]" value="" />
								<button type="submit" class="upload_image_button button btn btn-default image-buttons">Загрузить</button>
								<button type="submit" class="remove_image_button button btn btn-default image-buttons">&times;</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row add-video">
				<div class="col-md-12">
					<div class="form-group">
					    <label for="inputVideo" class="col-sm-3 control-label">Ссылка на видео</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" id="inputVideo" name="inputVideo">
					    </div>
				  	</div>
				</div>
			</div>
			
			<div class="row add-place">
				<div class="col-md-6 col-sm-8 col-xs-12">
					<div class="form-group">
					    <label for="inputPlace" class="col-sm-6 control-label">Где надо сделать</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control" id="inputPlace" name="inputPlace">
					    </div>
				  	</div>
				</div>
				<div class="col-md-6 col-sm-8 col-xs-12">
				</div>
				<div class="col-md-12 ztu-map">
					<div class="form-group">
					<?php echo GeoMashupPostUIManager::get_instance()->print_form('enable_scroll_wheel_zoom=true'); ?>
					</div>
				</div>
			</div>
			
			<div class="row add-details">
				<div class="col-md-12">
					<div class="form-group">
					    <label for="inputTime" class="col-sm-3 control-label">Когда надо сделать</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" id="inputTime" name="inputTime">
					    </div>
				  	</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
					    <label for="inputDescr" class="col-sm-3 control-label">Детали задания</label>
					    <div class="col-sm-9">
					      <textarea class="form-control" rows="7" id="inputDescr" name="inputDescr">
					      </textarea>
					    </div>
				  	</div>
				</div>
			</div>
			
			<div class="row add-submit">
				<div class="col-sm-2 col-xs-12">
					<button type="submit" class="ztu-submit btn btn-success" id="send" name="send">Отправить</button>
				</div>
			</div>
			
		</form>
    </div>

    <?php //get the right sidebar ?>
    <?php get_sidebar( 'right' ); ?>

</div>
<!-- end content container -->

</div>

<script>
	jQuery("#inputCat").change(function(){
		parent_id = jQuery("#inputCat").val();
		
		if (parent_id == 0) {
			jQuery("#inputNewCategory").show();
			jQuery("#inputNewSubCategory").show();
		}
		else {
			jQuery("#inputNewCategory").hide();
		}
		
		//alert(parent_id);
		jQuery.ajax({
		type: "POST",
        url: '<?php echo admin_url( 'admin-ajax.php' );?>',
		data: {
            action:'ovg_load_subcategories',
            parent_id: parent_id,
            },
        dataType:'json',
		success: function (data) {
			//alert(data);
			jQuery("#inputSubCat").empty();
			jQuery("#inputSubCat").append(jQuery("<option value='0'>Добавить подкатегорию</option>"));
			if (parent_id !=0) {
				jQuery.each(data, function(key, value) {
					jQuery("#inputSubCat").append(jQuery("<option value='" + key + "'>" + value + "</option>"));
				});
			}
		},
		error : function(s , i , error){
			console.log(error);
		}
	});
	});
	
	jQuery("#inputSubCat").change(function(){
		parent_id = jQuery("#inputSubCat").val();
		
		if (parent_id == 0) {
			jQuery("#inputNewSubCategory").show();
		}
		else {
			jQuery("#inputNewSubCategory").hide();
		}
	});
	
	jQuery('#send').click(function(){
		//Проверка заполненности
		if (jQuery("#inputNewCategory").css('display') != 'none') {
			slert('!!!');
		}
		
		return false;
	});
</script>

<?php get_footer(); ?>
