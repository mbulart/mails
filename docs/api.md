# API Emails

## Endpoint

`POST /api/v1/mails/send`

Header obligatoire :

```http
X-API-KEY: pmk_xxx
```

## Payload

```json
{
  "type": "otp_code",
  "to": ["client@example.com"],
  "cc": [],
  "bcc": [],
  "variables": {
    "name": "Joseph",
    "otp": "552211"
  },
  "attachments": []
}
```

Les pièces jointes supportent les types `local`, `url` et `base64`.

## Réponse succès

```json
{
  "success": true,
  "message": "Mail queued successfully",
  "log_ids": [1]
}
```

## Réponse erreur

```json
{
  "success": false,
  "message": "Invalid API key"
}
```

## Administration Filament

Le panel est disponible sur `/admin`. Les formulaires sont organisés par sections, la page `Settings SMTP` utilise des onglets persistants, et les actions de listes sont regroupées dans un menu par ligne.

Chaque consommateur API peut aussi définir un SMTP dédié depuis sa fiche Filament. Si `Host SMTP` est renseigné, l'envoi utilise ce SMTP client ; sinon l'application utilise la configuration SMTP globale.

