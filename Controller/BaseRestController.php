<?php

namespace Ant\PhotoRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest,
	FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\View;

use JMS\Serializer\SerializationContext;

/**
 * BaseRest controller.
 *
 */
abstract class BaseRestController extends FOSRestController
{
	
    public function buildView($entity, $statusCode, $context = null)
    {
    	$view = $this->view($entity, $statusCode);
    
    	if($context != null){
    		$context = SerializationContext::create()->setGroups(array($context));
    		$view->setSerializationContext($context);
    	}
    		
    	return $this->handleView($view);
    }
}
