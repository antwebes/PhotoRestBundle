<?php

namespace Ant\PhotoRestBundle\Event;



use Ant\PhotoRestBundle\Model\PhotoInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PhotoResponseEvent extends BasePhotoEvent
{
    private $response;

    public function __construct(PhotoInterface $photo, Request $request, Response $response)
    {
    	parent::__construct($photo, $request);
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