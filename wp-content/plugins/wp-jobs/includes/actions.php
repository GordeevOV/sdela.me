<?php
//Регистрация типа поля для окна действий

add_action( 'init', 'ovg_create_action_field' );

function ovg_create_action_field() {
	register_post_type( 'ovg_action',	
        array(
            'labels' => array(
                'name' => 'Действия',
                'singular_name' => 'Действие',
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
            'menu_position' => 15,
            'supports' => array( 'title'),
            'taxonomies' => array( 'ovg_actions_icons', 'ovg_actions_functions' ),
            'has_archive' => false
        )
    );		
}

add_action( 'init', 'ovg_create_action_taxonomy' );

function ovg_create_action_taxonomy() {

register_taxonomy(	//Категории
		'ovg_actions_icons',
		'ovg_action',
		array(
			'label' => 'Иконка',
			'hierarchical' => FALSE,
			'show_ui' => FALSE,
			'meta_box_cb' => 'post_tags_meta_box',
			'show_in_nav_menus' => FALSE,
			'public' => FALSE
		)
	);
	
	register_taxonomy(	//Категории
		'ovg_actions_functions',
		'ovg_action',
		array(
			'label' => 'Функция',
			'hierarchical' => FALSE,
			'show_ui' => FALSE,
			'meta_box_cb' => 'post_tags_meta_box',
			'show_in_nav_menus' => FALSE,
			'public' => FALSE
		)
	);
}

add_action( 'save_post', 'ovg_save_action_metabox' );
add_action( 'add_meta_boxes','ovg_add_metabox_for_action' );

function  ovg_add_metabox_for_action(){
	add_meta_box(
		'action_attribute_metabox', // ID, should be a string
		'Настройка параметров действия', // Meta Box Title
		'ovg_action_meta_box_content', // Your call back function, this is where your form field will go
		'ovg_action', // The post type you want this to edit screen section (�post�, �page�, �dashboard�, �link�, �attachment� or �custom_post_type� where custom_post_type is the custom post type slug)
		'normal', // The placement of your meta box, can be �normal�, �advanced�or side
		'high' // The priority in which this will be displayed
		);
}

function ovg_action_meta_box_content($post) {
	$actions_icon = get_post_meta($post->ID, 'actions_icons', true);
	$actions_functions = get_post_meta($post->ID, 'actions_functions', true);
?>
	
	<table style="border-spacing: 0px 5px; border-collapse: initial;">
		<tbody>
			<tr>
				<th style="width:300px;">Название иконки (fontawesome):</th>
				<td>
					<?php if(isset($actions_icon)) echo "<span><i class='fa fa-".$actions_icon."'></i></span>";?>
					
					<input type="text" name="actions_icons" id="actions_icons" value="<?php if(isset($actions_icon)) echo $actions_icon;?>" />
				</td>
			</tr>

			<tr>
				<th style="width:300px;">Имя функции-обработчика:</th>
				<td>
					<input type="text" name="actions_functions" id="actions_functions" value="<?php if(isset($actions_functions)) echo $actions_functions;?>" />
				</td>
			</tr>
		</tbody>
	</table>
<?php
}

function ovg_save_action_metabox($post_id) {
	$post = get_post($post_id);
	if($_POST){
		$action_icon = "";
		if(isset($_POST['actions_icons'])){	
			$action_icon = $_POST['actions_icons'];
		}
		
		$actions_functions = "";
		if(isset($_POST['actions_functions'])){	
			$actions_functions = $_POST['actions_functions'];
		}
		
		update_post_meta($post->ID, 'actions_icons', $action_icon);
		update_post_meta($post->ID, 'actions_functions', $actions_functions);
	}
}


//Подгрузка fontawesome в админку

function wpb_load_fa() {

wp_enqueue_style( 'wpb-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );

}

add_action( 'admin_enqueue_scripts', 'wpb_load_fa' );

?>
