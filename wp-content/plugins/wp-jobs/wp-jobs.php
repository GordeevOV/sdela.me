<?php
/*
Plugin Name: WP Jobs
Description: Job posting Plugin
Version: 1.0
Author: Oleg V. Gordeev
*/

define('WP_JOBS_DIR', plugin_dir_path(__FILE__));
define('WP_JOBS_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'wp_jobs_activation');
register_deactivation_hook(__FILE__, 'wp_jobs_deactivation');

function wp_jobs_activation() {
 
    // действие при активации
    register_uninstall_hook(__FILE__, 'wp_jobs_uninstall');
    //remove_role( 'subscriber' );
    //remove_role( 'author' );
    add_role('moderator_role', 'Модератор', array( 'read' => true ) );
}
 
function wp_jobs_deactivation() {
    // при деактивации
}

function wp_jobs_uninstall(){
 
    //действие при удалении
}

//Удаляем лишнее из профиля

function admin_del_options() {
	global $_wp_admin_css_colors;
	global $wp_rich_edit_exists;
	 
	$_wp_admin_css_colors = 0;
	$wp_rich_edit_exists = 0;
}
 
add_action(‘admin_head’, ‘admin_del_options’);

function true_remove_personal_options(){
	echo "\n" . '<script type="text/javascript">
	jQuery(document).ready(function($) {
	$(\'form#your-profile > h2:first\').hide();
	$(\'form#your-profile > table:first\').hide();
	$(\'form#your-profile > h2:eq(3)\').hide();
	$(\'form#your-profile > table:eq(3)\').hide();
	$(\'form#your-profile tr.user-url-wrap\').hide();
	$(\'form#your-profile > table:last\').prependTo(\'form#your-profile > h2:last\');
	
	$(\'form#your-profile\').show(); });
	</script>' . "\n";
}
 
add_action('admin_head', 'true_remove_personal_options');

//Добавляем поля в профиль пользователя
add_filter('user_contactmethods', 'my_user_contactmethods');

function my_user_contactmethods($user_contactmethods)
{
    $user_contactmethods['tel'] = '<b>Телефон</b>'; 
    $user_contactmethods['tel2'] = '<b>Телефон 2</b>';
    $user_contactmethods['addr'] = '<b>Адрес</b>'; 
    $user_contactmethods['birthdate'] = '<b>Дата рождения</b>';  
	$user_contactmethods['education'] = '<b>Образование</b>';
	$user_contactmethods['spec'] = '<b>Специальность</b>';
	$user_contactmethods['experience'] = '<b>Опыт работы</b>';
	$user_contactmethods['job_category'] = '<b>Виды деятельности</b>';
	$user_contactmethods['facebook'] = '<b>Facebook</b>';
	$user_contactmethods['vkontakte'] = '<b>ВКонтакте</b>';
	$user_contactmethods['twitter'] = '<b>Twitter</b>';
	$user_contactmethods['linkedin'] = '<b>LinkedIn</b>';
	$user_contactmethods['instagram'] = '<b>Istagram</b>';
	$user_contactmethods['skype'] = '<b>Skype</b>';
	$user_contactmethods['viber'] = '<b>Viber</b>';
	$user_contactmethods['whatsapp'] = '<b>WhatsApp</b>';

    return $user_contactmethods;
}

//Добавляем раздел в профиль пользователя

### дополнительные данные на странице профиля
add_action('show_user_profile', 'my_profile_new_fields_add');
add_action('edit_user_profile', 'my_profile_new_fields_add');

add_action('personal_options_update', 'my_profile_new_fields_update');
add_action('edit_user_profile_update', 'my_profile_new_fields_update');

function my_profile_new_fields_add(){ 
	global $user_ID;
	
	$addinfo = get_user_meta( $user_ID, "user_addinfo", 1 );
	
	?>
	<table class="form-table">
		<tr>
			<th><label for="user_fb_txt">Дополнительная информация</label></th>
			<td>
				<textarea name="user_addinfo" rows=5 cols=30><?php echo $addinfo ?></textarea><br>
			</td>
		</tr>
	</table>
	<?php            
}

// обновление
function my_profile_new_fields_update(){
	global $user_ID;
	
	update_user_meta( $user_ID, "user_addinfo", $_POST['user_addinfo'] );
}

//Регистрация скрипта загрузки картинок
add_action( 'admin_enqueue_scripts', 'ovg_include_myuploadscript' );
function ovg_include_myuploadscript() {
	// у вас в админке уже должен быть подключен jQuery, если нет - раскомментируйте следующую строку:
	// wp_enqueue_script('jquery');
	// дальше у нас идут скрипты и стили загрузчика изображений WordPress
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
	// само собой - меняем admin.js на название своего файла
 	wp_enqueue_script( 'myuploadscript', WP_JOBS_URL . 'js/upload.js', array('jquery'), null, false );
 	wp_enqueue_style('admin_styles', WP_JOBS_URL . 'css/admin.css');
 	// подключаем все необходимые скрипты: jQuery, jquery-ui, datepicker
	wp_enqueue_script('jquery-ui-datepicker');
	// подключаем нужные css стили
	wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );
}



//регистрация типа поля ЗТУ
add_action( 'init', 'ovg_create_ztu_field' );

function ovg_create_ztu_field() {
	register_post_type( 'ovg_ztu',	
        array(
            'labels' => array(
                'name' => 'Заказ/товар',
                'singular_name' => 'Заказ/товар',
                'add_new' => 'Добавить',
                'add_new_item' => 'Добавить',
                'edit' => 'Редактировать',
                'edit_item' => 'Редактировать',
                'new_item' => 'Создать',
                'view' => 'Просмотреть',
                'view_item' => 'Просмотреть',
                'search_items' => 'Найти',
                'not_found' => 'Не найлено',
                'not_found_in_trash' => 'Не найлено в корзине'
            ),
            'public' => true,
            'rewrite' => array( 'slug' => 'usluga' ),
            'menu_position' => 15,
            'supports' => array( 'title', 'thumbnail'),
            'taxonomies' => array( '' ),
            //'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => false
        )
    );		
}

//Регистрация таксономия для ЗТУ

add_action( 'init', 'ovg_create_ztu_taxonomy' );

function ovg_create_ztu_taxonomy() {
	register_taxonomy(  //Типы заказ/товар/услуга
		'ovg_ztu_type',
		'ovg_ztu',
		array(
			'label' => 'Типы',
			'hierarchical' => FALSE,
			'meta_box_cb' => FALSE
		)
	);
	
	register_taxonomy(	//Категории
		'ovg_ztu_categories',
		'ovg_ztu',
		array(
			'label' => 'Категории заказа/товара/услуги',
			'hierarchical' => true,
			'meta_box_cb' => FALSE
		)
	);
	
	register_taxonomy(	//Категории
		'ovg_ztu_price_type',
		'ovg_ztu',
		array(
			'label' => 'Типы цен',
			'hierarchical' => FALSE,
			'meta_box_cb' => FALSE
		)
	);
}

//Редактирование ЗТУ

add_action( 'save_post', 'ovg_save_ztu_metabox' );
add_action( 'add_meta_boxes','ovg_add_metabox_for_ztu' );

function  ovg_add_metabox_for_ztu(){
	add_meta_box(
		'ztu_attribute_metabox', // ID, should be a string
		'Описание заказа/товара/услуги', // Meta Box Title
		'ovg_ztu_meta_box_content', // Your call back function, this is where your form field will go
		'ovg_ztu', // The post type you want this to edit screen section (�post�, �page�, �dashboard�, �link�, �attachment� or �custom_post_type� where custom_post_type is the custom post type slug)
		'normal', // The placement of your meta box, can be �normal�, �advanced�or side
		'high' // The priority in which this will be displayed
		);
}

function ovg_ztu_meta_box_content($post) {
	$ztu_type = get_post_meta($post->ID, 'ztu_type', true);
	$ztu_category = get_post_meta($post->ID, 'ztu_category', true);
	$ztu_subcategory = get_post_meta($post->ID, 'ztu_subcategory', true);
	$ztu_price = get_post_meta($post->ID, 'ztu_price', true);
	$ztu_price_type = get_post_meta($post->ID, 'ztu_price_type', true);
	$ztu_place = get_post_meta($post->ID, 'ztu_place', true);
	$ztu_descr = get_post_meta($post->ID, 'ztu_descr', true);
	$ztu_photo = get_post_meta($post->ID, 'ztu_photo', true);
	$ztu_video = get_post_meta($post->ID, 'ztu_video', true);
	$ztu_begin = get_post_meta($post->ID, 'ztu_begin', true);
	$ztu_end = get_post_meta($post->ID, 'ztu_end', true);
	
	
	//echo print_r(json_decode($car_transfer[0]));
?>
	<table style="border-spacing: 0px 5px; border-collapse: initial;">
		<tbody>
			<tr>
				<th style="width:300px;">Тип:</th>
				<td>
					<select name="ztumetabox_type" id="ztumetabox_type">
					
					<?php 
						$types = get_terms('ovg_ztu_type', array('orderby' => 'name', 'fields' => 'names', 'hide_empty' => 0));
						//print_r($types);
						foreach($types as $type):
					?>
					
						<option value="<?php echo $type?>" <?php if($ztu_type == $type) echo 'selected'; ?>><?php echo $type?></option>
						
						
					<?php endforeach;?>
					</select>
				</td>
			</tr>

			<tr>
				<th style="width:300px;">Категория:</th>
				<td>
					<select name="ztumetabox_category" id="ztumetabox_category">
					
					<?php 
						$categories = get_terms('ovg_ztu_categories', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0, 'parent' => 0));
						//print_r($cities);
						foreach($categories as $category_id=>$category_name):
					?>
					
						<option value="<?php echo $category_id?>" <?php if($ztu_category == $category_id) echo 'selected'; ?>><?php echo $category_name?></option>
						
						
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th style="width:300px;">Подкатегория:</th>
				<td>
					<select name="ztumetabox_subcategory" id="ztumetabox_subcategory">
					
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
				</td>
			</tr>
			
			<tr>
				<th style="width:300px;">Подробное описание:</th>
				<td>
					<textarea name="ztumetabox_descr" id="ztumetabox_descr"><?php if(isset($ztu_descr)) echo $ztu_descr;?></textarea>
				</td>
			</tr>
			
			<tr>
				<th style="width:300px;">Цена:</th>
				<td>
					<input type="text" name="ztumetabox_price" id="ztumetabox_price" value="<?php if(isset($ztu_price)) echo $ztu_price;?>" />
					<select name="ztumetabox_price_type" id="ztumetabox_price_type">
					
					<?php 
						$price_types = get_terms('ovg_ztu_price_type', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0));
						//print_r($types);
						foreach($price_types as $price_type_id=>$price_type_name):
					?>
					
						<option value="<?php echo $price_type_id?>" <?php if($ztu_price_type == $price_type_id) echo 'selected'; ?>><?php echo $price_type_name?></option>
						
						
					<?php endforeach;?>
					</select>
					
				</td>
			</tr>
			
			<?php
			$i = 0;
			if ($ztu_photo) {
		
			foreach ($ztu_photo as $photo) {
			
				$w=115;
				$h=90;
				$default = get_stylesheet_directory_uri() . '/img/no-image.png';
				if( $photo ) {
					$image_attributes = wp_get_attachment_image_src( $photo, array($w, $h) );
					$src = $image_attributes[0];
				} else {
					$src = $default;
				}
			?>
			<tr>
				<th style="width:300px;">Изображения:</th>
				<td>
					<div style="float: left;">
						<img data-src="<?php echo $default?>" src="<?php echo $src?>" width="<?php echo $w?>px" />
						
						<div>
							<input type="hidden" name="ztumetabox_number[<?php echo $i; ?>]" class="ztumetabox_number" value="<?php echo $i; ?>" />
							<input type="hidden" name="ztumetabox_photo[<?php echo $i; ?>]" id="ztumetabox_photo[<?php echo $i; ?>]" value="<?php echo $photo?>" />
							<button type="submit" class="upload_image_button button">Загрузить</button>
							<button type="submit" class="remove_image_button button">&times;</button>
						</div>
					</div>
				</td>
			</tr>
			
			<?php
			$i++;
			}
			}
			else {
				$w=115;
				$h=90;
				$default = get_stylesheet_directory_uri() . '/img/no-image.png';
				if( $photo ) {
					$image_attributes = wp_get_attachment_image_src( $photo, array($w, $h) );
					$src = $image_attributes[0];
				} else {
					$src = $default;
				}
			?>
			<tr>
				<th style="width:300px;">Изображения:</th>
				<td>
					<div style="float: left;">
						<img data-src="<?php echo $default?>" src="<?php echo $src?>" width="<?php echo $w?>px" />
						<div>
							<input type="hidden" name="ztumetabox_number[0]" class="ztumetabox_number" value="0" />
							<input type="hidden" name="ztumetabox_photo[0]" id="ztumetabox_photo[0]" value="" />
							<button type="submit" class="upload_image_button button">Загрузить</button>
							<button type="submit" class="remove_image_button button">&times;</button>
						</div>
					</div>
				</td>
			</tr>
			
			<?php
			}
			?>
			<tr>
				<th></th>
				<td><button type="submit" class="add_image_button button">Добавить изображения</button></td>
			</tr>
			
			<tr>
				<th style="width:300px;">Ссылка на видео:</th>
				<td>
					<input type="text" name="ztumetabox_video" id="ztumetabox_video" value="<?php if(isset($ztu_video)) echo $ztu_video;?>" />
				</td>
			</tr>
			
			<tr>
				<th style="width:300px;">Даты начала-окончания:</th>
				<td>
					<input class="datepicker" type="text" name="ztumetabox_begin" id="ztumetabox_begin" value="<?php if(isset($ztu_begin)) echo $ztu_begin;?>" />
					<span> - </span>
					<input class="datepicker" type="text" name="ztumetabox_end" id="ztumetabox_end" value="<?php if(isset($ztu_end)) echo $ztu_end;?>" />
				</td>
			</tr>
			
		</tbody>
	</table>

<script>
	jQuery("#ztumetabox_category").change(function(){
		parent_id = jQuery("#ztumetabox_category").val();
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
			jQuery("#ztumetabox_subcategory").empty();
			jQuery.each(data, function(key, value) {
				jQuery("#ztumetabox_subcategory").append(jQuery("<option value='" + key + "'>" + value + "</option>"));
			});
		},
		error : function(s , i , error){
			console.log(error);
		}
	});
	});
</script>

<?
}

function ovg_save_ztu_metabox($post_id) {
	$post = get_post($post_id);
	if($_POST){
		$ztu_type = "";
		if(isset($_POST['ztumetabox_type'])){	
			$ztu_type = $_POST['ztumetabox_type'];
		}
		
		$ztu_category = "";
		if(isset($_POST['ztumetabox_category'])){	
			$ztu_category = $_POST['ztumetabox_category'];
		}
		
		$ztu_subcategory = "";
		if(isset($_POST['ztumetabox_subcategory'])){	
			$ztu_subcategory = $_POST['ztumetabox_subcategory'];
		}
		
		$ztu_descr = "";
		if(isset($_POST['ztumetabox_descr'])){	
			$ztu_descr = $_POST['ztumetabox_descr'];
		}
		
		$ztu_price = "";
		if(isset($_POST['ztumetabox_price'])){	
			$ztu_price = $_POST['ztumetabox_price'];
		}
		
		$ztu_price_type = "";
		if(isset($_POST['ztumetabox_price_type'])){	
			$ztu_price_type = $_POST['ztumetabox_price_type'];
		}
		
		$ztu_photo = "";
		if(isset($_POST['ztumetabox_photo'])){	
			$ztu_photo = $_POST['ztumetabox_photo'];
			$ztu_photo = array_diff($ztu_photo, array('')); //Удаляем все пустые элементы из массива
		}
		
		$ztu_video = "";
		if(isset($_POST['ztumetabox_video'])){	
			$ztu_video = $_POST['ztumetabox_video'];
		}
		
		$ztu_begin = "";
		if(isset($_POST['ztumetabox_begin'])){	
			$ztu_begin = $_POST['ztumetabox_begin'];
		}
		
		$ztu_end = "";
		if(isset($_POST['ztumetabox_end'])){	
			$ztu_end = $_POST['ztumetabox_end'];
		}
		
		update_post_meta($post->ID, 'ztu_type', $ztu_type);
		update_post_meta($post->ID, 'ztu_category', $ztu_category);
		update_post_meta($post->ID, 'ztu_subcategory', $ztu_subcategory);
		update_post_meta($post->ID, 'ztu_descr', $ztu_descr);
		update_post_meta($post->ID, 'ztu_price', $ztu_price);
		update_post_meta($post->ID, 'ztu_price_type', $ztu_price_type);
		update_post_meta($post->ID, 'ztu_photo', $ztu_photo);
		update_post_meta($post->ID, 'ztu_video', $ztu_video);
		update_post_meta($post->ID, 'ztu_begin', $ztu_begin);
		update_post_meta($post->ID, 'ztu_end', $ztu_end);
	}
}

//-------------AJAX functions--------------------
add_action( 'wp_ajax_nopriv_ovg_load_subcategories','ovg_load_subcategories' );
add_action( 'wp_ajax_ovg_load_subcategories', 'ovg_load_subcategories' );

function ovg_load_subcategories() {
	if ( count($_POST) > 0 ) {
		$parent_id = $_POST['parent_id'];
		
		$subcategories = get_terms('ovg_ztu_categories', array('orderby' => 'name', 'fields' => 'id=>name', 'hide_empty' => 0, 'parent' => $parent_id));
		$subcategories = json_encode($subcategories);
		echo $subcategories;
		exit;
	}
}
?>