<x-mail::message>
# Bonjour {{ $user->nom_complet }},

Votre compte a été créé avec succès.

**Voici vos identifiants de connexion :**

- **Email :** {{ $user->email }}
- **Mot de passe :** {{ $plainPassword }}

<x-mail::button :url="url('/login')">
Se connecter
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
