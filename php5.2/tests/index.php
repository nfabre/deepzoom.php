<?php
/**
 * Exemple
 */

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../lib'),
    get_include_path(),
)));
require 'Oz/Deepzoom/ImageCreator.php';

$converter = new Oz_Deepzoom_ImageCreator();
$converter->create( realpath('source/hlegius.jpg'), 'dest/hlegius.xml');
?>
<!DOCTYPE html>
<html>
    <head>
    	<!-- http://www.seadragon.com/developer/ajax/getting-started/ -->
        <script type="text/javascript" 
      	    src="http://seadragon.com/ajax/0.8/seadragon-min.js">
    	</script>
        <script type="text/javascript">
            var viewer = null;
            
            function init() {
                viewer = new Seadragon.Viewer("container");
                viewer.openDzi("dest/hlegius.xml");
            }
            
            Seadragon.Utils.addEvent(window, "load", init);
        </script>
        
        <style type="text/css">
            #container
            {
                width: 500px;
                height: 400px;
                background-color: black;
                border: 1px solid black;
                color: white;   /* for error messages, etc. */
            }
        </style>

    </head>

    <body>
        <div id="container"></div>
    </body>
</html>			
