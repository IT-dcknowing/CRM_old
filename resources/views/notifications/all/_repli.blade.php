{{--
    Repli pour les notifications sans vue dediee.

    La liste n'affichait une notification que si un gabarit portait
    exactement son nom. Faute de quoi elle etait ignoree en silence :
    sur quatre-vingt-quinze notifications, soixante-neuf ne
    s'affichaient nulle part, sans que rien ne le signale.

    Ce gabarit affiche ce que l'on peut lire de n'importe quelle
    notification — un intitule, une date — plutot que de la taire.
    Une vue dediee, quand elle existe, reste prioritaire.
--}}

@php
    $donnees = $notification->data ?? [];
    $classe = class_basename($notification->type);

    /* Le libelle le plus parlant que porte la notification. */
    $texte = collect(['heading', 'title', 'name', 'project_name', 'message'])
        ->map(fn ($cle) => $donnees[$cle] ?? null)
        ->first(fn ($valeur) => filled($valeur) && is_string($valeur));

    /* A defaut, le nom de la classe rendu lisible : « TaskInitNotification »
       devient « Task Init Notification ». Ce n'est pas ideal, mais c'est
       preferable a une ligne vide. */
    $intitule = \Illuminate\Support\Str::headline($classe);

    /* Le lien n'est propose que si l'on sait vers quoi pointer.

       On ne devine pas a partir du seul identifiant : « id » designe
       une tache dans une notification de tache, un projet dans une
       notification de projet. Envoyer l'un vers l'autre ouvrirait la
       mauvaise fiche, ou une page d'erreur. */
    $lien = 'javascript:;';

    if (isset($donnees['id']) && is_numeric($donnees['id'])) {
        if (\Illuminate\Support\Str::contains($classe, 'Task')) {
            $lien = route('tasks.show', $donnees['id']);
        }
        elseif (\Illuminate\Support\Str::contains($classe, 'Project')) {
            $lien = route('projects.show', $donnees['id']);
        }
    }
@endphp

<x-cards.notification :notification="$notification"
                      :link="$lien"
                      type="icon"
                      image='<i class="fa fa-bell"></i>'
                      :title="$intitule"
                      :text="$texte ?? ''"
                      :time="$notification->created_at" />
