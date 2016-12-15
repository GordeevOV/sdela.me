<?php
/* Define these, So that WP functions work inside this file */
define('WP_USE_THEMES', false);
require( $_SERVER['DOCUMENT_ROOT'] .'/wp-blog-header.php');
?>
<?php
//echo "<pre>".print_r($_POST)."</pre>";
if(isset($_POST['send']) == '1') {
 $post_title = $_POST['inputTitle'];
 
 $new_post = array(
 'ID' => '',
 'post_type'     => 'ovg_ztu',
 'post_author' => $user->ID,
 'post_category' => array($post_category),
 'post_content' => $post_content,
 'post_title' => $post_title,
 'post_status' => 'publish'
 );
 
 $post_id = wp_insert_post($new_post);
 
 //echo $post_id;
 
 $post = get_post($post_id);
	if($_POST){
		$ztu_type = "";
		if(isset($_POST['inputType'])){	
			$ztu_type = $_POST['inputType'];
		}
		
		$ztu_category = "";
		if(isset($_POST['inputCat'])){
			
			if ($_POST['inputCat'] == 0) { //Добавляем категорию
				$newCat = $_POST['inputNewCategory'];
				$new_term = wp_insert_term( $newCat, 'ovg_ztu_categories');
				$ztu_category = $new_term['term_id'];
			}
			else {	
				$ztu_category = $_POST['inputCat'];
			}
		}
		
		$ztu_subcategory = "";
		if(isset($_POST['inputSubCat'])){
			
			if ($_POST['inputSubCat'] == 0) { //Добавляем подкатегорию
				$newCat = $_POST['inputNewSubCategory'];
				
				if ($_POST['inputCat'] == 0) {
					$parent_id = $new_term['term_id'];
				}
				else {
					$parent_id = $_POST['inputCat'];
				}
				
				$args = array(
					'alias_of'=>''
					,'description'=>''
					,'parent'=>$parent_id
					,'slug'=>''
				);
				$new_subterm = wp_insert_term( $newCat, 'ovg_ztu_categories', $args);
				$ztu_subcategory = $new_subterm['term_id'];
			}
			else {
				$ztu_subcategory = $_POST['inputSubCat'];
			}			
		}
		
		$ztu_descr = "";
		if(isset($_POST['inputDescr'])){	
			$ztu_descr = $_POST['inputDescr'];
		}
		
		$ztu_price = "";
		if(isset($_POST['inputPrice'])){	
			$ztu_price = $_POST['inputPrice'];
		}
		
		$ztu_price_type = "";
		if(isset($_POST['inputPriceType'])){	
			$ztu_price_type = $_POST['inputPriceType'];
		}
		
		$ztu_photo = "";
		if(isset($_POST['ztumetabox_photo'])){	
			$ztu_photo = $_POST['ztumetabox_photo'];
			$ztu_photo = array_diff($ztu_photo, array('')); //Удаляем все пустые элементы из массива
		}
		
		$ztu_video = "";
		if(isset($_POST['inputVideo'])){	
			$ztu_video = $_POST['inputVideo'];
		}
		
		$ztu_begin = "";
		if(isset($_POST['inputTime'])){	
			$ztu_begin = $_POST['inputTime'];
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
 
 // This will redirect you to the newly created post
 $post = get_post($post_id);
 wp_redirect($post->guid);
}
?>