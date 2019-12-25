define({ "api": [
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://www.jdr-delain.net/api/v2/auth/"
      }
    ],
    "type": "post",
    "url": "/auth/",
    "title": "Request a new token",
    "name": "requestToken",
    "group": "Auth",
    "description": "<p>Permet de demander un token d'identification qu'il faudra faire suivre pour les prochaines demandes</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "login",
            "description": "<p>Login du compte</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Password du compte</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n  \"login\": \"monlogin\",\n  \"password\": \"monpassword\"\n}",
          "type": "json"
        }
      ]
    },
    "header": {
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"Content-type\": \"application/json\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "FailedAuth",
            "description": "<p>Authentification échouée</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "compte",
            "description": "<p>Numéro du compte</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token à garder</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"compte\": \"2\",\n  \"token\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./auth.php",
    "groupTitle": "Auth"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://www.jdr-delain.net/api/v2/compte/"
      }
    ],
    "type": "get",
    "url": "/compte/",
    "title": "retourne les détails du compte",
    "name": "CompteDetail",
    "group": "Compte",
    "description": "<p>Permet de demander le détail du compte</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "X-delain-auth",
            "description": "<p>Token</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"X-delain-auth\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "NoToken",
            "description": "<p>Token non transmis</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNotFound",
            "description": "<p>Token non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "AccountNotFound",
            "description": "<p>Compte non trouvé dans la base</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "json",
            "optional": false,
            "field": "Tableau",
            "description": "<p>des données</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"compte\": \"2\",\n  \"token\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./compte.php",
    "groupTitle": "Compte"
  }
] });
