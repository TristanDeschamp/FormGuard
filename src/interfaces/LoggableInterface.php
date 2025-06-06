<?php

namespace FormGuard\Interfaces;

/**
 * Interface pour les classes qui peuvent enregistrer des événements de log.
 * 
 * @package FormGuard\Interfaces
 */
interface LoggableInterface
{
	/**
	 * Enregistre une tentative suspecte ou un événement.
	 * 
	 * @param string $event - Nom de l'événement (ex: 'csrf_failed').
	 * @param array $context - Données associées à l'événement.
	 * @return void
	 */
	public function log(string $event, array $context = []): void;
}