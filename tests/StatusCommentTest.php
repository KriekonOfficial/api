<?php

namespace Tests;

use \Exception;
use PHPUnit\Framework\TestCase;

use Core\Util\TimeUtils;
use Core\Util\KeyGenerator;
use Core\Store\Database\Util\DBWrapper;

use Modules\User\Models\UserModel;
use Modules\Password\PasswordModel;

use Modules\Status\StatusGateway;
use Modules\Status\StatusEntity;
use Modules\Status\StatusCommentEntity;
use Modules\Status\Models\StatusCommentModel;

class StatusCommentTest extends TestCase
{
	private static $comment = null;
	private static $status;
	private static $user;

	public static function setUpBeforeClass() : void
	{
		$user = new UserModel();
		$user->setUserID(6970);
		$user->setFirstName('Tester');
		$user->setLastName('McTester');
		$user->setEmail('noreply+status_comment_test@kriekon.com');
		$user->setUsername('tmctester');
		$user->setDateOfBirth('1996-04-12');
		$user->setRegistrationTime(date(TimeUtils::DATEFORMAT_STANDARD));
		$user->setVerified(UserModel::VERIFIED_ON);
		$user->setLocked(UserModel::LOCKED_ON);

		$password = new PasswordModel('Testqwe12345#1');
		$user->setPasswordHash($password->generatePasswordHash());

		$entity = $user->createEntity();
		try
		{
			$entity->store();
		}
		catch (\Core\Store\Database\Exception\DatabaseException $e)
		{
			// Primary Key already declared.
		}

		self::$user = $user;

		$gateway = new StatusGateway();
		$status = $gateway->createStatus(self::$user, 'This is a comment test for crying out loud.');
		if ($status === null)
		{
			throw new Exception('Failed to create a status for the status comment test.');
		}
		self::$status = $status;
	}

	public static function tearDownAfterClass() : void
	{
		$entity = new StatusCommentEntity();
		DBWrapper::PExecute('DELETE FROM '.$entity->getCollectionTable().' WHERE USERID = ?', [self::$user->getUserID()]);

		$entity = new StatusEntity();
		DBWrapper::PExecute('DELETE FROM '.$entity->getCollectionTable().' WHERE USERID = ?', [self::$user->getUserID()]);
	}

	public function testCreateComment()
	{
		$gateway = new StatusGateway();
		$test_string = 'This is mctester create comment';
		$comment = $gateway->createComment(self::$user, self::$status, $test_string);

		$this->assertInstanceOf(StatusCommentModel::class, $comment);
		$this->assertEquals($test_string, $comment->getCommentContent());
		$this->assertNotEquals(0, $comment->getCommentID());
		$this->assertEquals(TimeUtils::DATE_ZERO, $comment->getCommentModifiedDate());
		$this->assertTrue($comment->isInitialized());

		self::$comment = $comment;

		$test_string = KeyGenerator::generateToken(300);
		$model = $gateway->createComment(self::$user, self::$status, $test_string);
		$this->assertInstanceOf(StatusCommentModel::class, $model);
		$this->assertEquals($test_string, $model->getCommentContent());
		$this->assertTrue($model->isInitialized());

		$test_string = KeyGenerator::generateToken(301);
		$model = $gateway->createComment(self::$user, self::$status, $test_string);
		$this->assertNull($model);
	}

	/**
	 * @depends testCreateComment
	 */
	public function testGetComment()
	{
		$this->assertInstanceOf(StatusCommentModel::class, self::$comment);

		$gateway = new StatusGateway();
		$model = $gateway->getComment(self::$comment->getCommentID());
		$this->assertTrue($model->isInitialized());
		$this->assertEquals(self::$comment, $model);

		$min = self::$comment->getCommentID() + 100;
		$max = self::$comment->getCommentID() + 1000;
		$model = $gateway->getComment(random_int($min, $max));
		$this->assertFalse($model->isInitialized());
	}

	/**
	 * @depends testGetComment
	 */
	public function testUpdateComment()
	{
		$this->assertInstanceOf(StatusCommentModel::class, self::$comment);

		$gateway = new StatusGateway();
		$test_string = 'This is an updated mctester status test.';
		$test_model = clone self::$comment;
		$model = $gateway->updateComment($test_model->getCommentID(), $test_string);
		$this->assertInstanceOf(StatusCommentModel::class, $model);
		$this->assertTrue($model->isInitialized());
		$this->assertEquals($test_string, $model->getCommentContent());
		$this->assertNotEquals(self::$comment->getCommentContent(), $model->getCommentContent());
		$this->assertNotEquals(self::$comment->getCommentModifiedDate(), $model->getCommentModifiedDate());

		$test_model = clone self::$comment;
		$test_string = KeyGenerator::generateToken(300);
		$model = $gateway->updateComment($test_model->getCommentID(), $test_string);
		$this->assertInstanceOf(StatusCommentModel::class, $model);
		$this->assertTrue($model->isInitialized());
		$this->assertEquals($test_string, $model->getCommentContent());
		$this->assertNotEquals(self::$comment->getCommentContent(), $model->getCommentContent());
		$this->assertNotEquals(self::$comment->getCommentModifiedDate(), $model->getCommentModifiedDate());

		$test_model = clone self::$comment;
		$test_string = KeyGenerator::generateToken(301);
		$model = $gateway->updateComment($test_model->getCommentID(), $test_string);
		$this->assertNull($model);
	}

	/**
	 * @depends testUpdateComment
	 */
	public function testDeleteComment()
	{
		$this->assertInstanceOf(StatusCommentModel::class, self::$comment);
	}
}
