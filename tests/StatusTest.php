<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use Core\Util\TimeUtils;
use Core\Util\KeyGenerator;
use Core\Store\Database\Util\DBWrapper;

use Modules\Password\PasswordModel;
use Modules\Status\StatusGateway;
use Modules\Status\Models\StatusModel;
use Modules\Status\StatusEntity;
use Modules\User\Models\UserModel;

class StatusTest extends TestCase
{
	private static $status = null;
	private static $user;

	public static function setUpBeforeClass() : void
	{
		$user = new UserModel();
		$user->setUserID(6969);
		$user->setFirstName('Tester');
		$user->setLastName('McTester');
		$user->setEmail('noreply+status_test@kriekon.com');
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
	}

	public static function tearDownAfterClass() : void
	{
		$entity = new StatusEntity();
		DBWrapper::PExecute('DELETE FROM '.$entity->getCollectionTable().' WHERE USERID = ?', [self::$user->getUserID()]);
	}

	public function testCreateStatus()
	{
		$gateway = new StatusGateway();

		$test_string = 'This is a tester mctester status test.';
		$model = $gateway->createStatus(self::$user, $test_string);

		$this->assertInstanceOf(StatusModel::class, $model);
		$this->assertEquals($test_string, $model->getStatusContent());
		$this->assertNotEquals(0, $model->getStatusID());
		$this->assertEquals(TimeUtils::DATE_ZERO, $model->getStatusModifiedDate());
		$this->assertTrue($model->isInitialized());
		self::$status = $model;

		$test_string = KeyGenerator::generateToken(300);
		$model = $gateway->createStatus(self::$user, $test_string);
		$this->assertInstanceOf(StatusModel::class, $model);
		$this->assertEquals($test_string, $model->getStatusContent());
		$this->assertTrue($model->isInitialized());

		$test_string = KeyGenerator::generateToken(301);
		$model = $gateway->createStatus(self::$user, $test_string);
		$this->assertNull($model);
	}

	/**
	 * @depends testCreateStatus
	 */
	public function testGetStatus()
	{
		$this->assertInstanceOf(StatusModel::class, self::$status);

		$gateway = new StatusGateway();
		$model = $gateway->getStatus(self::$status->getStatusID());
		$this->assertTrue($model->isInitialized());
		$this->assertEquals(self::$status, $model);

		$min = self::$status->getStatusID() + 100;
		$max = self::$status->getStatusID() + 1000;
		$model = $gateway->getStatus(random_int($min, $max));
		$this->assertFalse($model->isInitialized());
	}

	/**
	 * @depends testGetStatus
	 */
	public function testUpdateStatus()
	{
		$this->assertInstanceOf(StatusModel::class, self::$status);

		$gateway = new StatusGateway();
		$test_string = 'This is an updated mctester status test.';
		$test_model = clone self::$status;
		$model = $gateway->updateStatus($test_model->getStatusID(), $test_string);
		$this->assertInstanceOf(StatusModel::class, $model);
		$this->assertTrue($model->isInitialized());
		$this->assertEquals($test_string, $model->getStatusContent());
		$this->assertNotEquals(self::$status->getStatusContent(), $model->getStatusContent());
		$this->assertNotEquals(self::$status->getStatusModifiedDate(), $model->getStatusModifiedDate());

		$test_model = clone self::$status;
		$test_string = KeyGenerator::generateToken(300);
		$model = $gateway->updateStatus($test_model->getStatusID(), $test_string);
		$this->assertInstanceOf(StatusModel::class, $model);
		$this->assertTrue($model->isInitialized());
		$this->assertEquals($test_string, $model->getStatusContent());
		$this->assertNotEquals(self::$status->getStatusContent(), $model->getStatusContent());
		$this->assertNotEquals(self::$status->getStatusModifiedDate(), $model->getStatusModifiedDate());

		$test_model = clone self::$status;
		$test_string = KeyGenerator::generateToken(301);
		$model = $gateway->updateStatus($test_model->getStatusID(), $test_string);
		$this->assertNull($model);
	}

	/**
	 * @depends testUpdateStatus
	 */
	public function testDeleteStatus()
	{
		$this->assertInstanceOf(StatusModel::class, self::$status);
		$gateway = new StatusGateway();
		$this->assertTrue($gateway->deleteStatus(self::$status->getStatusID()));
		$this->assertFalse($gateway->deleteStatus(self::$status->getStatusID()));
	}
}
