<?php
namespace Deepzoom\Tests\StreamWrapper;

use Deepzoom\Exception as dzException;
use Deepzoom\StreamWrapper\File;

/**
* Deep Zoom Tools
*
* Copyright (c) 2008-2010, OpenZoom <http://openzoom.org/>
* Copyright (c) 2008-2010, Nicolas Fabre <nicolas.fabre@gmail.com>
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without modification,
* are permitted provided that the following conditions are met:
*
* 1. Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
*
* 2. Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in the
* documentation and/or other materials provided with the distribution.
*
* 3. Neither the name of OpenZoom nor the names of its contributors may be used
* to endorse or promote products derived from this software without
* specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
* ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
* ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Testing File Wrapper
 *
 * @package    Deepzoom
 * @subpackage Test_StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    static public function setUpBeforeClass()
    {
    }

    public function setUp()
    {
      $this->path = __DIR__.'/../Fixtures/';
      if(file_exists($this->path.'NOTExist')) {
        rmdir($this->path.'NOTExist');
      }
      if(file_exists($this->path.'testFile.xml')) {
      	unlink($this->path.'testFile.xml');
      }
    }
    
    protected function tearDown()
    {
    }
    
    public function testConstructor()
    {
        $fileWrapper = new File();	
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperInterface',$fileWrapper, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\StreamWrapperAbstract',$fileWrapper, '__construct()'); 
        $this->assertInstanceOf('Deepzoom\StreamWrapper\File',$fileWrapper, '__construct()'); 
    }
    
    public function testEnsureDirectoryNotExist() {
        $fileWrapper = new File();  
        $result = $fileWrapper->ensure($this->path.'NOTExist');
        $this->assertFileExists($this->path.'NOTExist');	
        $this->assertEquals($this->path.'NOTExist', $result);
    }
    
    public function testEnsureDirectoryAlreadyExsit() {
        $fileWrapper = new File();  
        $result = $fileWrapper->ensure($this->path);
        $this->assertEquals($this->path, $result);
    }

     public function testFormatUri() {
         $fileWrapper = new File();
         $this->assertEquals('tmp', $fileWrapper->formatUri('tmp'));	
     }
     
     public function testExistDirectoryNotExist() {
     	$fileWrapper = new File();  
        $this->assertFalse($fileWrapper->exists($this->path.'NOTExist'));    
     }
     
     public function testExistDirectoryExist() {
        $fileWrapper = new File();  
        $this->assertTrue($fileWrapper->exists($this->path));    
     }
     
     public function testGetPathInfo() {
        $fileWrapper = new File();  
        $infos = $fileWrapper->getPathInfo($this->path.'model1.xml');
        
        $this->assertType('array', $infos);
        $this->assertArrayHasKey('dirname',$infos);
        $this->assertArrayHasKey('basename',$infos);
        $this->assertArrayHasKey('extension',$infos);
        $this->assertArrayHasKey('filename',$infos);
     }
     
     public function testGetContents() {
     	 $fileWrapper = new File();  
         $contents = $fileWrapper->getContents($this->path.'model1.xml');
         $this->assertGreaterThan(0, sizeof($contents));
     }
     
    public function testPutContents() {
         $fileWrapper = new File();  
         $result = $fileWrapper->putContents($this->path.'testFile.xml','myText');
         $this->assertTrue($result  );  
         $contents = $fileWrapper->getContents($this->path.'testFile.xml'); 
         $this->assertEquals('myText', $contents);
     }
}