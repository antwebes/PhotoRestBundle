<?php

namespace Ant\PhotoRestBundle\Tests\Model;

use Ant\PhotoRestBundle\Tests\TestPhoto;

class PhotoTest extends \PHPUnit_Framework_TestCase
{

	public function testCreatePhotoWithParameters()
	{
		$photo = new TestPhoto();
	
		$path = 'path.png';
		$title = 'title_photo';
		$numberVotes = 1;
		$score = 10;
	
		$photo->setPath($path);
		$photo->setTitle($title);
		$photo->setNumberVotes($numberVotes);
		$photo->setScore($score);
	
		// 		$photoManagerMock->expects($this->once())
		// 		->method('updateUser')
		// 		->will($this->returnValue($user))
		// 		->with($this->isInstanceOf('FOS\UserBundle\Tests\TestUser'));
	
		// 		$manipulator = new UserManipulator($userManagerMock);
		// 		$photoManagerMock->create($username, $password, $email, $active, $superadmin);
	
		$this->assertEquals($path, $photo->getPath());
		$this->assertEquals($title, $photo->getTitle());
		$this->assertEquals($numberVotes, $photo->getNumberVotes());
		$this->assertEquals($score, $photo->getScore());
		$this->assertNotNull($photo->getPublicatedAt());
	}


	public function testPath()
	{
		$photo = $this->getPhoto();
		$this->assertNull($photo->getPath());
	
		$photo->setPath('path/relative/photo.jpeg');
		$this->assertEquals('path/relative/photo.jpeg', $photo->getPath());
	}
	
	public function testTitle()
	{
		$photo = $this->getPhoto();
		$this->assertNull($photo->getTitle());
	
		$photo->setTitle('my title');
		$this->assertEquals('my title', $photo->getTitle());
	}
	
	public function testSetAlbum()
	{
		$album = $this->getAlbum();
		$photo = $this->getPhoto();
		
		$this->assertNull($photo->getAlbum());
		
		$photo->setAlbum($album);
		$this->assertEquals($album, $photo->getAlbum());
		
	}

	public function testHasAlbumFalse()
	{
		$photo = $this->getPhoto();
		
		$this->assertFalse($photo->hasAlbum());
	}
	
	public function testHasAlbumTrue()
	{
		$album = $this->getAlbum();
		$photo = $this->getPhoto();
	
		$photo->setAlbum($album);
		$this->assertTrue($photo->hasAlbum());
	}
	
	public function testSetImage()
	{
		$image = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
		->disableOriginalConstructor(true)
		->getMock();
		$photo = $this->getPhoto();
		
		$this->assertNull($photo->getImage());
		
		$photo->setImage($image);
		$this->assertEquals($image, $photo->getImage());
	}
	
	/**
	 * @return Photo
	 */
	protected function getPhoto()
	{
		return $this->getMockForAbstractClass('Ant\PhotoRestBundle\Model\Photo');
	}
	/**
	 * @return Album
	 */
	protected function getAlbum()
	{
		return $this->getMockForAbstractClass('Ant\PhotoRestBundle\Model\Album');
	}
}