<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\User;
use App\Notifications\WhatsAppNotification;
use Illuminate\Http\Request;

class NotificationController extends AccountBaseController
{

    public function showNotifications()
    {
        $this->userType = 'all';

        if (in_array('client', user_roles())) {
            $this->userType = 'client';
        }

        $view = view('notifications.user_notifications', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }

    public function all()
    {
        $this->pageTitle = __('app.newNotifications');
        $this->userType = 'all';

        if (in_array('client', user_roles())) {
            $this->userType = 'client';
        }

        /* Toutes les notifications, et non les seules non-lues.

           L'ecran n'affichait que « unreadNotifications » : apres un
           passage par la cloche, qui marque tout comme lu, « Tout
           afficher » ouvrait une page vide sur un compte qui en portait
           pourtant des dizaines.

           Elles sont paginees : certains comptes en cumulent plusieurs
           centaines, et tout rendre d'un bloc chargeait la page pour
           rien. */
        $this->notifications = user()->notifications()->paginate(30);
        $this->nonLues = user()->unreadNotifications()->count();

        return view('notifications.all_user_notifications', $this->data);
    }

    public function markAllRead()
    {
        $this->user->unreadNotifications->markAsRead();
        return Reply::success(__('messages.notificationRead'));
    }

    public function markRead(Request $request)
    {
        $this->user->unreadNotifications->where('id', $request->id)->markAsRead();
        return Reply::dataOnly(['status' => 'success']);
    }

    public function sendWhatsAppNotification($userId)
    {
        // Trouver l'utilisateur par son ID
        $user = User::find($userId);

        // Vérifiez si l'utilisateur existe et a un numéro de téléphone
        if ($user && $user->mobile) {
            // Créez la notification avec le message
            $message = 'Ceci est un message WhatsApp !';
            $user->notify(new WhatsAppNotification($message));

            return response()->json(['message' => 'Notification WhatsApp envoyée avec succès.']);
        }

        return response()->json(['message' => 'Utilisateur non trouvé ou numéro de téléphone manquant.'], 404);
    }

}
