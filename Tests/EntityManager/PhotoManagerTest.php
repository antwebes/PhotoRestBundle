<?php

namespace Ant\PhotoRestBundle\Tests\EntityManager;

use Ant\PhotoRestBundle\Tests\TestPhoto;
use Ant\PhotoRestBundle\EntityManager\PhotoManager;

/**
 * Class PhotoManagerTest
 *
 */
class PhotoManagerTest extends \PHPUnit_Framework_TestCase
{
	CONST PHOTO_CLASS = 'Ant\PhotoRestBundle\Tests\TestPhoto';
	
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $em;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $repository;

	protected $fileSystem;
	
	protected $event_dispatcher;

	public function setUp()
	{		
		if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
			$this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
		}
		
		$this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
		
		$this->event_dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
		
		$metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
		
		$this->fileSystem = $this->getMockBuilder('Gaufrette\Filesystem')
			->disableOriginalConstructor()
			->getMock();
		
		$this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();
		
		$this->em->expects($this->once())
			->method('getRepository')
			->with($this->equalTo(static::PHOTO_CLASS))
			->will($this->returnValue($this->repository));
		
		$this->em->expects($this->once())
			->method('getClassMetadata')
			->with($this->equalTo(static::PHOTO_CLASS))
			->will($this->returnValue($metadata));
		
		$metadata->expects($this->any())
			->method('getName')
			->will($this->returnValue(static::PHOTO_CLASS));
		
		$this->photoManager = $this->createPhotoManager($this->fileSystem, $this->em, static::PHOTO_CLASS, $this->event_dispatcher);
	}
	
	/**
	 * Get a Participant
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	protected function createParticipantMock($id)
	{
		$participant = $this->getMockBuilder('Ant\PhotoRestBundle\Model\ParticipantInterface')
		->disableOriginalConstructor(true)
		->getMock();
	
		$participant->expects($this->any())
		->method('getId')
		->will($this->returnValue($id));
	
		return $participant;
	}
	
	protected function createPhotoManager($fileSystem, $em, $photoClass, $event_dispatcher)
	{
		return new PhotoManager($fileSystem, $em, $photoClass, $event_dispatcher);	
	}
	
	public function testGetClass()
	{		
		$this->assertEquals(static::PHOTO_CLASS, $this->photoManager->getClass());
	}
	
	public function testFindOnePhotoBy()
	{
		$crit = array("foo" => "bar");
		$this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->will($this->returnValue(array()));
	
		$this->photoManager->findOnePhotoBy($crit);
	}
	
	public function testDeletePhoto()
	{
		$photo = $this->getPhoto();
		$photo->setPath('unpath.jpg');

		$this->fileSystem->expects($this->once())->method('delete')->with('unpath.jpg');
		$this->fileSystem->expects($this->once())->method('has')->with('unpath.jpg')->will($this->returnValue(true));
		$this->em->expects($this->once())->method('remove')->with($this->equalTo($photo));
		$this->em->expects($this->once())->method('flush');
	
		$this->photoManager->deletePhoto($photo);
	}
	
	/**
	 * Now the repository is called although the image is removed already, so dont must return (@expectedException InvalidArgumentException)
	 * If the image is not in amazon s3, we remove of database.
	 */
	public function testDeletePhotoWithPathIncorrect()
	{
		$photo = $this->getPhoto();
		$photo->setPath('unpath.jpg');
	
		$this->fileSystem->expects($this->never())->method('delete');
		$this->fileSystem->expects($this->once())->method('has')->with('unpath.jpg')->will($this->returnValue(false));
		
		$this->em->expects($this->once())->method('remove');
		$this->em->expects($this->once())->method('flush');
	
		$this->photoManager->deletePhoto($photo);
	}
	
	public function testIsOwnerTrue()
	{
		$photo = $this->getPhoto();
		$user = $this->createParticipantMock(1);
		
		$photo->setParticipant($user);
		$this->assertTrue($this->photoManager->isOwner($user, $photo));
	}
	public function testIsOwnerFalse()
	{
		$photo = $this->getPhoto();
		$user = $this->createParticipantMock(1);
		$userNoOwner = $this->createParticipantMock(2);
	
		$photo->setParticipant($user);
		$this->assertFalse($this->photoManager->isOwner($userNoOwner, $photo));
	}
	
	protected function getPhoto()
	{
		$photoClass = static::PHOTO_CLASS;
	
		return new $photoClass();
	}
	
// 	public function testSavePhoto()
// 	{
// 		$photo = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
	
// 		$thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
// 		$comment->expects($this->any())
// 		->method('getThread')
// 		->will($this->returnValue($thread));
	
// 		// TODO: Not sure how to set the assertion that this method
// 		// will be called twice with different parameters.
// 		$this->em->expects($this->exactly(2))
// 		->method('persist');
	
// 		$this->em->expects($this->once())
// 		->method('flush');
	
// 		$commentManager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);
// 		$commentManager->saveComment($comment);
// 	}
	
