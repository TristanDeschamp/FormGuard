<?php

use PHPUnit\Framework\TestCase;
use FormGuard\TokenManager;

class TokenManagerTest extends TestCase
{
	protected function setUp(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
				session_start();
		}
	}

	public function testGenerateAndValidateToken()
	{
		$manager = new TokenManager(300);
		$token = $manager->generateToken('test_form');

		$this->assertNotEmpty($token);
		$this->assertTrue($manager->validateToken('test_form', $token));
	}

	public function testTokenExpiresImmediately()
	{
		$manager = new TokenManager(0); // expires immédiatement
		$token = $manager->generateToken('expire_form');
		sleep(1);
		$this->assertFalse($manager->validateToken('expire_form', $token));
	}
}
