<?php

namespace FormGuard;

use FormGuard\Interfaces\LoggableInterface;

/**
 * Implémentation simple de journalisation dans un fichier texte.
 * 
 * @package FormGuard
 */
class Logger implements LoggableInterface
{
	/**
	 * Chemin du fichier de log.
	 * 
	 * @var string
	 */
	private string $logFile;

	/**
	 * Initialise le logger.
	 * 
	 * @param string $logFile - Chemin du fichier de log.
	 */
	public function __construct(string $logFile = __DIR__ . '/../logs/formguard.log')
	{
		$this->logFile = $logFile;

		$dir = dirname($this->logFile);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
	}

	/**
	 * Enregistre une ligne de log formatée.
	 * 
	 * @param string $event.
	 * @param array $context.
	 * @return void
	 */
	public function log(string $event, array $context = []): void
	{
		$time = date('Y-m-d H:i:s');
		$ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
		$data = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		$line = "[$time][$ip][$event] $data" . PHP_EOL;
		file_put_contents($this->logFile, $line, FILE_APPEND);
	}
}