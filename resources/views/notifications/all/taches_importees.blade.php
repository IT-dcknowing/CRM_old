@php
    $count = $notification->data['nombre'] ?? 1;
    $titre = trans_choice('Une nouvelle tâche vous a été assignée|:count nouvelles tâches vous ont été assignées', $count, ['count' => $count]);
    $text = $count > 1 ? ($count . ' ' . __('tâches importées')) : ($notification->data['heading'] ?? __('Nouvelle tâche'));
    $link = !empty($notification->data['id']) ? route('tasks.show', $notification->data['id']) : route('tasks.index');
@endphp
<x-cards.notification :notification="$notification" :link="$link"
                      :image="user() ? user()->image_url : null"
                      :title="$titre" :text="$text"
                      :time="$notification->created_at"/>
