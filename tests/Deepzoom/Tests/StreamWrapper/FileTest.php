<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\Tests\StreamWrapper;

use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\File;

/**
 * Testing File Wrapper
 *
 * @package    Deepzoom
 * @subpackage Test_StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
    	$this->path = __DIR__.'/..'.DEEPZOOM_TESTSUITE_FIXTURES_PATH;
    	
      if(file_exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist')) {
        rmdir(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist');
      }
      if(file_exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'testFile.xml')) {
      	unlink(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'testFile.xml');
      }
      $this->_fileWrapper = new File();;
    }
    
    public function testConstructor()
    {
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface',$this->_fileWrapper); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperAbstract',$this->_fileWrapper); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\File',$this->_fileWrapper); 
    }
    
    public function testCreatedDirectoryIfNotExist() {
    	$result = $this->_fileWrapper->ensure(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist');
        $this->assertEquals(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist', $result);
        $this->assertFileExists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist');	
        $this->assertStringEndsWith('755', substr(sprintf('%o', fileperms(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist')), -4));
    }
    
    public function testEnsureDirectoryAlreadyExsit() {
        $result = $this->_fileWrapper->ensure(DEEPZOOM_TESTSUITE_DESTINATION_PATH);
        $this->assertEquals(DEEPZOOM_TESTSUITE_DESTINATION_PATH, $result);
    }

     public function testFormatUri() {
         $this->assertEquals('tmp', $this->_fileWrapper->formatUri('tmp'));	
     }
     
     public function testExistDirectoryNotExist() {
        $this->assertFalse($this->_fileWrapper->exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'NOTExist'));    
     }
     
     public function testExistDirectoryExist() {
        $this->assertTrue($this->_fileWrapper->exists(DEEPZOOM_TESTSUITE_DESTINATION_PATH));    
     }
     
     public function testGetPathInfo() {
        $infos = $this->_fileWrapper->getPathInfo($this->path.'model1.xml');
        
        $this->assertInternalType('array', $infos);
        $this->assertArrayHasKey('dirname',$infos);
        $this->assertArrayHasKey('basename',$infos);
        $this->assertArrayHasKey('extension',$infos);
        $this->assertArrayHasKey('filename',$infos);
     }
     
     public function testGetContents() {
         $contents = $this->_fileWrapper->getContents($this->path.'model1.xml');
         $this->assertGreaterThan(0, sizeof($contents));
     }
     
    public function testPutContents() {
         $result = $this->_fileWrapper->putContents(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'testFile.xml','myText');
         $this->assertTrue($result);  
         $contents = $this->_fileWrapper->getContents(DEEPZOOM_TESTSUITE_DESTINATION_PATH.'testFile.xml'); 
         $this->assertEquals('myText', $contents);
     }
}