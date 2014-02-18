<?php 

namespace Ant\PhotoRestBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\EntityManager\PhotoManager;
use Ant\PhotoRestBundle\Event\PhotoEvent;

/**
 * @deprecated esto no debe ir en el vendor, borrar las miniaturas es propio de cada proyecto
 * @author pc
 *
 */
class Listener implements EventSubscriberInterface
{
	protected $root_dir;
	
	protected $photoManager;
	
	/**
	 * Constructor.
	 *
	 * @param EntityManager\photoManager 
	 */
	public function __construct(PhotoManager $photoManager)
	{
		$this->photoManager = $photoManager;
	}
	
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
	 * @deprecated
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
