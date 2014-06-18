<?php

namespace Ant\PhotoRestBundle\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Gaufrette\Filesystem;
use Ant\ImageResizeBundle\Image\Resizer;

class PhotoUploader
{
    private static $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');

    private $filesystem;
    private $resizer;
    private $thumnailsSizes;
    private $cacheControl;

    public function __construct(Filesystem $filesystem, Resizer $resizer, $thumnailsSizes, $cacheControl)
    {
        $this->filesystem = $filesystem;
        $this->resizer = $resizer;
        $this->thumnailsSizes = $thumnailsSizes;
        $this->cacheControl = $cacheControl;
    }

    public function upload(UploadedFile $file)
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if ((!in_array($file->getClientMimeType(), self::$allowedMimeTypes) && !in_array($file->getMimeType(), self::$allowedMimeTypes)) || !$this->isImage($file)) {
            throw new \InvalidArgumentException(sprintf('Files of ClientMimetype %s or MimeType are not allowed.', $file->getClientMimeType(), $file->getMimeType()));
        }
        if (in_array($file->getMimeType(), self::$allowedMimeTypes)){
        	$mimeType = $file->getMimeType();
        }elseif (in_array($file->getClientMimeType(), self::$allowedMimeTypes)){
        	$mimeType = $file->getClientMimeType();
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s/%s.%s', date('Y'), date('m'), date('d'), uniqid(), $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        $this->setMetadata($adapter, $filename, $mimeType);
		
		
        //podemos acceder al bucket usando: 
        //$this->get('knp.gaufrette.filesystem_map')->get('amazon');
        
        $adapter->write($filename, file_get_contents($file->getPathname()));

        $this->generateThumbs($filename, $file->getClientOriginalExtension(), $adapter, $mimeType);

        return $filename;
    }

    private function generateThumbs($originalImageFile, $extension, $adapter, $mimeType)
    {
        $baseName = substr($originalImageFile, 0, -(strlen($extension) + 1));

        foreach ($this->thumnailsSizes as $thumbnailName => $size) {
        	
            $width = $size['width'];
            $height = $size['height'];
            
            $thumbFilename = sprintf("%s_%s.%s", $baseName, $thumbnailName, $extension);
            
            $image = $this->resizer->resize($originalImageFile, $width, $height, 'proportional');
            
            $content = $image->get($extension);
            
            $file = $this->filesystem->createFile($thumbFilename);
            
            $this->setMetadata($adapter, $thumbFilename, $mimeType);
            
            $file->setContent($content);
        }
        //delete the file original
        $this->filesystem->delete($originalImageFile);
        
        //new original file
        $new_originalImageFile = sprintf("original/%s.%s", $baseName, $extension);
        
        $image = $this->filesystem->read(sprintf("%s_%s.%s", $baseName, 'large', $extension));
        $file = $this->filesystem->createFile($new_originalImageFile);
        
        $this->setMetadata($adapter, $new_originalImageFile, $mimeType);
        
        $file->setContent($image);

    }

    /**
     * 
     * @param Adapter $adapter A configured Adapter instance of Gaufrette
     * @param string $filename
     * @param string $mimeType The type of the file as provided by PHP
     * 
     * I created this function to no repit the conditional if
     */
    private function setMetadata($adapter, $filename, $mimeType){
    	if (method_exists($adapter,'setMetadata')) {
    		$adapter->setMetadata($filename, array(
    				'contentType' => $mimeType,
    				'headers' => array('Cache-Control' => $this->cacheControl)
    				)
    			);
    	};
    	
    }
    	
    private function isImage($file){
        if($info = @getimagesize($file)) {
            return true;
        } else {
            return false;
        }
    }
}