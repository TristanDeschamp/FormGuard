<?php

namespace FormGuard;

/**
 * Gère les champs honeypot pour piéger les bots.
 * 
 * Le principe est d'ajouter un champ invisible dans le formulaire.
 * Un utilisateur humain ne le remplira jamais. Un bot oui.
 * Si ce champ contient une valeur à la soumission, le formulaire est bloqué.
 * 
 * @package FormGuard
 */
class HoneypotManager
{
	/**
	 * Nom du champ honeypot (modifiable pour éviter les patters connus).
	 * 
	 * @var string
	 */
	private string $fieldName;

	/**
	 * Message d'erreur associé (utilie pour logging).
	 * 
	 * @var string
	 */
	private string $errorMessage = 'Le champ honeypot a été rempli.';

	/**
	 * Initialise le gestionnaire de honeypot.
	 * 
	 * @param string $fieldName - Nom du champ honeypot (par défaut '_hp').
	 */
	public function __construct(string $fieldName = '_hp')
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * Retourne le HTML du champ honeypot à inclure dans le formulaire.
	 * 
	 * A insérer dans le formulaire HTML avec `echo $honeypot->renderField();`.
	 * 
	 * @return string Code HTML du champ.
	 */
	public function renderField(): string
	{
		return sprintf(
			'<div style="display:none;">
				<label for="%1$s">Ne pas remplir ce champ</label>
				<input type="text" name="%1$s" id="%1$s" autocomplete="off" />
			</div>',
			htmlspecialchars($this->fieldName)
		);
	}

	/**
	 * Vérifie que le champ honeypot n'a pas été rempli.
	 * 
	 * @param array $postData - Le tableau $_POST.
	 * @return bool - true si le champ est vide (OK), false sinon (potentiellement un bot).
	 */
	public function isValid(array $postData): bool
	{
		return empty($postData[$this->fieldName]);
	}

	/**
	 * Récupère le nom du champ honeypot.
	 * 
	 * @return string
	 */
	public function getFieldName(): string
	{
		return $this->fieldName;
	}

	/**
	 * Définit un message d'erreur personnalisé.
	 * 
	 * @param string $message - Le nouveau message d'erreur.
	 * @return void
	 */
	public function setErrorMessage(string $message): void
	{
		$this->errorMessage = $message;
	}

	/**
	 * Récupère le message d'erreur courant.
	 * 
	 * @return string
	 */
	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}
}