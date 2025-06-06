<?php

namespace FormGuard;

use Exception;

/**
 * Gère un délai minimum entre l'affichage du formulaire et sa soumission.
 * 
 * Le but est de bloquer les soumissions instantanées suspectes.
 * Le timestamp est stocké en session pour chaque formulaire.
 * 
 * @package FormGuard
 */
class DelayManager
{
	/**
	 * Clé de session utilisée pour stocker les timestamps.
	 * 
	 * @var string
	 */
	private string $sessionKey = '_formguard_delays';

	/**
	 * Initialise le gestionnaire et la session si nécessaire.
	 * 
	 * @throws Exception
	 */
	public function __construct()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			if (!headers_sent()) {
				session_start();
			} else {
				throw new Exception('La session doit être démarrée avant envoi des headers.');
			}
		}

		if (!isset($_SESSION[$this->sessionKey])) {
			$_SESSION[$this->sessionKey] = [];
		}
	}

	/**
	 * Enregistre l'instant où le formulaire est affiché.
	 * 
	 * @param string $formName - Nom unique du formulaire.
	 * @return void
	 */
	public function markStart(string $formName): void
	{
		$_SESSION[$this->sessionKey][$formName] = time();
	}

	/**
	 * Vérifie que la soumission a été faite après un délai minimum.
	 * 
	 * @param string $formName - Nom du formulaire.
	 * @param int $minimumSeconds - Nombre de secondes minimales.
	 * @return bool - true si le délai est respecté, false sinon.
	 */
	public function isDelayRespected(string $formName, int $minimumSeconds = 3): bool
	{
		if (!isset($_SESSION[$this->sessionKey][$formName])) {
			return false;
		}

		$start = $_SESSION[$this->sessionKey][$formName];
		$nom = time();

		return ($nom - $start) >= $minimumSeconds;
	}

	/**
	 * Supprime la marque temporelle d'un formulaire.
	 * 
	 * @param string $formName.
	 * @return void
	 */
	public function clearMark(string $formName): void
	{
		unset($_SESSION[$this->sessionKey][$formName]);
	}

	/**
	 * Réinitialise tous les délais enregistrés (utile en logout ou réinit global).
	 * 
	 * @return void
	 */
	public function clearAll(): void
	{
		$_SESSION[$this->sessionKey] = [];
	}
}