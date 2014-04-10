<?php

namespace Ant\PhotoRestBundle\Event;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Ant\PhotoRestBundle\Model\PhotoInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

class BasePhotosEvent extends Event
{
	private $request;
    private $photos;

    public function __construct(Paginator $photos, Request $request)
    {
        $this->photos = $photos;
        $this->request = $request;
    }

    /**
     * @return PhotoInterface
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
