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
		<form class="add-form form-horizontal">
			<div class="row">
				<div class="col-md-6 col-xs-12">
					<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Что надо сделать</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control" id="inputTitle">
					    </div>
				  	</div>
				  	<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Категория</label>
					    <div class="col-sm-6">
					      <select class="form-control" id="inputCat" name="inputCat">
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
				  	<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Подкатегория</label>
					    <div class="col-sm-6">
					      <select class="form-control" id="inputSubCat" name="inputSubCat">
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
				  	<div class="form-group">
					    <label for="inputTitle" class="col-sm-6 control-label">Стоимость работы</label>
					    <div class="col-sm-6">
					      <input type="text" class="form-control" id="inputTitle" name="inputTitle">
					    </div>
				  	</div>
				</div>
				<div class="col-md-6 col-xs-12">
					<div class="col-md-8 col-xs-12"></div>
					<div class="col-md-4 col-xs-12">
						<select name="inputType" id="inputType" class="form-control form-type">
						
						<?php 
							$types = get_terms('ovg_ztu_type', array('orderby' => 'name', 'fields' => 'names', 'hide_empty' => 0));
							//print_r($types);
							foreach($types as $type):
						?>
						
							<option value="<?php echo $type?>" <?php if($ztu_type == $type) echo 'selected'; ?>><?php echo $type?></option>
							
							
						<?php endforeach;?>
						</select>
					</div>
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
			jQuery.each(data, function(key, value) {
				jQuery("#inputSubCat").append(jQuery("<option value='" + key + "'>" + value + "</option>"));
			});
		},
		error : function(s , i , error){
			console.log(error);
		}
	});
	});
</script>

<?php get_footer(); ?>