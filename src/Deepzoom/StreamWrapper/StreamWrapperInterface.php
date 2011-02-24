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
 * StreamWrapper Interface
 *
 * @package    Deepzoom
 * @subpackage StreamWrapper
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
interface StreamWrapperInterface {

	/**
	 * Checks whether a file exists
	 * 
	 * @param string $filename
	 * @retur bool Returns true if the file specified by filename exists; false otherwise. 
	 */
	public function exists($filename);
	
	/**
	 * Reads entire file into a string
	 * 
	 * @param string $filename
	 * @returnstring returns the file in a string
	 */
	public function getContents($filename);
	
	/**
	 * Write a string to a file
	 * 
	 * @param string $filename
	 * @param mixed $data
	 * @return bool Returns true on success or false on failure. 
	 */
	public function putContents($filename, $data);
	
	/**
     * Create directory if not exist
     * 
     * @param string $path The path being checked. 
     * @return string The path
     */
    public function ensure($path) ;
    
    /**
     * Returns information about a file path
     * 
     * @param string $path The path being checked. 
     * @return array The following associative array elements are returned: dirname, basename, extension (if any), and filename. 
     */
    public function getPathInfo($path);
    
    /**
     * Convert the path into a valid URI
     * 
     * @param string $path
     * @return string 
     */
    public function formatUri($path);
}