<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests\StreamWrapper\Amazon;

use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\Amazon\S3;

/**
 * Testing Amazon S3 Wrapper
 *
 * @package    Deepzoom
 * @subpackage Test_StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class S3Test extends \PHPUnit_Framework_TestCase
{
	protected static $blobConfig;
	protected static $blobStorage;
	
    static public function setUpBeforeClass() {
    	self::$blobConfig = array(
           'container'       => DEEPZOOM_TESTSUITE_STREAMWRAPPER_CONTAINER,     
           'accessKey'       => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AMAZON_S3_ACCESSKEY,     
           'secretAccessKey' => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AMAZON_S3_SECRETACCESSKEY,     
           'name'            => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AMAZON_S3_NAME,     
        );
        if(!empty(self::$blobConfig['accessKey']) && !empty(self::$blobConfig['secretAccessKey']) ) {   
	        self::$blobStorage = new \Zend_Service_Amazon_S3(self::$blobConfig['accessKey'],self::$blobConfig['secretAccessKey']);
	        if(!self::$blobStorage->isBucketAvailable(self::$blobConfig['container'])){
	            self::$blobStorage->createBucket(self::$blobConfig['container']);
	        }
	          	
	        self::$blobStorage->putObject(self::$blobConfig['container'].'/mypath/model1.xml', file_get_contents(__DIR__.'/../../Fixtures/model1.xml'),
	            array(\Zend_Service_Amazon_S3::S3_ACL_HEADER => \Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ)
	        );
        }
    }

    public function setUp()
    {
    	if(empty(self::$blobConfig['accessKey']) || empty(self::$blobConfig['secretAccessKey']) ) {
        	$this->markTestSkipped('Amazon S3 is not properly configured');	
        }
        $this->path = 'mypath';
    }
    
    protected function getWrapperInstance() {
    	return new S3(self::$blobStorage,self::$blobConfig['container'],self::$blobConfig['name']);
    }
    
    public function testConstructor()
    {
        $blobWrapper = $this->getWrapperInstance();
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface',$blobWrapper, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperAbstract',$blobWrapper, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\Amazon\S3',$blobWrapper, '__construct()'); 
    }
    
    public function testEnsure() {
    	$blobWrapper = $this->getWrapperInstance();
        $result = $blobWrapper->ensure($this->path.'NOTExist');
        $this->assertEquals($this->path.'NOTExist', $result);
    }
    
     public function testGetPrefix() {
        $blobWrapper = $this->getWrapperInstance();
        $blobConfig = self::$blobConfig;
     	$this->assertEquals("{$blobConfig['name']}://{$blobConfig['container']}/", $blobWrapper->getPrefix());	
     }
     
     public function testExistDirectoryNotExist() {
        $blobWrapper = $this->getWrapperInstance();
        $this->assertFalse($blobWrapper->exists($this->path.'/model2.xml'));    
     }
     
     public function testExist() {
     	$blobWrapper = $this->getWrapperInstance();
        $this->assertTrue($blobWrapper->exists($this->path.'/model1.xml'));    
     }
     
     public function testGetPathInfo() {
     	/**
		 * @var $blobWrapper Deepzoom\StreamWrapper\Amazon\S3
     	 */
        $blobWrapper = $this->getWrapperInstance();
     	$infos = $blobWrapper->getPathInfo($this->path.'/model1.xml');
        
        $this->assertInternalType('array', $infos);
        $this->assertArrayHasKey('dirname',$infos);
        $this->assertArrayHasKey('basename',$infos);
        $this->assertArrayHasKey('extension',$infos);
        $this->assertArrayHasKey('filename',$infos);
     }
     
    public function testGetContents() {
         $blobWrapper = $this->getWrapperInstance();
         $contents = $blobWrapper->getContents($this->path.'/model1.xml');
         $this->assertGreaterThan(0, sizeof($contents));
     }
      
    public function testPutAndGetContents() {
         $blobWrapper = $this->getWrapperInstance();
         $result = $blobWrapper->putContents($this->path.'/testFile.xml','myText');
         $this->assertTrue($result);  
         $contents = $blobWrapper->getContents($this->path.'/testFile.xml'); 
         $this->assertEquals('myText', $contents);
         $this->assertGreaterThan(0, sizeof($contents));
     }
}