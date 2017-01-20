<?php

class w2dc_search_map_form extends w2dc_search_form {

	public function __construct($uid = null) {
		$this->uid = $uid;
		$this->controller = 'listings_controller';
	}

	public function display($columns = 2, $advanced_open = false) {
		global $w2dc_instance;

		// random ID needed because there may be more than 1 search form on one page
		$random_id = w2dc_generateRandomVal();
		
		$search_url = ($w2dc_instance->index_page_url) ? w2dc_directoryUrl() : home_url('/');

		w2dc_renderTemplate('search_map_form.tpl.php', array('random_id' => $random_id, 'search_url' => $search_url, 'hash' => $this->uid, 'controller' => $this->controller));
	}
}
?>