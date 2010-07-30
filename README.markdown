# PHP Deep Zoom Tools
## Installation
Just download

## Test
To run the Deepzoom test suite, you need PHPUnit 3.5 or later.


## TODOS
* Refactoring & PHP 5.3.2
* Use Dependency Injection (Add Descriptor Interface and Image Adapter Interface)
* Add "CollectionCreator"                       
* Add Stream Support, you can create Deep Zoom on Amazon S3 or Microsoft Azure Storage

Installation
============
  1. Add the Deep Zoom Tools to your project as Git submodules:

        $ git submodule add git://github.com/nfabre/deepzoom.php.git src/vendor/deepzoom

  2. Exemple:

        $deep = new ImageCreator(new File(),new Descriptor(new File()),new Imagick());
        
        $deep->create(realpath('my/image.jpg'), 'my/deepzoom.dzi');