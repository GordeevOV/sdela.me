<?php
/**
 * Manage integration with WPML.
 * @package GeoMashup
 * @since 1.9.0
 */

class GeoMashupWPML {

	/**
	 * Load WPML integrations.
	 * @since 1.9.0
	 */
	public static function load() {
		add_filter( 'geo_mashup_get_language_code', array( __CLASS__, 'get_language_code' ) );
		add_filter( 'geo_mashup_locations_join', array( __CLASS__, 'augment_locations_join_clause' ), 10, 2 );
		add_filter( 'geo_mashup_locations_where', array( __CLASS__, 'augment_locations_where_clause' ), 10, 2 );
		add_filter( 'geo_mashup_results_page_id', array( __CLASS__, 'translate_results_page_id' ) );
	}

	/**
	 * Use WPML's language code unless the lang querystring parameter is present.
	 * @since 1.9.0
	 * @param string $code
	 * @return string
	 */
	public static function get_language_code( $code ) {
		return isset( $_GET['lang'] ) ? $_GET['lang'] : ICL_LANGUAGE_CODE;
	}

	/**
	 * Add WPML tables to post location query join clause, changing posts table references to our alias.
	 * @since 1.9.0
	 * @param string $join
	 * @param array $query_args
	 * @return string
	 */
	public static function augment_locations_join_clause( $join, $query_args ) {
		global $wpdb, $wpml_query_filter;

		if ( self::suppress_query_filters() ) {
			return $join;
		}

		if ( 'post' != $query_args['object_name'] ) {
			return $join;
		}

		// Apply post query filters,
		$join = $wpml_query_filter->filter_single_type_join( $join, 'any' );
		return str_replace( $wpdb->posts . '.', 'o.', $join );
	}

	/**
	 * Add WPML conditions to post location query where clause, changing posts table references to our alias.
	 *
	 * Also removes an interfering WPML filter.
	 * @since 1.9.0
	 * @param string $where
	 * @param array $query_args
	 * @return string
	 */
	public static function augment_locations_where_clause( $where, $query_args ) {
		global $wpdb, $wpml_query_filter;

		if ( self::suppress_query_filters() ) {
			return $where;
		}

		if ( 'post' != $query_args['object_name'] ) {
			return $where;
		}

		$where = $wpml_query_filter->filter_single_type_where(
			$where,
			$GLOBALS['geo_mashup_options']->get( 'overall', 'located_post_types' )
		);
		$where = str_replace( $wpdb->posts . '.', 'o.', $where );

		remove_filter( 'get_translatable_documents', array( __CLASS__, 'wpml_filter_get_translatable_documents' ) );

		return $where;
	}

	/**
	 * @since 1.10.0
	 * @param int $page_id
	 * @return int
	 */
	public static function translate_results_page_id( $page_id ) {
		return apply_filters( 'wpml_object_id', $page_id, 'page' );
	}

	/**
	 * Whether WPML post location query filters have been suppressed.
	 * @since 1.9.0
	 * @return bool
	 */
	protected static function suppress_query_filters() {
		return defined( 'GEO_MASHUP_SUPPRESS_POST_FILTERS' ) && GEO_MASHUP_SUPPRESS_POST_FILTERS;
	}
}