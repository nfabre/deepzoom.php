<?php
namespace Deepzoom\Tests\Console\Command;

use Deepzoom\Exception as dzException;
use Symfony\Components\Console\Application;
use Symfony\Components\Console\Input;
use Symfony\Components\Console\Output;

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
 * Testing Command Line
 *
 * @package    Deepzoom
 * @subpackage Test_Command
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */    
class ImageCreatorCommandTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	public function setUp()
    {
        $this->application = new Application('Deepzoom Command Line Interface', 1);
        $this->application->setCatchExceptions(false);
        $this->application->setAutoExit(false);
        $this->application->addCommand(new \Deepzoom\Console\Command\ImageCreatorCommand()); 
        $this->path = __DIR__.'/../../Fixtures/';
    }
    
    public function testRun() {
    	$stream = fopen('php://memory', 'a', false); 
    	$output = new Output\StreamOutput($stream);
        $input = new Input\ArrayInput( array('command' => 'image:creator','source' => $this->path.'hlegius.jpg','destination' =>  $this->path.'hlegius.xml')); 
        $this->application->run($input,$output);
        
        $this->assertFileExists($this->path.'hlegius.xml','-->create() Creates Deep Zoom image from source file and saves it to destination');
     }
    
     public function testInvalidCommand() {
     	$input = new Input\ArrayInput( array('command' => 'unknow-command')); 
     	try {
     	  $this->application->run($input);
     	  $this->fail('echec');
     	}catch(\InvalidArgumentException $e) {
     	  $this->assertStringStartsWith('Command',$e->getMessage());    	
     	  $this->assertStringEndsWith('is not defined.',$e->getMessage());  
     	}
     }
    public function testInvalidNoParams() {
        $input = new Input\ArrayInput( array('command' => 'image:creator')); 
        try {
          $this->application->run($input);
          $this->fail('echec');
        }catch(\RuntimeException $e) {
          $this->assertStringStartsWith('Not enough arguments.',$e->getMessage());        
        }
     }
 
    public function testInvalidParams() {
        $input = new Input\ArrayInput( array('command' => 'image:creator','dest' => 'invalid-dest')); 
        try {
          $this->application->run($input);
          $this->fail('echec');
        }catch(\InvalidArgumentException $e) {
          $this->assertStringStartsWith('The "dest" argument does not exist.',$e->getMessage());        
        }
     }
     
     public function testRuntimeException() {
       try {
      	  $input = new Input\ArrayInput( array('command' => 'image:creator','source' => null,'destination' => null)); 
          $this->application->run($input);
          $this->fail('echec');
        }catch(\RuntimeException $e) {
            $this->assertStringEndsWith("is required in order to execute this command correctly.",$e->getMessage());        
        }
        try {
          $input = new Input\ArrayInput( array('command' => 'image:creator','source' => 'source','destination' => null)); 
          $this->application->run($input);
          $this->fail('echec');
        }catch(\RuntimeException $e) {
            $this->assertStringEndsWith("is required in order to execute this command correctly.",$e->getMessage());        
        }
     } 
     
     public function testLogicException() {
        try {
          $input = new Input\ArrayInput( array('command' => 'image:creator','source' => 'source','destination' => 'destination','--tile-size' => 'a')); 
          $this->application->run($input);
          $this->fail('echec');
        }catch(\LogicException $e) {
            $this->assertStringEndsWith("must contains an integer value",$e->getMessage());        
        }
        try {
          $input = new Input\ArrayInput( array('command' => 'image:creator','source' => 'source','destination' => 'destination','--tile-overlap' => 'a')); 
          $this->application->run($input);
          $this->fail('echec');
        }catch(\LogicException $e) {
            $this->assertStringEndsWith("must contains an integer value",$e->getMessage());        
        }
        try {
          $input = new Input\ArrayInput( array('command' => 'image:creator','source' => 'source','destination' => 'destination','--tile-format' => 'bmp')); 
          $this->application->run($input);
          $this->fail('echec');
        }catch(\LogicException $e) {
            $this->assertStringEndsWith("is not supported. It should be either: jpg or png.",$e->getMessage());        
        }
     }
}