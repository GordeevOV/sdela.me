<?php
/**
 *  Export-Import Records in csv.
 *  @package Maps
 *  @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'FlipperCode_Export_Import' ) ) {

	/**
	 * Import/Export Class
	 */
	class FlipperCode_Export_Import {
		/**
		* Header Columns
		* @var array
		*/
		var $columns = array();
		/**
		* Array of records
		* @var array
		*/
		var $data = array();
		/**
		 * Intialize Importer Object.
		 * @param array $columns  Header Columns.
		 * @param array $data   Records Data.
		 */
		public function __construct($columns = array(), $data = array()) {
			$this->columns = $columns;
			$this->data = $data;
		}
		/**
		 * Export CSV
		 * @param  string $action     File type.
		 * @param  [type] $asFilename File name.
		 */
		function export($action, $asFilename) {

			if ( 'csv' == $action ) {

				header( 'Content-Type: text/csv' );
				header( 'Content-Disposition: attachment;filename="'.$asFilename.'.csv"' );
				$fp = fopen( 'php://output', 'w' );

				if ( ! empty( $this->data ) ) {
					$csv_array = $this->columns;
					fputcsv( $fp, $csv_array );
					foreach ( $this->data as $key => $result ) {
						fputcsv( $fp, array_values( $result ), ',', '"' );
					}
				}

				fclose( $fp );

			}
		}
		/**
		 * Read csv file.
		 * @param  string $action   File Type.
		 * @param  string $filename File name.
		 * @param  string $delimiter CSV Delimiter.
		 * @return array           File Data.
		 */
		function import($action, $filename,$delimiter = ',') {
			global $_FILES;

			$file_data = array();
			$file_datas = array();
			if ( 'csv' == $action ) {
				ini_set( 'auto_detect_line_endings', true );
				$row = 1;

				if ( ($handle = fopen( $_FILES[ $filename ]['tmp_name'], 'r' )) !== false ) {
					while ( ($data = fgetcsv( $handle,1000,$delimiter )) !== false ) {
						$num = count( $data );

						++$row;
						for ( $c = 0; $c < $num; ++$c ) {
							$data[ $c ]."<br />\n";
						}

						$file_data[] = $data;
					}

					fclose( $handle );

				}
			}
			
			if ( ! empty( $file_data ) and  empty( $file_datas ) ) {
				foreach ( $file_data[0] as $i => $key ) {
					$keys[] = $key;
				}
				$core_fields = array(
							'title',
							'address',
							'latitude',
							'longitude',
							'city',
							'state',
							'country',
							'postal-code',
							'message',
							'categories',
							);
				foreach ( $file_data as $i => $data ) {
					if ( 0 == $i ) {
						continue;
					}
					foreach ( $data as $d => $value ) {
						if ( in_array( sanitize_title( $keys[ $d ] ), $core_fields ) ) {
							$file_datas[ $i -1 ][ sanitize_title( $keys[ $d ] ) ] = $value;
						} else { 						$file_datas[ $i -1 ][ $keys[ $d ] ] = $value; }
					}
				}
			}
			return $file_datas;
		}
	}
}
