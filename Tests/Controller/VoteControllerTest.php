<?php
/*
 * This file is part of the  apiChatea package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\PhotoRestBundle\Tests\Controller;

use Ant\PhotoRestBundle\Controller\VoteController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\Role;



/**
 * Class VoteControllerTest
 *
 * @package Ant\PhotoRestBundle\Tests\Controller
 */
class VoteControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testCreateAction()
    {
        //TODO This is not necessary (more single) if createError it is to service
        $user = new UserTest();
        $user->setId(-1);

        $controller = new VoteController();
        $container = new ContainerBuilder();
        $request = new Request();

        $viewHandler = $this->getMockBuilder('FOS\RestBundle\View\ViewHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $securityContextMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $container->set('security.context',$securityContextMock);
        $container->set('request',$request);

        $controller->setContainer($container);

        $token = new UsernamePasswordToken($user,'bar','providerKey',array('ROLE_USER_TEST'));
        $securityContextMock->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->will($this->returnValue($token));

        $participantMock = $this->getMock('Ant\PhotoRestBundle\Model\ParticipantInterface');
        $participantMock->expects($this->once())
            ->method('getId')
            ->withAnyParameters()
            ->will($this->returnValue(1));


        $container->set('fos_rest.view_handler',$viewHandler);
        $responseError = new Response('{"errors": "You can not vote for other user","code": "32"}',403);
        $viewHandler->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseError));

        $response = $controller->createAction($participantMock,$request);
        $this->assertEquals($response->getStatusCode(),$responseError->getStatusCode());

    }
}
