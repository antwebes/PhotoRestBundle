<?php

namespace Ant\PhotoRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use	FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use Ant\PhotoRestBundle\Util\ErrorResponse;
use Ant\PhotoRestBundle\Util\Util;

use JMS\Serializer\SerializationContext;

/**
 * BaseRest Controller.
 *
 */
abstract class BaseRestController2 extends FOSRestController
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
    
    public function createError($message, $code, $statusCode)
    {
    	$errorResponse = ErrorResponse::createResponse($message, $code);
    	$view = View::create($errorResponse, $statusCode);
    	return $this->handleView($view);
    }
    
    protected function createFormErrorsView($form, $statusCode = 400)
    {
    	$errors = Util::getAllFormErrorMessages($form);
    	$r = $this->get('api.servicio.error_response')->createResponse($errors, $this->container->getParameter('channel.form.register'));
    	$view = $this->view($r, $statusCode);
    	$view->setFormat('json');
    	return $view;
    }
    
    protected function buildFormErrorsView($form)
    {
    	$view = $this->createFormErrorsView($form);
    	return $this->handleView($view);
    }
}
