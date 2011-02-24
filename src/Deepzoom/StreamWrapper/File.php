<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom\StreamWrapper;

/**
 * File Management
 *
 * @package    Deepzoom
 * @subpackage StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class File extends StreamWrapperAbstract  {
    /**
     * Create directory if not exist
     * 
     * @param string $path The path being checked. 
     * @param int mode
     * @return string The path
     */
    public function ensure($path) {
    	$mode=0755;	 
    	if(!file_exists($path)) {
            mkdir($path, $mode, true);
        }
        
        return $path;
    }
    
    /**
     * Convert the file path into a valid File URI
     * 
     * @param string $path
     * @return string 
     */
    public function formatUri($path) {
        return  $path;
    }
}