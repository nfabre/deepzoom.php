<?php
namespace Deepzoom\Gearman;

use Deepzoom\ImageCreator as baseImageCreator;

class ImageCreator extends baseImageCreator {
	/**
     * Returns the bitmap image at the given level
     *
     * @param int $level
     * 
     * @return ?
     * @throw Deepzoom\Exception check pyramid level
     */ 
    public function getImage($level) {
        if(0 <= $level && $level < $this->_descriptor->getNumLevels()) {
            $dimension = $this->_descriptor->getDimension($level);
            // don't transform to what we already have
            if($this->_descriptor->getWidth() == $dimension['width'] and $this->_descriptor->getHeight() == $dimension['height']) {
                return $this->_imageAdapter;
            }
            else {
                $image = clone $this->_imageAdapter;
                return $image->resize($dimension['width'],$dimension['height']);
            }
        } else throw new Exception('Invalid pyramid level');
    } 
    
    public function getImageTmp($level) {
        if(0 <= $level && $level < $this->_descriptor->getNumLevels()) {
            $dimension = $this->_descriptor->getDimension($level);
            if($this->_descriptor->getWidth() == $dimension['width'] and $this->_descriptor->getHeight() == $dimension['height']) {
                $image = $this->_imageAdapter;	
            }
            else {
                $image = clone $this->_imageAdapter;
                $image->resize($dimension['width'],$dimension['height']);
            }
                $tmpImg = $this->_dirName .'/tmp/';
                $this->_ensure($tmpImg);
                $image->save($tmpImg.$level.'.'.$this->_tileFormat);  
                return $tmpImg.$level.'.'.$this->_tileFormat;   
        } else throw new Exception('Invalid pyramid level');
    } 
            
    
    /**
     * Creates Deep Zoom image from source file and saves it to destination
     *
     * @param string $source
     * @param string $destination
     * @throw Deepzoom\Exception check source existe and destination is writable
     */ 
    public function  create($source,$destination) {
        $this->_imageAdapter->setSource($source);
        $dimensions = $this->_imageAdapter->getDimensions();
        $this->_descriptor->setWidth($dimensions['width'])
                          ->setHeight($dimensions['height'])
                          ->setTileSize($this->_tileSize)
                          ->setTileOverlap($this->_tileOverlap)
                          ->setTileFormat($this->_tileFormat);
        $aImage = pathinfo($destination);
        /**
        * @todo secure variables
        */
        $imageName = $aImage['filename'];
        $this->_dirName = $dirName = $aImage['dirname'];
        $imageFile = $this->_ensure($dirName.DIRECTORY_SEPARATOR.$imageName.'_files');
        
        $client = new \GearmanClient();
        $client->addServer('web2.groupereflect.net');
        
        foreach (range(0,$this->_descriptor->getNumLevels() - 1) as $level) {
        	$levelFile = $this->getImageTmp($level);     
            $handle = $client->doBackground("deepzoom-by-level",serialize(array('level' => $level,'imageFile' => $imageFile, 'file' => $levelFile,'dimension' => $dimensions)));
        }
        $this->_descriptor->save($destination);
    }
    
    public function createLevel($level,$imageFile,$source,$dimensions) {
        $levelDir = $this->_ensure($imageFile.DIRECTORY_SEPARATOR.$level);
        $this->_descriptor->setWidth($dimensions['width'])
                          ->setHeight($dimensions['height'])
                          ->setTileSize($this->_tileSize)
                          ->setTileOverlap($this->_tileOverlap)
                          ->setTileFormat($this->_tileFormat);
             $this->_imageAdapter->setSource($source);
             $format = $this->_descriptor->getTileFormat();
             
             $tiles = $this->_descriptor->getNumTiles($level);
             foreach (range(0, $tiles['columns'] - 1) as $column) {
                foreach (range(0, $tiles['rows'] - 1) as $row) {
                    $bounds = $this->_descriptor->getTileBounds($level,$column,$row);
                    $cropLevelImage = clone $this->_imageAdapter;
                    $cropLevelImage->crop($bounds['x'],$bounds['y'],$bounds['width'],$bounds['height']);
                    $tilePath = $levelDir.DIRECTORY_SEPARATOR.sprintf('%s_%s.%s',$column,$row,$format);
                    $cropLevelImage->save($tilePath);
                    unset($cropLevelImage);
                }
             }	
    }
}