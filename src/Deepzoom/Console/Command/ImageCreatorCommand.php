<?php
namespace Deepzoom\Console\Command;

use Symfony\Components\Console\Input\InputArgument,
    Symfony\Components\Console\Input\InputOption,
    Symfony\Components\Console\Command\Command,
    Symfony\Components\Console,
    Deepzoom\ImageCreator,
    Deepzoom\Exception as dzException,
    Deepzoom\ImageAdapter\GdThumb,
    Deepzoom\Descriptor;
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
 * Command line
 *
 * @package    Deepzoom
 * @subpackage Console_Command
 * @author     Nicolas Fabre <nicolas.fabre@gmail.com>
 */
class ImageCreatorCommand extends Command {
	/**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('image:creator')
        ->setDescription('Convert one image into the Deep Zoom Image (DZI) file format.')
        ->setDefinition(array(
            new InputArgument('source', InputArgument::REQUIRED, 'Image path'),
            new InputArgument('destination', InputArgument::REQUIRED, 'Set the destination of the output'),
            new InputOption(
                'tile-size', 's', InputOption::PARAMETER_OPTIONAL,
                'Size of the tiles. Default: 254',
                254
            ),
            new InputOption(
                'tile-format', 'f', InputOption::PARAMETER_OPTIONAL,
                'Image format of the tiles (jpg or png). Default: jpg',
            	'jpg'
            ),
            new InputOption(
                'tile-overlap', 'o', InputOption::PARAMETER_OPTIONAL,
                'Overlap of the tiles in pixels (0-10). Default: 1',
                1
            ),
        ))
        ->setHelp(<<<EOT
Convert one image into the Deep Zoom Image (DZI) file format.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        //$em = $this->getHelper('em')->getEntityManager();

        if (($source = $input->getArgument('source')) === null) {
            throw new \RuntimeException("Argument 'source' is required in order to execute this command correctly.");
        }
        
    	if (($destination = $input->getArgument('destination')) === null) {
            throw new \RuntimeException("Argument 'destination' is required in order to execute this command correctly.");
        }

        $tileSize = $input->getOption('tile-size');
        if ( ! is_numeric($tileSize)) {
            throw new \LogicException("Option 'tile-size' must contains an integer value");
        }
        
   		$tileOverlap = $input->getOption('tile-overlap');
        if ( ! is_numeric($tileOverlap)) {
            throw new \LogicException("Option 'tile-overlap' must contains an integer value");
        }

        $tileFormat = $input->getOption('tile-format');
        if ( ! in_array($tileFormat,array('jpg','png'))) {
            throw new \LogicException(
                "Tile format  '$tileFormat' is not supported. It should be either: jpg or png."
            );
        }
		try {		
        	$deep = new ImageCreator(new Descriptor(),new GdThumb(),$tileSize,$tileOverlap,$tileFormat);
        	$deep->create(realpath($source),$destination);
        	$output->doWrite('Image converted into the Deep Zoom Image (DZI) file format',true);
		} catch(dzException $e) {
			// @codeCoverageIgnoreStart
			$output->doWrite($e->getMessage(),true);	
		}
		// @codeCoverageIgnoreEnd
    }
}