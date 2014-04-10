<?php

namespace Ant\PhotoRestBundle\Event;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Ant\PhotoRestBundle\Model\PhotoInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PhotosUserResponseEvent extends BasePhotosEvent
{
    private $response;

    public function __construct(Paginator $photos, Request $request, Response $response)
    {
    	parent::__construct($photos, $request);
        $this->response = $response;
    }
    
    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}