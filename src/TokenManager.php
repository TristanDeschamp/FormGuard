<?php

namespace FormGuard;

use Exception;

/**
 * Gère les tokens CSRF pour sécuriser les formulaires.
 * 
 * Cette classe permet de générer, stocker et valider des tokens CSRF
 * pour des formulaires nommés. Les tokens sont stockés dans la session,
 * avec une expiration personnalisable.
 * 
 * @package FormGuard
 */
class TokenManager
{
	/**
	 * Durée de validité des tokens (en secondes).
	 * 
	 * @var int
	 */
	private int $ttl;

	/**
	 * Nom de la clé dans $_SESSION où sont stockés les tokens.
	 * 
	 * @var string
	 */
	private string $sessionKey = '_formguard_tokens';

	/**
	 * Initialise le gestionnaire de tokens CSRF.
	 * 
	 * @param int $ttl - Temps de validité des tokens (en secondes), par défaut 5 minutes.
	 * @throws Exception - Si la session ne peut pas être démarée.
	 */
	public function __construct(int $ttl = 300)
	{
		$this->ttl = $ttl;

		if (session_status() !== PHP_SESSION_ACTIVE) {
			if (!headers_sent()) {
				session_start();
			} else {
				throw new Exception('Session must be started before sending headers.');
			}
		}

		if (!isset($_SESSION[$this->sessionKey])) {
			$_SESSION[$this->sessionKey] = [];
		}
	}

	/**
	 * Génère un nouveau token pour un formulaire donné.
	 * 
	 * @param string $formName - Nom unique du formulaire.
	 * @return string - Le token CSRF généré.
	 */
	public function generateToken(string $formName): string
	{
		$token = bin2hex(random_bytes(32));
		$_SESSION[$this->sessionKey][$formName] = [
			'token' => $token,
			'generated_at' => time()
		];

		return $token;
	}

	/**
	 * Vérifie si un token est valide pour un formulaire donné.
	 * 
	 * @param string $formName - Nom unique du formulaire.
	 * @param string $token - Token soumis par l'utilisateur.
	 * @return bool - true si le token est valide, false sinon.
	 */
	public function validateToken(string $formName, string $token): bool
	{
		if (!isset($_SESSION[$this->sessionKey][$formName])) {
			return false;
		}

		$data = $_SESSION[$this->sessionKey][$formName];

		if (!isset($data['token'], $data['generated_at'])) {
			return false;
		}

		// Token expiré
		if (time() - $data['generated_at'] > $this->ttl) {
			$this->destroyToken($formName);
			return false;
		}

		$isValid = hash_equals($data['token'], $token);

		// Une fois validé, je détruit le token
		if ($isValid) {
			$this->destroyToken($formName);
		}

		return $isValid;
	}

	/**
	 * Supprime un token pour un formulaire donné.
	 * 
	 * @param string $formName - Le nom unique du formulaire.
	 * @return void
	 */
	public function destroyToken(string $formName): void
	{
		unset($_SESSION[$this->sessionKey][$formName]);
	}

	/**
	 * Récupère le token actuellement stocké (si valide).
	 * 
	 * @param string $formName - Nom unique du formulaire.
	 * @return string|null
	 */
	public function getStoredToken(string $formName): ?string
	{
		return $_SESSION[$this->sessionKey][$formName]['token'] ?? null;
	}

	/**
	 * Netoie tous les tokens stockés (utile en logout ou erreur grave).
	 * 
	 * @return void
	 */
	public function clearAllTokens(): void
	{
		$_SESSION[$this->sessionKey] = [];
	}

	/**
	 * Change la durée de validité des tokens.
	 * 
	 * @param int $seconds - La nouvelle durée du token (en secondes).
	 * @return void
	 */
	public function setTTL(int $seconds): void
	{
		$this->ttl = $seconds;
	}

	/**
	 * Récupère la durée de validité actuelle.
	 * 
	 * @return int
	 */
	public function getTTL(): int
	{
		return $this->ttl;
	}
}