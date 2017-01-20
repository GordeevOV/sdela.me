<?php

class w2dc_csv_manager {
	public $menu_page_hook;
	
	private $test_mode = false;
	
	private $log = array('errors' => array(), 'messages' => array());
	private $header_columns = array();
	private $rows = array();
	private $collated_fields = array();
	
	private $csv_file_name;
	private $images_dir;
	private $columns_separator;
	private $values_separator;
	private $if_term_not_found;
	private $selected_user;
	private $do_geocode;
	private $is_claimable;
	
	public $collation_fields;
	
	public function __construct() {
		add_action('admin_menu', array($this, 'menu'));
	}
	
	public function menu() {
		$this->menu_page_hook = add_submenu_page('w2dc_settings',
			__('CSV Import', 'W2DC'),
			__('CSV Import', 'W2DC'),
			'administrator',
			'w2dc_csv_import',
			array($this, 'w2dc_csv_import')
		);
	}
	
	private function buildCollationColumns() {
		global $w2dc_instance;
		
		$this->collation_fields = array(
				'title' => __('Title*', 'W2DC'),
				'level_id' => __('Level ID*', 'W2DC'),
				'user' => __('Author', 'W2DC'),
				'categories_list' => __('Categories', 'W2DC'),
				'listing_tags' => __('Tags', 'W2DC'),
				'content' => __('Description', 'W2DC'),
				'excerpt' => __('Summary', 'W2DC'),
				'locations_list' => __('Locations (existing or new)', 'W2DC'),
				'address_line_1' => __('Address line 1', 'W2DC'),
				'address_line_2' => __('Address line 2', 'W2DC'),
				'zip' => __('Zip code or postal index', 'W2DC'),
				'latitude' => __('Latitude', 'W2DC'),
				'longitude' => __('Longitude', 'W2DC'),
				'map_icon_file' => __('Map icon file', 'W2DC'),
				'images' => __('Images files', 'W2DC'),
				'videos' => __('YouTube or Vimeo videos', 'W2DC'),
				'expiration_date' => __('Listing expiration date', 'W2DC'),
				'contact_email' => __('Listing contact email', 'W2DC'),
		);
		
		$this->collation_fields = apply_filters('w2dc_csv_collation_fields_list', $this->collation_fields);
		
		foreach ($w2dc_instance->content_fields->content_fields_array AS $field)
			if (!$field->is_core_field)
			$this->collation_fields[$field->slug] = $field->name;
	}
	
