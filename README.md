# 🛡️ FormGuard

**FormGuard** est une librairie PHP orientée objet, légère et robuste, pour **sécuriser vos formulaires web** contre les menaces courantes :  
protection CSRF, bots, soumissions automatisées, replays… Le tout, sans dépendance, facilement intégrable dans tout projet PHP.

---

## 🚀 Fonctionnalités

- 🔐 **CSRF Protection** : génération et validation de tokens sécurisés
- 🕳️ **Honeypot** : champ invisible pour piéger les bots
- ⏱️ **Délai de soumission** : bloque les envois instantanés suspects
- 📂 **Logging** : journalisation des tentatives malveillantes
- 🧩 Architecture **100% POO**, typée et documentée avec PHPDoc
- ✅ Facile à intégrer dans n'importe quel projet PHP (framework, CMS, custom)

---

## 📦 Installation

**Méthode manuelle :**

1. Clonez ou copiez le dossier `FormGuard` dans votre projet.
2. Incluez l’autoloader dans votre fichier principal :

```php
require_once __DIR__ . '/FormGuard/Autoloader.php';
```

---

## 🧪 Exemple d'utilisation rapide

```php
use FormGuard\FormGuard;
use FormGuard\Logger;

$form = new FormGuard();
$form->setLogger(new Logger()); // Optionnel

$formName = 'contact_secure';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form->isValid($formName, $_POST, 3)) {
        echo "✅ Formulaire valide.";
    } else {
        echo "❌ Tentative rejetée.";
    }
} else {
    $form->prepareForm($formName);
}
```

### 💡 Dans le HTML :

```php
<form method="post">
    <?= $form->csrfField($formName) ?>
    <?= $form->honeypotField() ?>

    <!-- Vos champs classiques -->
    <input type="text" name="name" required>
    <textarea name="message" required></textarea>

    <button type="submit">Envoyer</button>
</form>
```

---

## 🧱 Structure du projet

```
FormGuard/
│
├── src/
│   ├── FormGuard.php
│   ├── TokenManager.php
│   ├── HoneypotManager.php
│   ├── DelayManager.php
│   ├── Logger.php
│   └── interfaces/
│       └── LoggableInterface.php
│
├── examples/         ← Exemples de formulaires sécurisés
├── logs/             ← Dossier des logs générés automatiquement
├── Autoloader.php
└── README.md
```

---

## 🛡️ Sécurité

FormGuard respecte les bonnes pratiques de sécurité web :

- Tokens CSRF aléatoires avec `random_bytes()` et `hash_equals()`
- Honeypots invisibles avec `display: none`
- Timestamps de soumission stockés en session
- Logger personnalisable
- Code strictement typé, clair, extensible

---

## 🪪 Auteur

Développé par [TristanDeschamp]  
Contact : [tristan2020.d@gmail.com]  
Projet personnel pour portfolio / usage pro

---

## 📄 Licence

MIT — libre à l’usage personnel ou commercial avec attribution.
