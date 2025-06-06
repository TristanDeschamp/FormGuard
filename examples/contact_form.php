<?php
require_once __DIR__ . '/../Autoloader.php';

use FormGuard\FormGuard;
use FormGuard\Logger;

$formName = 'contact_secure';
$form = new FormGuard();
$form->setLogger(new Logger());

$message = '';
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
	if ($form->isValid($formName, $_POST, 3)) {
		$message = '✅ Formulaire sécurisé envoyé avec succès !';
	} else {
		$message = '❌ Échec : le formulaire a échoué une ou plusieurs vérifications de sécurité.';
	}
} else {
	// Initialisation du délai de soumission
	$form->prepareForm($formName);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Formulaire sécurisé avec FormGuard</title>
</head>
<body>

	<h2>Formulaire de contact protégé (CSRF + Honeypot + Délai)</h2>

	<?php if ($message): ?>
		<p><strong><?= htmlspecialchars($message) ?></strong></p>
	<?php endif; ?>

	<form method="post">
		<label>Nom :
			<input type="text" name="name" required>
		</label><br><br>

		<label>Message :
			<textarea name="message" required></textarea>
		</label><br><br>

		<?= $form->csrfField($formName) ?>
		<?= $form->honeypotField() ?>

		<button type="submit">Envoyer</button>
	</form>

</body>
</html>
