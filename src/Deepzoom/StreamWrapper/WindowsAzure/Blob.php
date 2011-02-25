<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\StreamWrapper\WindowsAzure;

use Deepzoom\StreamWrapper\StreamWrapperAbstract;

class Blob extends StreamWrapperAbstract  {
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
     * @param Zend_Service_WindowsAzure_Storage_Blob $blobStorage
     * @param string $container
     * @param string $name
     */
    public function __construct(\Zend_Service_WindowsAzure_Storage_Blob $blobStorage, $container,$name = 'azure') {
        $this->_container = $container;
        $this->_name = $name;
        $this->_storageClient = $blobStorage;
        $existed = in_array('azure', stream_get_wrappers());
        if ($existed === false) {
            $this->_storageClient->registerStreamWrapper();
        }
    }
    
    /**
     * Convert the file path into a valid Windows Azure URI
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
     * Convert the file path into a valid Windows Azure URI
     * 
     * @param string $path
     * @return string 
     */
    public function formatUri($path) {
        return  $this->getPrefix().str_replace('\\','/',$path); 
    }
}