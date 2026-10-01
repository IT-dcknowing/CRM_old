@foreach ($tasks as $task)
    {{-- Seul l'employe assigne peut deplacer la carte (changer son statut). --}}
    <x-cards.task-card :task="$task" :draggable="(est_assigne_tache($task) ? 'true' : 'false')" :company="$company"/>
@endforeach
