<?php

/*
 * This file is part of the Deepzoom.php package.
 *
 * (c) Nicolas Fabre <nicolas.fabre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deepzoom;

/**
 * Descriptor Interface
 *
 * @package    Deepzoom
 * @subpackage Descriptor
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
interface DescriptorInterface {
	/**
	 * Number of levels in the pyramid
	 *
	 * @return int
	 */
	public function getNumLevels();
	
	/**
	 * Dimensions of level (width, height)
	 *
	 * @param int $level
	 * @return array
	 * @throw Deepzoom\Exception check pyramid level
	 */
	public function getDimension($level);
	
	/**
	 * Number of tiles (columns, rows)
	 *
	 * @param int $level
	 * @return array
	 * @throw Deepzoom\Exception check pyramid level
	 */
	public function getNumTiles($level);
	
	/**
     * Save descriptor file
     *
     * @param string $source
     * 
     * @return Deepzoom\Descriptor Fluent interface
     */ 
	public function save($destination);
}