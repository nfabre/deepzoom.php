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
 * Image Creator, generate pyramid
 *
 * @package    Deepzoom
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
abstract class AbstractCreator {
    /**
     * Tile size
     * 
     * @var string
     */ 
    protected $_tileSize; 

    /**
     * Tile format
     * 
     * @var string
     */ 
    protected $_tileFormat; 
        
    /**
     * Ensures that $val is between the limits set by $min and $max.
     *
     * @param int $val
     * @param int $min
     * @param int $max
     * 
     * @return int
     */ 
    protected function _clamp($val, $min, $max) {
        if($val < $min) {
            return $min;
        }elseif($val > $max) {
            return $max;
        }
        return $val;
    } 
}