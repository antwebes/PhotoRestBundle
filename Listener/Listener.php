<?php 

namespace Ant\PhotoRestBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotoEvent;

class Listener implements EventSubscriberInterface
{
	protected $root_dir;
	
    static public function getSubscribedEvents()
    {
        return array(
        		AntPhotoRestEvents::PHOTO_DELETED => 'photoDeleted'
        		
            );
    }
    public function setParameter($root_dir)
    {
    	$this->root_dir = $root_dir;
    }
	/**
	 * the photo has been deleted, now you have to delete the file of image
	 * @param AntPhotoRestEvent $event
	 */
    public function photoDeleted(PhotoEvent $event)
    {
		$path = $event->getPath();
		$pathAbsolute = $this->root_dir. DIRECTORY_SEPARATOR. '..'. DIRECTORY_SEPARATOR . 'web/uploads' . DIRECTORY_SEPARATOR . $path; 
		
		if(file_exists($pathAbsolute)){
			unlink($pathAbsolute);
		}
		
    }

}
