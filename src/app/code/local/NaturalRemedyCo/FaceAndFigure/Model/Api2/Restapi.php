<?php
/**
 * Oauth API for React.js app to connect with
 * 
 * @author      Chris Rogers
 * @package     rwd_faceandfigure_default
 * @since       2019-01-12
*/
class NaturalRemedyCo_FaceAndFigure_Api2_Restapi extends Mage_Api2_Model_Resource {
	
	/**
	 * @return JSON
	 */
	public function _retrieve() {
		return json_encode(array("value"=>"Hello World"));
	}
	
	/**
	 * @return JSON
	 */
	public function _retrieveCollection() {
		return json_encode(array("value"=>"Hello World"));
    }
}