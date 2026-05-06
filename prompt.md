# Spécification — API d’envoi d’emails avec Laravel + FilamentPHP v5

## Objectif

Créer une application Laravel disposant :

* d’une API sécurisée d’envoi d’emails ;
* d’une gestion des types d’emails ;
* d’un système de templates par type d’email ;
* d’une interface d’administration avec FilamentPHP v5 ;
* d’une gestion des consommateurs API via API Key ;
* d’une page de configuration SMTP complète ;
* d’un historique des emails envoyés.

---

# Stack technique

## Backend

* Laravel 12+
* PHP 8.2+
* MySQL ou PostgreSQL
* Laravel Sanctum (ou système custom API Key)
* Queue Laravel obligatoire
* Jobs Laravel pour l’envoi des emails
* Notifications/Mailables Laravel

## Admin Panel

* FilamentPHP v5

## Templates

* Blade templates
* Support HTML + variables dynamiques

---

# Fonctionnalités principales

## 1. Gestion des types d’emails

Créer une table :

```sql
mail_types
```

### Champs

| Champ       | Type          |
| ----------- | ------------- |
| id          | bigint        |
| name        | string        |
| slug        | string unique |
| description | text nullable |
| is_active   | boolean       |
| created_at  | timestamp     |
| updated_at  | timestamp     |

### Exemples

* welcome_email
* password_reset
* invoice_created
* otp_code
* newsletter

---

# 2. Gestion des templates d’emails

Chaque type d’email possède un template.

Créer une table :

```sql
mail_templates
```

## Champs

| Champ        | Type              |
| ------------ | ----------------- |
| id           | bigint            |
| mail_type_id | foreignId         |
| subject      | string            |
| html_content | longText          |
| text_content | longText nullable |
| variables    | json nullable     |
| is_default   | boolean           |
| created_at   | timestamp         |
| updated_at   | timestamp         |

---

# Variables dynamiques

Le système doit permettre l’utilisation de variables dynamiques.

## Exemple

Template :

```html
<h1>Bonjour {{name}}</h1>
<p>Votre code OTP est : {{otp}}</p>
```

Payload API :

```json
{
  "type": "otp_code",
  "to": "user@example.com",
  "variables": {
    "name": "Joseph",
    "otp": "552211"
  }
}
```

Le moteur doit remplacer automatiquement les variables.

---

# 3. API d’envoi d’email

## Endpoint

```http
POST /api/v1/mails/send
```

## Authentification

Authentification obligatoire via API Key.

Header :

```http
X-API-KEY: YOUR_API_KEY
```

---

# Payload

```json
{
  "type": "welcome_email",
  "to": [
    "client@example.com"
  ],
  "cc": [],
  "bcc": [],
  "variables": {
    "name": "Joseph"
  },
  "attachments": []
}
```

---

# Validation

## Règles

* type obligatoire
* type doit exister
* template actif obligatoire
* email valide
* variables facultatives
* attachments facultatifs

---

# Réponse succès

```json
{
  "success": true,
  "message": "Mail queued successfully"
}
```

---

# Réponse erreur

```json
{
  "success": false,
  "message": "Invalid API key"
}
```

---

# 4. Gestion des consommateurs API

Créer une table :

```sql
api_consumers
```

## Champs

| Champ      | Type                |
| ---------- | ------------------- |
| id         | bigint              |
| name       | string              |
| email      | string              |
| api_key    | string unique       |
| is_active  | boolean             |
| rate_limit | integer default 100 |
| created_at | timestamp           |
| updated_at | timestamp           |

---

# Fonctionnalités consommateurs

* Génération automatique des API Keys
* Désactivation d’un consommateur
* Rotation API Key
* Rate limiting
* Logs d’utilisation
* Dernière utilisation

---

# Middleware API

Créer un middleware :

```php
ValidateApiKey
```

Le middleware doit :

* vérifier le header X-API-KEY
* vérifier si le consommateur existe
* vérifier si actif
* injecter le consommateur dans la requête
* enregistrer l’usage

---

# 5. Historique des emails

Créer une table :

```sql
mail_logs
```

## Champs

| Champ           | Type               |
| --------------- | ------------------ |
| id              | bigint             |
| api_consumer_id | foreignId nullable |
| mail_type_id    | foreignId          |
| recipient       | string             |
| subject         | string             |
| payload         | json               |
| status          | string             |
| error_message   | text nullable      |
| sent_at         | timestamp nullable |
| created_at      | timestamp          |
| updated_at      | timestamp          |

---

# Status possibles

* pending
* queued
* sent
* failed

---

# 6. Système SMTP dynamique

