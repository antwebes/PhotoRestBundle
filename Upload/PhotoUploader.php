<?php 
namespace Ant\PhotoRestBundle\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Gaufrette\Filesystem;
use Ant\ImageResizeBundle\Image\Resizer;

class PhotoUploader
{
    private static $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif', 'application/octet-stream');

    private $filesystem;
    private $resizer;
    private $thumnailsSizes;

    public function __construct(Filesystem $filesystem, Resizer $resizer, $thumnailsSizes)
    {
        $this->filesystem = $filesystem;
        $this->resizer = $resizer;
        $this->thumnailsSizes = $thumnailsSizes;
    }

    public function upload(UploadedFile $file)
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes) && !$this->isImage($file)) {
            throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.', $file->getClientMimeType()));
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s/%s.%s', date('Y'), date('m'), date('d'), uniqid(), $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        //http://braincrafted.com/symfony2-gaufrette-s3/
//         $adapter->setMetadata($filename, array('contentType' => $file->getClientMimeType()));
        $adapter->write($filename, file_get_contents($file->getPathname()));

        $this->generateThumbs($filename, $file->getClientOriginalExtension());

        return $filename;
    }

    private function generateThumbs($originalImageFile, $extension)
    {
        $baseName = substr($originalImageFile, 0, -(strlen($extension) + 1));

        foreach ($this->thumnailsSizes as $thumbnailName => $size) {
            $width = $size['width'];
            $height = $size['height'];
            $thumbFilename = sprintf("%s_%s.%s", $baseName, $thumbnailName, $extension);
            $image = $this->resizer->resize($originalImageFile, $width, $height, 'proportional');
            $content = $image->get($extension);
            $file = $this->filesystem->createFile($thumbFilename);
            
            $file->setContent($content);
        }

        $this->filesystem->delete($originalImageFile);
    }

    private function isImage($file){
        if($info = @getimagesize($file)) {
            return true;
        } else {
            return false;
        }
    }
}