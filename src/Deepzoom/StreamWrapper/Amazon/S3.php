<?php
namespace Deepzoom\StreamWrapper\Amazon;

use  Deepzoom\StreamWrapper\StreamWrapperAbstract;
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

class S3 extends StreamWrapperAbstract  {
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
     * @param Zend_Service_Amazon_S3 $blobStorage
     * @param string $container
     * @param string $name
     */
    public function __construct(\Zend_Service_Amazon_S3 $blobStorage, $container,$name = 's3') {
        $this->_container = $container;
        $this->_name = $name;
        $this->_storageClient = $blobStorage;
        $existed = in_array('s3', stream_get_wrappers());
        if ($existed === false) {
            $this->_storageClient->registerStreamWrapper();
        }
    }
    
    /**
     * Convert the file path into a valid Amazon S3 URI
     *
     * @return string 
     */
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
    
    /**
     * Convert the file path into a valid Amazon S3 URI
     * 
     * @param string $path
     * @return string 
     */
    public function formatUri($path) {
        return  $this->getPrefix().str_replace('\\','/',$path); 
    }
}