Créer une page Filament Settings.

Les paramètres SMTP doivent être modifiables depuis l’admin.

## Champs SMTP

| Champ             |
| ----------------- |
| mail_mailer       |
| mail_host         |
| mail_port         |
| mail_username     |
| mail_password     |
| mail_encryption   |
| mail_from_address |
| mail_from_name    |

---

# Exigences SMTP

* Possibilité de tester la connexion SMTP
* Possibilité d’envoyer un email de test
* Stockage sécurisé des credentials
* Cache automatique de la configuration
* Rechargement dynamique de la config mail

---

# 7. Interface FilamentPHP v5

Créer les Resources suivantes.

---

# MailTypeResource

## Fonctionnalités

* CRUD complet
* Activation/désactivation
* Liste des templates associés

---

# MailTemplateResource

## Fonctionnalités

* CRUD complet
* Éditeur HTML
* Prévisualisation template
* Variables supportées
* Choix du type de mail
* Template par défaut

---

# ApiConsumerResource

## Fonctionnalités

* CRUD complet
* Génération API Key
* Régénération API Key
* Activation/désactivation
* Affichage dernière utilisation
* Rate limit

---

# MailLogResource

## Fonctionnalités

* Liste des emails envoyés
* Filtres par status
* Filtres par type
* Filtres par date
* Détail payload
* Message erreur

---

# Settings Page

Créer une page Filament :

```php
MailSettingsPage
```

## Fonctionnalités

* Modifier SMTP
* Tester SMTP
* Sauvegarde sécurisée
* Validation des paramètres

---

# 8. Architecture recommandée

## Services

Créer :

```php
App\Services\MailService
```

Responsabilités :

* récupérer le template
* parser les variables
* envoyer le mail
* logger le résultat
* dispatcher les jobs

---

# Jobs

Créer :

```php
SendMailJob
```

Responsabilités :

* envoi asynchrone
* retry automatique
* gestion des erreurs

---

# Actions recommandées

Créer des classes Actions.

Exemple :

```php
CreateMailLogAction
SendTemplatedMailAction
ValidateMailVariablesAction
```

---

# 9. Sécurité

## Obligatoire

* Rate limiting API
* Validation stricte payload
* Protection contre injection HTML
* Logs complets
* API Key hashée en base
* HTTPS obligatoire
* Queue obligatoire

---

# 10. Variables template

Le système doit supporter :

## Variables simples

```blade
{{name}}
```

## Conditions

```blade
@if($premium)
<p>Compte premium</p>
@endif
```

## Boucles

```blade
@foreach($items as $item)
<li>{{ $item }}</li>
@endforeach
```

---

# 11. Support pièces jointes

Le système doit permettre :

* fichiers locaux
* URLs
* base64

Validation obligatoire :

* taille max
* types autorisés

---

# 12. Monitoring

Prévoir :

* dashboard Filament
* nombre emails envoyés
* emails échoués
* emails par type
* emails par consommateur
* graphiques statistiques

---

# 13. Tests

Créer :

* tests API
* tests Feature
* tests Unit
* tests MailService
* tests middleware API Key

---

# 14. Seeder

Créer des seeders pour :

* types emails par défaut
* admin user
* templates exemples

---

# 15. Livrables attendus

## Backend

* API REST fonctionnelle
* FilamentPHP v5 configuré
* Queue configurée
* SMTP dynamique
* Documentation API

---

# 16. Structure API recommandée

```text
/app
  /Actions
  /DTOs
  /Enums
  /Filament
  /Http
  /Jobs
  /Mail
  /Models
  /Services
  /Settings
```

---

# 17. Bonus recommandés

## Optionnel

* Webhooks statut email
* Multi SMTP providers
* SMTP failover
* Scheduled emails
* Email campaigns
* Multi-language templates
* Versioning templates
* Drag & Drop email builder
* Tracking ouverture email
* Tracking clics

---

# 18. Documentation Swagger

Ajouter :

* Swagger/OpenAPI
* Documentation endpoints
* Exemples payloads
* Exemples réponses

---

# 19. Qualité du code

Respecter :

* SOLID
* Repository pattern si nécessaire
* Services layer
* DTOs
* Form Requests
* Enum PHP
* Laravel Policies
* Clean Architecture

---

# 20. Résultat attendu

L’application doit permettre :

1. d’enregistrer des consommateurs API ;
2. de créer plusieurs types d’emails ;
3. d’associer un template à chaque type ;
4. d’envoyer des emails via API ;
5. de gérer SMTP via Filament ;
6. de monitorer tous les emails ;
7. de sécuriser l’accès via API Key.
