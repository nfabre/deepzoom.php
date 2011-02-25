<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests\StreamWrapper\WindowsAzure;

use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\WindowsAzure\Blob;

/**
 * Testing Windows Azure Blob Wrapper
 *
 * @package    Deepzoom
 * @subpackage Test_StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class BlobTest extends \PHPUnit_Framework_TestCase
{
	protected static $blobConfig;
	protected static $blobStorage;
	
    static public function setUpBeforeClass()
    {
    	self::$blobConfig = array(
           'container'  => DEEPZOOM_TESTSUITE_STREAMWRAPPER_CONTAINER,     
           'host'       => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AZURE_HOST,     
           'account'    => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AZURE_ACCOUNT,     
           'credencial' => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AZURE_CREDENCIAL,     
           'name'       => DEEPZOOM_TESTSUITE_STREAMWRAPPER_AZURE_NAME,    
        );
        
       
        
        self::$blobStorage = new \Zend_Service_WindowsAzure_Storage_Blob(self::$blobConfig['host'],self::$blobConfig['account'],self::$blobConfig['credencial']);
        if(!empty(self::$blobConfig['account']) && !empty(self::$blobConfig['credencial']) ) {     
	        if(!self::$blobStorage->containerExists(self::$blobConfig['container'])){
	            self::$blobStorage->createContainer(self::$blobConfig['container']);
	            self::$blobStorage->setContainerAcl(self::$blobConfig['container'], \Zend_Service_WindowsAzure_Storage_Blob::ACL_PUBLIC);    
	        }
	        self::$blobStorage->putBlob(self::$blobConfig['container'], 'mypath/model1.xml', __DIR__.'/../../Fixtures/model1.xml' );
    
        }
    }
    
    public function setUp()
    {
    	if(empty(self::$blobConfig['account']) || empty(self::$blobConfig['credencial']) ) {
        	$this->markTestSkipped('Windows Azure is not properly configured');	
        }
        $this->path = 'mypath';
    }
    
    protected function getWrapperInstance() {
    	return new Blob(self::$blobStorage,self::$blobConfig['container'],self::$blobConfig['name']);
    }
    
    public function testConstructor()
    {
        $blobWrapper = $this->getWrapperInstance();
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface',$blobWrapper); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperAbstract',$blobWrapper); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\WindowsAzure\Blob',$blobWrapper); 
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
     
     public function testGetPathInfo() {
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
         $result = $blobWrapper->putContents($this->path.'testFile.xml','myText');
         $this->assertTrue($result);  
         $contents = $blobWrapper->getContents($this->path.'testFile.xml'); 
         $this->assertEquals('myText', $contents);
         $this->assertGreaterThan(0, sizeof($contents));
     }
}