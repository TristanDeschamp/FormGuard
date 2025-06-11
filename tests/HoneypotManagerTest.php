<?php

use PHPUnit\Framework\TestCase;
use FormGuard\HoneypotManager;

class HoneypotManagerTest extends TestCase
{
	public function testHoneypotFieldIsValidWhenEmpty()
	{
		$honeypot = new HoneypotManager('_bot');
		$postData = ['name' => 'Alice', '_bot' => ''];
		$this->assertTrue($honeypot->isValid($postData));
	}

	public function testHoneypotFieldIsInvalidWhenFilled()
	{
		$honeypot = new HoneypotManager('_bot');
		$postData = ['_bot' => 'gotcha'];
		$this->assertFalse($honeypot->isValid($postData));
	}
}
