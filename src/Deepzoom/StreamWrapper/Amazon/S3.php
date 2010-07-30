<?php
namespace Deepzoom\StreamWrapper\Amazon;

use  Deepzoom\StreamWrapper\StreamWrapperInterface;
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


class S3 implements StreamWrapperInterface {
    /**
     * 
     * @var $_container string
     */
    protected $_container;
    
    /**
     * @var $_name string
     */
    protected $_name;
    
    /**
     * @var $_storageClient Zend_Service_WindowsAzure_Storage_Blob
     */
    protected $_storageClient;
    
    /**
     * 
     * @param string $container
     * @param string $name
     */
    public function __construct($container,$name = 's3',$accessKey=null, $secretKey=null, $region=null) {
        $this->_container = $container;
        $this->_name = $name;
        $this->_storageClient = new \Zend_Service_Amazon_S3($accessKey, $secretKey, $region);
        $this->_storageClient->registerStreamWrapper();
    }
    /**
     * Checks whether a file exists
     * 
     * @param string $filename
     * @retur bool Returns true if the file specified by filename exists; false otherwise. 
     */
    public function exists($filename) {
        return file_exists($this->formatUri($filename));
    }
    
    /**
     * Reads entire file into a string
     * 
     * @param string $filename
     * @returnstring returns the file in a string
     */
    public function getContents($filename){
        return file_get_contents($this->formatUri($filename));
    }
    
    /**
     * Write a string to a file
     * 
     * @param string $filename
     * @param mixed $data
     * @return bool Returns true on success or false on failure. 
     */
    public function putContents($filename, $data) {
        $result = file_put_contents($this->formatUri($filename), $data);
        
        return $result > 0 ? true : false;
    }
    
    /**
     * Returns information about a file path
     * 
     * @param string $path The path being checked. 
     * @return array The following associative array elements are returned: dirname, basename, extension (if any), and filename. 
     */
    public function getPathInfo($path) {
        return pathinfo($path);
    }
    
    public function getPrefix() {
        return "{$this->_name}://{$this->_container}/";
    }
    
    /**
     * Create directory if not exist
     * 
     * @param string $path The path being checked. 
     * @return string The path
     */
    public function ensure($path) {
        return $path;
    }
    
    protected function formatUri($path) {
        return  $this->getPrefix().str_replace('\\','/',$path); 
    }
}