	public function w2dc_csv_import() {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'import_settings') {
			// 2nd Step
			$this->csvCollateColumns();
		} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'import_collate' && isset($_REQUEST['csv_file_name'])) {
			// 3rd Step
			$this->csvImport();
		} elseif (!isset($_REQUEST['action'])) {
			// 1st Step
			$this->csvImportSettings();
		}
	}
	
	// 1st Step
	public function csvImportSettings($vars = array()) {

		w2dc_renderTemplate('csv_manager/import_settings.tpl.php', $vars);
	}

	// 2nd Step
	public function csvCollateColumns() {
		$this->buildCollationColumns();
		$users = get_users(array('orderby' => 'ID'));

		if ((w2dc_getValue($_POST, 'submit') || w2dc_getValue($_POST, 'goback')) && wp_verify_nonce($_POST['w2dc_csv_import_nonce'], W2DC_PATH)) {
			$errors = false;

			$validation = new w2dc_form_validation();
			$validation->set_rules('columns_separator', __('Columns separator', 'W2DC'), 'required');
			$validation->set_rules('values_separator', __('categories separator', 'W2DC'), 'required');

			// GoBack button places on import results page
			if (w2dc_getValue($_POST, 'goback')) {
				$validation->set_rules('csv_file_name', __('CSV file name', 'W2DC'), 'required');
				$validation->set_rules('images_dir', __('Images directory', 'W2DC'));
				$validation->set_rules('if_term_not_found', __('Category not found', 'W2DC'), 'required');
				$validation->set_rules('listings_author', __('Listings author', 'W2DC'), 'required|numeric');
				$validation->set_rules('do_geocode', __('Geocode imported listings', 'W2DC'));
				if (get_option('w2dc_fsubmit_addon') && get_option('w2dc_claim_functionality'))
					$validation->set_rules('is_claimable', __('Configure imported listings as claimable', 'W2DC'));
				$validation->set_rules('fields[]', __('Listings fields', 'W2DC'));
			}

			if ($validation->run()) {
				$this->columns_separator = $validation->result_array('columns_separator');
				$this->values_separator = $validation->result_array('values_separator');
				
				// GoBack button places on import results page
				if (w2dc_getValue($_POST, 'goback')) {
					$this->csv_file_name = $validation->result_array('csv_file_name');
					$this->images_dir = $validation->result_array('images_dir');
					$this->if_term_not_found = $validation->result_array('if_term_not_found');
					$this->selected_user = $validation->result_array('listings_author');
					$this->do_geocode = $validation->result_array('do_geocode');
					if (get_option('w2dc_fsubmit_addon') && get_option('w2dc_claim_functionality'))
						$this->is_claimable = $validation->result_array('is_claimable');
					$this->collated_fields = $validation->result_array('fields[]');
				}

				// GoBack button places on import results page
				if (w2dc_getValue($_POST, 'goback')) {
					$csv_file_name = $this->csv_file_name;

					if (!is_file($csv_file_name)) {
						w2dc_addMessage(esc_attr__("CSV temp file doesn't exist", 'W2DC'));
						return $this->csvImportSettings($validation->result_array());
					}

					if ($this->images_dir && !is_dir($this->images_dir)) {
						w2dc_addMessage(esc_attr__("Images temp directory doesn't exist", 'W2DC'));
						return $this->csvImportSettings($validation->result_array());
					}
				} else {
					$csv_file = $_FILES['csv_file'];

					if ($csv_file['error'] || !is_uploaded_file($csv_file['tmp_name'])) {
						w2dc_addMessage(__('There was a problem trying to upload CSV file', 'W2DC'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
	
					if (strtolower(pathinfo($csv_file['name'], PATHINFO_EXTENSION)) != 'csv' && $csv_file['type'] != 'text/csv') {
						w2dc_addMessage(__('This is not CSV file', 'W2DC'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
					
					if (function_exists('mb_detect_encoding') && !mb_detect_encoding(file_get_contents($csv_file['tmp_name']), 'UTF-8', true)) {
						w2dc_addMessage(__("CSV file must be in UTF-8", 'W2DC'), 'error');
						return $this->csvImportSettings($validation->result_array());
					}
					
					$upload_dir = wp_upload_dir();
					$csv_file_name = $upload_dir['path'] . '/' . $csv_file["name"];
					move_uploaded_file($csv_file['tmp_name'], $csv_file_name);

					if ($_FILES['images_file']['tmp_name']) {
						$images_file = $_FILES['images_file'];
						
						if ($images_file['error'] || !is_uploaded_file($images_file['tmp_name'])) {
							w2dc_addMessage(__('There was a problem trying to upload ZIP images file', 'W2DC'), 'error');
							return $this->csvImportSettings($validation->result_array());
						}
	
						if (!$this->extractImages($images_file['tmp_name'])) {
							w2dc_addMessage(__('There was a problem trying to unpack ZIP images file', 'W2DC'), 'error');
							return $this->csvImportSettings($validation->result_array());
						}
					}
				}
				
				$this->extractCsv($csv_file_name);

				if ($this->log['errors']) {
					foreach ($this->log['errors'] AS $message)
						w2dc_addMessage($message, 'error');

					return $this->csvImportSettings($validation->result_array());
				}

				w2dc_renderTemplate('csv_manager/collate_columns.tpl.php', array(
						'collation_fields' => $this->collation_fields,
						'collated_fields' => $this->collated_fields,
						'headers' => $this->header_columns,
						'rows' => $this->rows,
						'columns_separator' => $this->columns_separator,
						'values_separator' => $this->values_separator,
						'csv_file_name' => $csv_file_name,
						'images_dir' => $this->images_dir,
						'users' => $users,
						'if_term_not_found' => $this->if_term_not_found,
						'listings_author' => $this->selected_user,
						'do_geocode' => $this->do_geocode,
						'is_claimable' => $this->is_claimable,
				));
			} else {
				w2dc_addMessage($validation->error_string(), 'error');
				
				return $this->csvImportSettings($validation->result_array());
			}
		} else
			return $this->csvImportSettings();
	}
	
	// 3rd Step
	public function csvImport() {
		$this->buildCollationColumns();

		if ((w2dc_getValue($_POST, 'submit') || w2dc_getValue($_POST, 'tsubmit')) && wp_verify_nonce($_POST['w2dc_csv_import_nonce'], W2DC_PATH)) {
			if (w2dc_getValue($_POST, 'tsubmit'))
				$this->test_mode = true;

			$errors = false;

			$validation = new w2dc_form_validation();
			$validation->set_rules('csv_file_name', __('CSV file name', 'W2DC'), 'required');
			$validation->set_rules('images_dir', __('Images directory', 'W2DC'));
			$validation->set_rules('columns_separator', __('Columns separator', 'W2DC'), 'required');
			$validation->set_rules('values_separator', __('categories separator', 'W2DC'), 'required');
			$validation->set_rules('if_term_not_found', __('Category not found', 'W2DC'), 'required');
			$validation->set_rules('listings_author', __('Listings author', 'W2DC'), 'required|numeric');
			$validation->set_rules('do_geocode', __('Geocode imported listings', 'W2DC'), 'is_checked');
			if (get_option('w2dc_fsubmit_addon') && get_option('w2dc_claim_functionality'))
				$validation->set_rules('is_claimable', __('Configure imported listings as claimable', 'W2DC'), 'is_checked');
			$validation->set_rules('fields[]', __('Listings fields', 'W2DC'));
				
			if ($validation->run()) {
				$this->csv_file_name = $validation->result_array('csv_file_name');
				$this->images_dir = $validation->result_array('images_dir');
				$this->columns_separator = $validation->result_array('columns_separator');
				$this->values_separator = $validation->result_array('values_separator');
				$this->if_term_not_found = $validation->result_array('if_term_not_found');
				$this->selected_user = $validation->result_array('listings_author');
				$this->do_geocode = $validation->result_array('do_geocode');
				if (get_option('w2dc_fsubmit_addon') && get_option('w2dc_claim_functionality'))
					$this->is_claimable = $validation->result_array('is_claimable');
				$this->collated_fields = $validation->result_array('fields[]');
				
				if (!is_file($this->csv_file_name))
					$this->log['errors'][] = esc_attr__("CSV temp file doesn't exist", 'W2DC');

				if ($this->images_dir && !is_dir($this->images_dir))
					$this->log['errors'][] = esc_attr__("Images temp directory doesn't exist", 'W2DC');
				
				if (!in_array('title', $this->collated_fields))
					$this->log['errors'][] = esc_attr__("Title field wasn't collated", 'W2DC');
				
				if (!in_array('level_id', $this->collated_fields))
					$this->log['errors'][] = esc_attr__("Level ID field wasn't collated", 'W2DC');
		
				if ($this->selected_user != 0 && !get_userdata($this->selected_user))
					$this->log['errors'][] = esc_attr__("There isn't author user you selected", 'W2DC');
				if ($this->selected_user == 0 && !in_array('user', $this->collated_fields))
					$this->log['errors'][] = esc_attr__("Author field wasn't collated and default author wasn't selected", 'W2DC');

				$this->extractCsv($this->csv_file_name);
				
				ob_implicit_flush(true);
				w2dc_renderTemplate('admin_header.tpl.php');
				
				echo "<h2>" . __('CSV Import', 'W2DC') . "</h2>";
				echo "<h3>" . __('Import results', 'W2DC') . "</h3>";

				if (!$this->log['errors']) {
					$this->processCSV();
	
					if (!$this->test_mode) {
						unlink($this->csv_file_name);
						if ($this->images_dir)
							$this->removeImagesDir($this->images_dir);
					}
				} else {
					foreach ($this->log['errors'] AS $error) {
						echo '<p>'.$error.'</p>';
					}
				}
				
				w2dc_renderTemplate('csv_manager/import_results.tpl.php', array(
						'log' => $this->log,
						'test_mode' => $this->test_mode,
						'fields' => $this->collated_fields,
						'columns_separator' => $this->columns_separator,
						'values_separator' => $this->values_separator,
						'csv_file_name' => $this->csv_file_name,
						'images_dir' => $this->images_dir,
						'if_term_not_found' => $this->if_term_not_found,
						'listings_author' => $this->selected_user,
						'do_geocode' => $this->do_geocode,
						'is_claimable' => $this->is_claimable,
				));
			} else {
				w2dc_addMessage($validation->error_string(), 'error');
				
				return $this->csvImportSettings($validation->result_array());
			}
		}
	}
	
	private function extractCsv($csv_file) {
		ini_set('auto_detect_line_endings', true);

		if ($fp = fopen($csv_file, 'r')) {
			$n = 0;
			while (($line_columns = @fgetcsv($fp, 0, $this->columns_separator)) !== FALSE) {
				if ($line_columns) {
					if (!$this->header_columns) {
						$this->header_columns = $line_columns;
						foreach ($this->header_columns as &$column)
							$column = trim($column);
					} else {
						if (count($line_columns) > count($this->header_columns))
							$this->log['errors'][] = sprintf(__('Line %d has too many columns', 'W2DC'), $n+1);
						elseif (count($line_columns) < count($this->header_columns))
							$this->log['errors'][] = sprintf(__('Line %d has less columns than header line', 'W2DC'), $n+1);
						else
							$this->rows[] = $line_columns;
					}
				}
				$n++;
			}
			@fclose($fp);
		} else {
			$this->log['errors'][] = esc_attr__("Can't open CSV file", 'W2DC');
			return false;
		}
	}
	
	private function extractImages($zip_file) {
		$dir = trailingslashit(get_temp_dir() . 'w2dc_' . time());
		
		require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		
		$zip = new PclZip($zip_file);
		if ($files = $zip->extract(PCLZIP_OPT_PATH, $dir, PCLZIP_OPT_REMOVE_ALL_PATH)) {
			$this->images_dir = $dir;
			return true;
		}

		return false;
	}
	
	private function removeImagesDir($dir) {
		if (!isset($GLOBALS['wp_filesystem']) || !is_object($GLOBALS['wp_filesystem'])) {
			WP_Filesystem();
		}

		$wp_file = new WP_Filesystem_Direct($dir);
		return $wp_file->rmdir($dir, true);
	}

	private function processCSV() {
		global $wpdb, $w2dc_instance;
		
		printf(__('Import started, number of available rows in file: %d', 'W2DC'), count($this->rows));
		echo "<br />";
		if ($this->test_mode) {
			_e('Test mode enabled', 'W2DC');
			echo "<br />";
		}

		$users_logins = array();
		$users_emails = array();
		$users_ids = array();
		$users = get_users();
		foreach ($users AS $user) {
			$users_logins[] = $user->user_login;
			$users_emails[] = $user->user_email;
			$users_ids[] = $user->ID;
		}

		$levels = $w2dc_instance->levels->levels_array;
		$levels_ids = array_keys($levels);

		$total_rejected_lines = 0;
		foreach ($this->rows as $line=>$row) {
			$n = $line+1;
			printf(__('Importing line %d...', 'W2DC'), $n);
			echo "<br />";
			$error_on_line = false;
			$new_listing = array();
			foreach ($this->collated_fields as $i=>$field) {
				$value = trim($row[$i]);

				if ($field == 'title') {
					$new_listing['title'] = $value;
					printf(__('Listing title: %s', 'W2DC'), $value);
					echo "<br />";
				} elseif ($field == 'user') {
					if (!$this->selected_user) {
						if ((($key = array_search($value, $users_logins)) !== FALSE) || (($key = array_search($value, $users_emails)) !== FALSE) || (($key = array_search($value, $users_ids))) !== FALSE)
							$new_listing['user_id'] = $users_ids[$key];
						else {
							$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("User \"%s\" doesn't exist", 'W2DC'), $n, $value);
							$this->log['errors'][] = $error;
							echo $error;
							echo "<br />";
							$error_on_line = true;
						}
					} else 
						$new_listing['user_id'] = $this->selected_user;
				} elseif ($field == 'level_id') {
					if (in_array($value, $levels_ids))
						$new_listing['level_id'] = $value;
					else {
						$error = sprintf(__('line %d: ', 'W2DC') . __('Wrong level ID', 'W2DC'), $n);
						$this->log['errors'][] = $error;
						echo $error;
						echo "<br />";
						$error_on_line = true;
					}
				} elseif ($field == 'content') {
					$new_listing['content'] = $value;
				} elseif ($field == 'excerpt') {
					$new_listing['excerpt'] = $value;
				} elseif ($field == 'categories_list') {
					$new_listing['categories'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'listing_tags') {
					$new_listing['tags'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'locations_list') {
					$new_listing['locations'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'address_line_1') {
					$new_listing['address_line_1'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'address_line_2') {
					$new_listing['address_line_2'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'zip') {
					$new_listing['zip'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'latitude') {
					$new_listing['latitude'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'longitude') {
					$new_listing['longitude'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'map_icon_file') {
					$new_listing['map_icon_file'] = array_map('trim', explode($this->values_separator, $value));
				} elseif ($field == 'videos') {
					$new_listing['videos'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
				} elseif ($field == 'images') {
					if ($this->images_dir) {
						$new_listing['images'] = array_filter(array_map('trim', explode($this->values_separator, $value)));
					} else {
						$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Images column was specified, but ZIP archive wasn't upload", 'W2DC'), $n);
						$this->log['errors'][] = $error;
						echo $error;
						echo "<br />";
						$error_on_line = true;
					}
				} elseif ($content_field = $w2dc_instance->content_fields->getContentFieldBySlug($field)) {
					if (is_a($content_field, 'w2dc_content_field_checkbox')) {
						if ($value = array_map('trim', explode($this->values_separator, $value)))
							if (count($value) == 1)
								$value = array_shift($value);
					}

					if ($value) {
						$errors = array();
						$new_listing['content_fields'][$field] = $content_field->validateCsvValues($value, $errors);
						foreach ($errors AS $_error) {
							$error = sprintf(__('line %d: ', 'W2DC') . $_error, $n);
							$this->log['errors'][] = $error;
							echo $error;
							echo "<br />";
							$error_on_line = true;
						}
					}
				} elseif ($field == 'expiration_date') {
					if (!($timestamp = strtotime($value))) {
						$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Expiration date value is incorrect", 'W2DC'), $n);
						$this->log['errors'][] = $error;
						echo $error;
						echo "<br />";
						$error_on_line = true;
					} else
						$new_listing['expiration_date'] = $timestamp;
				} elseif ($field == 'contact_email') {
					if (!is_email($value)) {
						$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Contact email is incorrect", 'W2DC'), $n);
						$this->log['errors'][] = $error;
						echo $error;
						echo "<br />";
						$error_on_line = true;
					} else
						$new_listing['contact_email'] = $value;
				}
				
				$new_listing = apply_filters('w2dc_csv_process_fields', $new_listing, $field, $value);
			}

			if (!$error_on_line) {
				if (!$this->test_mode) {
					$new_listing_level = $levels[$new_listing['level_id']];

					$new_post_args = array(
							'post_title' => $new_listing['title'],
							'post_type' => W2DC_POST_TYPE,
							'post_author' => (isset($new_listing['user_id']) ? $new_listing['user_id'] : $this->selected_user),
							'post_status' => 'publish',
							'post_content' => (isset($new_listing['content']) ? $new_listing['content'] : ''),
							'post_excerpt' => (isset($new_listing['excerpt']) ? $new_listing['excerpt'] : ''),
					);
					$new_post_id = wp_insert_post($new_post_args);
					
					$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->w2dc_levels_relationships} (post_id, level_id) VALUES(%d, %d) ON DUPLICATE KEY UPDATE level_id=%d", $new_post_id, $new_listing_level->id, $new_listing_level->id));
					
					add_post_meta($new_post_id, '_listing_created', true);
					add_post_meta($new_post_id, '_order_date', time());
					add_post_meta($new_post_id, '_listing_status', 'active');
					
					if (!$new_listing_level->eternal_active_period) {
						$expiration_date = w2dc_sumDates(time(), $new_listing_level->active_days, $new_listing_level->active_months, $new_listing_level->active_years);
						add_post_meta($new_post_id, '_expiration_date', $expiration_date);
					}
					
					if (isset($new_listing['locations'])) {
						foreach ($new_listing['locations'] as $location_item) {
							if (!is_numeric($location_item)) {
								$locations_chain = array_filter(array_map('trim', explode('>', $location_item)));
								$listing_term_id = 0;
								foreach ($locations_chain as $key => $location_name) {
									if ($term = term_exists($location_name, W2DC_LOCATIONS_TAX, $listing_term_id)) {
										$term_id = intval($term['term_id']);
										$listing_term_id = $term_id;
									} else {
										if ($this->if_term_not_found == 'create') {
											if ($newterm = wp_insert_term($location_name, W2DC_LOCATIONS_TAX, array('parent' => $listing_term_id)))
											if (!is_wp_error($newterm)) {
												$term_id = intval($newterm['term_id']);
												$listing_term_id = $term_id;
											} else {
												$error = sprintf(__('line %d: ', 'W2DC') . __('Something went wrong with directory location "%s"', 'W2DC'), $n, $location_name);
												$this->log['errors'][] = $error;
												echo $error;
												echo "<br />";
											}
										} else {
											$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Directory location \"%s\" wasn't found, was skipped", 'W2DC'), $n, $location_name);
											$this->log['errors'][] = $error;
											echo $error;
											echo "<br />";
										}
									}
								}
								if ($listing_term_id)
									$new_listing['locations_ids'][] = $listing_term_id;
							} elseif (get_term($location_item, W2DC_LOCATIONS_TAX)) {
								$new_listing['locations_ids'][] = $location_item;
							} else {
								$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Directory location with ID \"%d\" wasn't found", 'W2DC'), $n, $location_item);
								$this->log['errors'][] = $error;
								echo $error;
								echo "<br />";
							}
						}
					}

					if (isset($new_listing['locations_ids']) || isset($new_listing['address_line_1'])) {
						if (isset($new_listing['locations_ids']))
							$locations_items = $new_listing['locations_ids'];
						else 
							$locations_items = $new_listing['address_line_1'];

						$locations_args = array();
						foreach ($locations_items AS $key=>$location_item) {
							if ($this->do_geocode && (!isset($new_listing['longitude'][$key]) || !isset($new_listing['latitude'][$key]))) {
								$location_string = '';
								if (isset($new_listing['locations_ids'][$key])) {
									$chain = array();
									$parent_id = $new_listing['locations_ids'][$key];
									while ($parent_id != 0) {
										if ($term = get_term($parent_id, W2DC_LOCATIONS_TAX)) {
											$chain[] = $term->name;
											$parent_id = $term->parent;
										} else
											$parent_id = 0;
									}
									$location_string = implode(', ', $chain);
								}
								if (isset($new_listing['address_line_1'][$key]))
									$location_string = $new_listing['address_line_1'][$key] . ' ' . $location_string;
								if (isset($new_listing['address_line_2'][$key]))
									$location_string = $new_listing['address_line_2'][$key] . ', ' . $location_string;
								if (isset($new_listing['zip'][$key]))
									$location_string = $location_string . ' ' . $new_listing['zip'][$key];
								if (get_option('w2dc_default_geocoding_location'))
									$location_string = $location_string . ' ' . get_option('w2dc_default_geocoding_location');
		
								$geoname = new w2dc_locationGeoname ;
								if ($result = $geoname->geonames_request(trim($location_string), 'coordinates')) {
									$new_listing['longitude'][$key] = $result[0];
									$new_listing['latitude'][$key] = $result[1];
								}
							}

							$locations_args['w2dc_location[]'][] = 1;
							$locations_args['selected_tax[]'][] = (isset($new_listing['locations_ids'][$key]) ? $new_listing['locations_ids'][$key] : 0);
							$locations_args['address_line_1[]'][] = (isset($new_listing['address_line_1'][$key]) ? $new_listing['address_line_1'][$key] : '');
							$locations_args['address_line_2[]'][] = (isset($new_listing['address_line_2'][$key]) ? $new_listing['address_line_2'][$key] : '');
							$locations_args['zip_or_postal_index[]'][] = (isset($new_listing['zip'][$key]) ? $new_listing['zip'][$key] : '');

							if (
								(!isset($new_listing['locations_ids'][$key]) && !isset($new_listing['address_line_1'][$key]) && !isset($new_listing['zip'][$key]))
								&&
								(isset($new_listing['latitude'][$key]) && isset($new_listing['longitude'][$key]))
							)
								$locations_args['manual_coords[]'][] = 1;
							else 
								$locations_args['manual_coords[]'][] = 0;

							$locations_args['map_coords_1[]'][] = (isset($new_listing['latitude'][$key]) ? $new_listing['latitude'][$key] : '');
							$locations_args['map_coords_2[]'][] = (isset($new_listing['longitude'][$key]) ? $new_listing['longitude'][$key] : '');
							$locations_args['map_zoom'] = get_option('w2dc_default_map_zoom');
							$locations_args['map_icon_file[]'][] = (isset($new_listing['map_icon_file'][$key]) ? $new_listing['map_icon_file'][$key] : '');
						}
						$args = apply_filters('w2dc_csv_save_location_args', $locations_args, $new_post_id, $new_listing);
							
						$w2dc_instance->locations_manager->saveLocations($new_listing_level, $new_post_id, $locations_args);
					}

					if (isset($new_listing['categories'])) {
						foreach ($new_listing['categories'] as $category_item) {
							$categories_chain = array_filter(array_map('trim', explode('>', $category_item)));
							$listing_term_id = 0;
							foreach ($categories_chain as $key => $category_name) {
								if ($term = term_exists($category_name, W2DC_CATEGORIES_TAX, $listing_term_id)) {
									$term_id = intval($term['term_id']);
									$listing_term_id = $term_id;
								} else {
									if ($this->if_term_not_found == 'create') {
										if ($newterm = wp_insert_term($category_name, W2DC_CATEGORIES_TAX, array('parent' => $listing_term_id)))
											if (!is_wp_error($newterm)) {
												$term_id = intval($newterm['term_id']);
												$listing_term_id = $term_id;
											} else {
												$error = sprintf(__('line %d: ', 'W2DC') . __('Something went wrong with directory category "%s"', 'W2DC'), $n, $category_name);
												$this->log['errors'][] = $error;
												echo $error;
												echo "<br />";
											}
									} else {
										$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Directory category \"%s\" wasn't found, was skipped", 'W2DC'), $n, $category_name);
										$this->log['errors'][] = $error;
										echo $error;
										echo "<br />";
									}
								}
							}
							if ($listing_term_id)
								$new_listing['categories_ids'][] = $listing_term_id;
						}
						if (isset($new_listing['categories_ids']))
							wp_set_object_terms($new_post_id, $new_listing['categories_ids'], W2DC_CATEGORIES_TAX);
					}
	
					if (isset($new_listing['tags'])) {
						foreach ($new_listing['tags'] as $tag_name) {
							if ($term = term_exists($tag_name, W2DC_TAGS_TAX))
								$new_listing['tags_ids'][] = intval($term['term_id']);
							else {
								if ($this->if_term_not_found == 'create') {
									if ($newterm = wp_insert_term($tag_name, W2DC_TAGS_TAX))
										if (!is_wp_error($newterm))
											$new_listing['tags_ids'][] = intval($newterm['term_id']);
										else {
											$error = sprintf(__('line %d: ', 'W2DC') . __('Something went wrong with directory tag "%s"', 'W2DC'), $n, $tag_name);
											$this->log['errors'][] = $error;
											echo $error;
											echo "<br />";
										}
								} else {
									$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("Directory tag \"%s\" wasn't found, was skipped", 'W2DC'), $n, $tag_name);
									$this->log['errors'][] = $error;
									echo $error;
									echo "<br />";
								}
							}
						}
						if (isset($new_listing['tags_ids']))
							wp_set_object_terms($new_post_id, $new_listing['tags_ids'], W2DC_TAGS_TAX);
					}
					
					if (isset($new_listing['content_fields'])) {
						foreach ($new_listing['content_fields'] AS $field=>$values) {
							$content_field = $w2dc_instance->content_fields->getContentFieldBySlug($field);
							$content_field->saveValue($new_post_id, $values);
						}
					}
					
					if (isset($new_listing['images'])) {
						foreach ($new_listing['images'] AS $image_item) {
							$value = explode('>', $image_item);
							$image_file_name = $value[0];
							$image_title = (isset($value[1]) ? $value[1] : '');
							if (file_exists($this->images_dir . $image_file_name)) {
								$filepath = $this->images_dir . $image_file_name;
							
								$file = array('name' => basename($filepath),
										'tmp_name' => $filepath,
										'error' => 0,
										'size' => filesize($filepath)
								);
							
								copy($filepath, $filepath . '.backup');
								$image = wp_handle_sideload($file, array('test_form' => FALSE));
								rename($filepath . '.backup', $filepath);

								if (!isset($image['error'])) {
									$attachment = array(
											'post_mime_type' => $image['type'],
											'post_title' => $image_title,
											'post_content' => '',
											'post_status' => 'inherit'
									);
									if ($attach_id = wp_insert_attachment($attachment, $image['file'], $new_post_id)) {
										require_once(ABSPATH . 'wp-admin/includes/image.php');
										$attach_data = wp_generate_attachment_metadata($attach_id, $image['file']);
										wp_update_attachment_metadata($attach_id, $attach_data);
										
										// insert attachment ID to the post meta
										add_post_meta($new_post_id, '_attached_image', $attach_id);
									} else {
										$error = sprintf(__('Image file "%s" could not be inserted.', 'W2DC'), $image_file_name);
										$this->log['errors'][] = $error;
										echo $error;
										echo "<br />";
									}
								} else {
									$error = sprintf(__("Image file \"%s\" wasn't attached. Full path: \"%s\". Error: %s", 'W2DC'), $image_file_name, $filepath, $image['error']);
									$this->log['errors'][] = $error;
									echo $error;
									echo "<br />";
								}
							} else {
								$error = sprintf(__("There isn't specified image file \"%s\" inside ZIP file. Or temp folder wasn't created: \"%s\"", 'W2DC'), $image_file_name, $this->images_dir);
								$this->log['errors'][] = $error;
								echo $error;
								echo "<br />";
							}
						}
					}
					
					if (isset($new_listing['videos'])) {
						foreach ($new_listing['videos'] AS $video_item) {
							$video_id = null;
							if (filter_var($video_item, FILTER_VALIDATE_URL) !== FALSE) {
								preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video_item, $matches_youtube);
								preg_match("#(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([‌​0-9]{6,11})[?]?.*#", $video_item, $matches_vimeo);
								if (isset($matches_youtube[0]) && strlen($matches_youtube[0]) == 11)
									$video_id = $matches_youtube[0];
								elseif (isset($matches_vimeo[5]) && strlen($matches_vimeo[5]) == 9) {
									$video_id = $matches_vimeo[5];
								} else {
									$error = sprintf(__('line %d: ', 'W2DC') . esc_attr__("YouTube or Vimeo video URL is incorrect", 'W2DC'), $n);
									$this->log['errors'][] = $error;
									echo $error;
									echo "<br />";
								}
							} else
								$video_id = $video_item;
							if ($video_id)
								add_post_meta($new_post_id, '_attached_video_id', $video_id);
						}
					}
					
					if (isset($new_listing['expiration_date'])) {
						update_post_meta($new_post_id, '_expiration_date', $new_listing['expiration_date']);
					}

					if (isset($new_listing['contact_email'])) {
						add_post_meta($new_post_id, '_contact_email', $new_listing['contact_email']);
					}

					if (get_option('w2dc_fsubmit_addon') && get_option('w2dc_claim_functionality') && $this->is_claimable) {
						add_post_meta($new_post_id, '_is_claimable', true);
					}
					
					do_action('w2dc_csv_create_listing', $new_post_id, $new_listing);
				}
			} else {
				$total_rejected_lines++;
			}
		}

		printf(__('Import finished, number of errors: %d, total rejected lines: %d', 'W2DC'), count($this->log['errors']), $total_rejected_lines);
		echo "<br />";
		echo "<br />";
	}
}

?>