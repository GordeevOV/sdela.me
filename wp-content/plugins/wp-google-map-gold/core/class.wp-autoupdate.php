<?php
/**
 * Auto Update notification Class File.
 * @author flippercode
 * @package Maps
 * @version 3.2.7
 */

/**
 * Auto Update notification Class.
 * @author flippercode
 * @package Maps
 * @version 3.2.7
 */
class WPGMP_Auto_Update
{
	/**
	 * Plugin's current version
	 * @var string
	 */
	public $wsq_current_version;

	/**
	 * Plugin's remote path
	 * @var string
	 */
	public $wsq_remote_path;

	/**
	 * Plugin's Slug
	 * @var string
	 */
	public $wsq_slug;

	/**
	 * Initialize a new instance of the Auto-Update class.
	 */
	function __construct() {

		$this->wsq_current_version = WPGMP_VERSION;
		$this->wsq_remote_path = 'http://www.flippercode.com/update.php';
		$this->wsq_plugin_file = WPGMP_FOLDER.'/'.WPGMP_FOLDER.'.php';
		$this->wsq_slug = WPGMP_FOLDER;

		// Check for plugin updates.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_updates' ),10,1 );

		// Set the response.
		add_filter( 'plugins_api', array( $this, 'get_updates_info' ), 10, 3 );
	}

	/**
	 * Add our plugin to the filter transient.
	 * @param  object $transient Transient Object.
	 * @return object            Transient Object.
	 */
	public function check_updates($transient) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Check and Get remote version.
		$response = wp_remote_post( $this->wsq_remote_path, array( 'body' => array( 'action' => 'version', 'plugin' => $this->wsq_slug ) ) );
		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$remote_version = $response['body'];

			// If update is available, set the transient.
			if ( version_compare( $this->wsq_current_version, $remote_version, '<' ) ) {
				$obj = new stdClass();
				$obj->slug = $this->wsq_slug;
				$obj->new_version = $remote_version;
				$obj->url = $this->wsq_remote_path;
				$obj->package = $this->wsq_remote_path;
				$transient->response[ $this->wsq_plugin_file ] = $obj;
			}
		}
		return $transient;
	}

	/**
	 * Get Plugins Update Information
	 * @param  bool   $false  Get Info or Not.
	 * @param  string $action Action Type.
	 * @param  array  $arg    Arguments.
	 * @return array         New Updates Information.
	 */
	public function get_updates_info($false, $action, $arg) {

		if ( $arg->slug === $this->wsq_slug ) {

			$response = wp_remote_post( $this->wsq_remote_path, array( 'body' => array( 'action' => 'info', 'plugin' => $this->wsq_slug ) ) );
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$info = unserialize( $response['body'] );
				return $info;
			}
			return false;
		}
		return false;
	}
}
new WPGMP_Auto_Update();
?>
