<?php 

class w2dc_search_controller extends w2dc_frontend_controller {

	public function init($args = array()) {
		parent::init($args);

		$shortcode_atts = array_merge(array(
				'columns' => 2,
				'advanced_open' => false,
				'uid' => null,
				'show_what_search' => true,
				'show_categories_search' => true,
				'show_keywords_search' => true,
				'category' => 0,
				'what_search' => '',
				'show_radius_search' => true,
				'radius' => 0,
				'show_where_search' => true,
				'show_locations_search' => true,
				'show_address_search' => true,
				'address' => '',
				'location' => 0,
				'search_fields' => '',
				'search_fields_advanced' => '',
		), $args);
		
		$this->args = $shortcode_atts;
		
		$hash = false;
		if ($this->args['uid'])
			$hash = md5($this->args['uid']);

		$this->search_form = new w2dc_search_form($hash, 'listings_controller', $this->args);
		
		apply_filters('w2dc_frontend_controller_construct', $this);
	}

	public function display() {
		ob_start();
		$this->search_form->display($this->args['columns'], $this->args['advanced_open']);
		$output = ob_get_clean();

		return $output;
	}
}

?>