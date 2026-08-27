/* ══════════════════════════════════════════════════════════════
   BASCULE CLAIR / SOMBRE
   ──────────────────────────────────────────────────────────────
   Tout existait sauf le lien entre les deux : l'interrupteur
   #dark-theme-toggle est present dans la barre laterale, la route
   profile.dark-theme enregistre le choix, et le gabarit pose deja
   <body class="dark-theme"> selon user()->dark_theme. Aucun script
   n'ecoutait l'interrupteur : le clic ne declenchait donc rien.

   Ce fichier fait trois choses :

     1. bascule la classe sur <body> immediatement, sans attendre
        le serveur — le changement doit etre instantane ;
     2. enregistre le choix pour les pages suivantes ;
     3. remet l'interrupteur dans son etat anterieur si
        l'enregistrement echoue, pour ne jamais mentir sur l'etat reel.

   Le choix est conserve en base, sur l'utilisateur : il suit donc la
   navigation et les appareils, sans dependre du stockage local.
   ══════════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    var CLASSE = 'dark-theme';

    function jeton() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function appliquer(sombre) {
        document.body.classList.toggle(CLASSE, sombre);
        // La barre laterale porte un theme clair nomme tant qu'on n'est
        // pas en sombre ; on la neutralise pour eviter un fond clair
        // residuel derriere le nouveau fond.
        var aside = document.querySelector('aside.dck-chrome');

        if (aside) {
            aside.classList.toggle('is-dark', sombre);
        }
    }

    document.addEventListener('change', function (e) {
        var bascule = e.target;

        if (!bascule || bascule.id !== 'dark-theme-toggle') {
            return;
        }

        var sombre = bascule.checked;
        appliquer(sombre);

        var url = bascule.getAttribute('data-url');

        if (!url) {
            return; // pas d'enregistrement possible : l'affichage a change, c'est deja ca
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': jeton(),
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ darkTheme: sombre ? 1 : 0 })
        })
            .then(function (r) {
                if (!r.ok) {
                    throw new Error('HTTP ' + r.status);
                }
            })
            .catch(function () {
                // L'enregistrement a echoue : on revient a l'etat
                // precedent plutot que d'afficher un choix non conserve.
                bascule.checked = !sombre;
                appliquer(!sombre);
            });
    }, true);
}());