// 	/**
// 	 * Usual test case where neither createdBy or createdAt is set
// 	 */
// 	public function testDoCreatedByAndAt()
// 	{
// 		$thread = $this->createThreadMock();
// 		$thread->expects($this->exactly(1))->method('getFirstMessage')
// 		->will($this->returnValue($this->createMessageMock()));

// 		$threadManager = new TestThreadManager();
// 		$threadManager->doCreatedByAndAt($thread);
// 	}

// 	/**
// 	 * Test where createdBy is set
// 	 */
// 	public function testDoCreatedByAndAtWithCreatedBy()
// 	{
// 		$thread = $this->createThreadMock();

// 		$thread->expects($this->exactly(0))->method('setCreatedBy');
// 		$thread->expects($this->exactly(1))->method('setCreatedAt');
// 		$thread->expects($this->exactly(1))->method('getCreatedBy')
// 		->will($this->returnValue($this->user));

// 		$thread->expects($this->exactly(1))->method('getFirstMessage')
// 		->will($this->returnValue($this->createMessageMock()));

// 		$threadManager = new TestThreadManager();
// 		$threadManager->doCreatedByAndAt($thread);
// 	}

// 	/**
// 	 * Test where createdAt is set
// 	 */
// 	public function testDoCreatedByAndAtWithCreatedAt()
// 	{
// 		$thread = $this->createThreadMock();

// 		$thread->expects($this->exactly(1))->method('setCreatedBy');
// 		$thread->expects($this->exactly(0))->method('setCreatedAt');
// 		$thread->expects($this->exactly(1))->method('getCreatedAt')
// 		->will($this->returnValue($this->date));

// 		$thread->expects($this->exactly(1))->method('getFirstMessage')
// 		->will($this->returnValue($this->createMessageMock()));

// 		$threadManager = new TestThreadManager();
// 		$threadManager->doCreatedByAndAt($thread);
// 	}

// 	/**
// 	 * Test where both craetedAt and createdBy is set
// 	 */
// 	public function testDoCreatedByAndAtWithCreatedAtAndBy()
// 	{
// 		$thread = $this->createThreadMock();
// 		$thread->expects($this->exactly(0))->method('setCreatedBy');
// 		$thread->expects($this->exactly(0))->method('setCreatedAt');
// 		$thread->expects($this->exactly(1))->method('getCreatedAt')
// 		->will($this->returnValue($this->date));

// 		$thread->expects($this->exactly(1))->method('getCreatedBy')
// 		->will($this->returnValue($this->user));

// 		$thread->expects($this->exactly(1))->method('getFirstMessage')
// 		->will($this->returnValue($this->createMessageMock()));

// 		$threadManager = new TestThreadManager();
// 		$threadManager->doCreatedByAndAt($thread);
// 	}

// 	/**
// 	 * Test where thread do not have a message
// 	 */
// 	public function testDoCreatedByAndNoMessage()
// 	{
// 		$thread = $this->createThreadMock();
// 		$thread->expects($this->exactly(0))->method('setCreatedBy');
// 		$thread->expects($this->exactly(0))->method('setCreatedAt');
// 		$thread->expects($this->exactly(0))
// 		->method('getCreatedAt')
// 		->will($this->returnValue($this->date));
// 		$thread->expects($this->exactly(0))
// 		->method('getCreatedBy')
// 		->will($this->returnValue($this->user));

// 		$threadManager = new TestThreadManager();
// 		$threadManager->doCreatedByAndAt($thread);
// 	}

// 	/**
// 	 * Get a message mock
// 	 *
// 	 * @return mixed
// 	 */
// 	protected function createMessageMock()
// 	{
// 		$message = $this->getMockBuilder('FOS\MessageBundle\Document\Message')
// 		->getMock();

// 		$message->expects($this->any())
// 		->method('getSender')
// 		->will($this->returnValue($this->user));

// 		$message->expects($this->any())
// 		->method('getCreatedAt')
// 		->will($this->returnValue($this->date));

// 		return $message;
// 	}

// 	/**
// 	 * Add expectations on the thread mock
// 	 *
// 	 * @param mock &$thread
// 	 * @param int $createdByCalls
// 	 * @param int $createdAtCalls
// 	 */
// 	protected function addThreadExpectations(&$thread, $createdByCalls=1, $createdAtCalls=1)
// 	{
// 		$thread->expects($this->exactly($createdByCalls))
// 		->method('setCreatedBy')
// 		->with($this->equalTo($this->user));

// 		$thread->expects($this->exactly($createdAtCalls))
// 		->method('setCreatedAt')
// 		->with($this->equalTo($this->date));
// 	}



// 	/**
// 	 * Returns a photo mock
// 	 *
// 	 * @return mixed
// 	 */
// 	protected function createPhotoMock()
// 	{
// 		return $this->getMockBuilder('Ant\PhotoRestBundle\Model\PhotoInterface')
// 		->disableOriginalConstructor(true)
// 		->getMock();
// 	}
}
