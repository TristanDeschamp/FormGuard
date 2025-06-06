<?php

namespace FormGuard;

use Exception;
use FormGuard\Interfaces\LoggableInterface;

/**
 * Façade unifiée pour sécuriser un formulaire web.
 * 
 * Combine protection CSRF, champ honeypot, et délai de soumission minimum.
 * Utilisable facilement dans n'importe quel projet PHP.
 * 
 * @package FormGuard
 */
class FormGuard
{
	private TokenManager $tokenManager;
	private HoneypotManager $honeypotManager;
	private DelayManager $delayManager;

	private ?LoggableInterface $logger = null;

	/**
	 * Initialise tous les gestionnaires interne.
	 * 
	 * @param int $csrfTTL - Durée de vie des tokens CSRF (en secondes).
	 * @param string $honeypotName - Nom du champ honeypot.
	 */
	public function __construct(int $csrfTTL = 300, string $honeypotName = '_hp')
	{
		$this->tokenManager = new TokenManager($csrfTTL);
		$this->honeypotManager = new HoneypotManager($honeypotName);
		$this->delayManager = new DelayManager();
	}

	/**
	 * Injecte un logger conforme à LoggableInterface.
	 * 
	 * @param LoggableInterface $logger.
	 * @return void
	 */
	public function setLogger(LoggableInterface $logger): void
	{
		$this->logger = $logger;
	}

	/**
	 * A appeler lors de l'affichage du formulaire pour marquer le départ.
	 * 
	 * @param string $formName
	 * @return void
	 */
	public function prepareForm(string $formName): void
	{
		$this->delayManager->markStart($formName);
	}

	/**
	 * Génère le champ CSRF à insérer dans le formulaire.
	 * 
	 * @param string $formName
	 * @return string
	 */
	public function csrfField(string $formName): string
	{
		$token = $this->tokenManager->generateToken($formName);
		return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '"/>';
	}

	/**
	 * Génère le champ honeypot HTML
	 * 
	 * @return string
	 */
	public function honeypotField(): string
	{
		return $this->honeypotManager->renderField();
	}

	/**
	 * Vérifie que la soumission du formulaire est valide.
	 * 
	 * @param string $formName - Nom du formulaire.
	 * @param array $postData - Le tableau $_POST.
	 * @param int $minDelay - Minimum de secondes attendues entre affichage et soumission.
	 * @return bool - true si toutes les vérifications passent.
	 */
	public function isValid(string $formName, array $postData, int $minDelay = 3): bool
	{
		$csrf = $postData['csrf_token'] ?? '';

		if (!$this->tokenManager->validateToken($formName, $csrf)) {
			$valid = false;
			$this->log('csrf_failed', ['form' => $formName, 'token' => $csrf]);
		}

		if (!$this->honeypotManager->isValid($postData)) {
			$valid = false;
			$this->log('honeypot_triggered', ['form' => $formName, 'data' => $postData]);
		}

		if (!$this->delayManager->isDelayRespected($formName, $minDelay)) {
			$valid = false;
			$this->log('delay_too_fast', ['form' => $formName]);
		}

		// Remettre la marqueur pour éviter les faux positifs à la prochaine soumission.
		$this->delayManager->markStart($formName);

		return $valid;
	}

	private function log(string $event, array $context = []): void
	{
		if ($this->logger) {
			$this->logger->log($event, $context);
		}
	}

	/**
	 * Accès direct au gestionnaire de tokens (si besoin avancé).
	 * 
	 * @return TokenManager
	 */
	public function tokens(): TokenManager
	{
		return $this->tokenManager;
	}

	/**
	 * Accès direct au gestionnaire honeypot.
	 * 
	 * @return HoneypotManager
	 */
	public function honeypot(): HoneypotManager
	{
		return $this->honeypotManager;
	}

	/**
	 * Accès direct au gestionnaire de délai.
	 * 
	 * @return DelayManager
	 */
	public function delay(): DelayManager
	{
		return $this->delayManager;
	}
}