<?php
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../../lib'),
    get_include_path(),
)));
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'Oz/Deepzoom/DescriptorTest.php';

/**
 * Static test suite.
 */
class DeepzoomSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'DeepzoomSuite' );
		$this->addTestSuite ( 'Oz_Deepzoom_DescriptorTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

