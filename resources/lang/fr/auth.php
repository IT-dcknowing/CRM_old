<?php

/*
 * Traductions francaises locales.
 *
 * Le serveur de production possede ses propres fichiers de langue,
 * bien plus complets. Ce dossier n'est pas suivi par Git
 * (voir .gitignore : resources/lang/*), il ne partira donc jamais
 * en production et n'ecrasera rien.
 *
 * Toute cle absente ici retombe automatiquement sur l'anglais.
 */

return [
    'email' => 'Adresse e-mail',
    'next' => 'Suivant',
    'failed' => 'Ces identifiants ne correspondent à aucun compte.',
    'failedLoginDisabled' => 'La connexion a été désactivée par votre administrateur.',
    'failedBlocked' => 'Votre compte est désactivé. Contactez votre administrateur pour le réactiver.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',
    'recaptchaFailed' => 'Le reCAPTCHA n’a pas été validé.',
    'sociaLoginFail' => 'Votre compte n’existe pas. Veuillez vous inscrire.',
    'signInGoogle' => 'Se connecter avec Google',
    'signInFacebook' => 'Se connecter avec Facebook',
    'signInLinkedin' => 'Se connecter avec LinkedIn',
    'signInTwitter' => 'Se connecter avec Twitter',
    'useEmail' => 'ou utilisez votre adresse e-mail',
];
