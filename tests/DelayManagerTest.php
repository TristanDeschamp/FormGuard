<?php

use PHPUnit\Framework\TestCase;
use FormGuard\DelayManager;

class DelayManagerTest extends TestCase
{
	protected function setUp(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
	}

	public function testDelayIsRespectedAfterWait()
	{
		$manager = new DelayManager();
		$formName = 'delayed_form';

		$manager->markStart($formName);
		sleep(2);
		$this->assertTrue($manager->isDelayRespected($formName, 1));
	}

	public function testDelayFailsIfTooFast()
	{
		$manager = new DelayManager();
		$formName = 'fast_form';

		$manager->markStart($formName);
		$this->assertFalse($manager->isDelayRespected($formName, 2));
	}
}
