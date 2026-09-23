@extends('layouts.app')


@section('content')

{{--
    Toutes les notifications de l'utilisateur, lues comprises.

    L'ecran ne montrait que les non-lues : ouvrir « Tout afficher »
    apres avoir consulte la cloche donnait une page vide, alors que le
    compte en portait des dizaines. Il liste desormais l'ensemble, et
    distingue les non-lues d'un liseré.

    Les types sans gabarit dedie passent par « _repli », faute de quoi
    ils etaient ignores en silence.
--}}

<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <x-cards.data padding="false" :title="__('Nouvelles notifications ')">

                @if ($notifications->total() > 0)
                    <div class="px-3 py-2 border-bottom-grey f-12 text-dark-grey bg-additional-grey d-flex justify-content-between align-items-center">
                        <div>
                            {{ $notifications->total() }} {{ __('app.notifications') }}
                            @if ($nonLues > 0)
                                — <span class="text-danger f-w-500">{{ $nonLues }} {{ __('app.unread') }}</span>
                            @endif
                        </div>
                        @if ($nonLues > 0)
                            <a href="javascript:;" class="mark-notification-read text-dark-grey f-12 text-decoration-none">
                                <i class="fa fa-check-double mr-1"></i>{{ __('Marquer tout comme lu') }}
                            </a>
                        @endif
                    </div>
                @endif

                @forelse ($notifications as $notification)

                    @php
                        $gabarit = 'notifications.' . $userType . '.'
                            . \Illuminate\Support\Str::snake(class_basename($notification->type));
                    @endphp

                    <div class="{{ is_null($notification->read_at) ? 'border-left-3 border-primary' : '' }}">
                        @if (view()->exists($gabarit))
                            @include($gabarit)
                        @else
                            @include('notifications.all._repli')
                        @endif
                    </div>

                    @foreach ($worksuitePlugins as $item)
                        @if(view()->exists(strtolower($item).'::notifications.'.\Illuminate\Support\Str::snake(class_basename($notification->type))))
                            @include(strtolower($item).'::notifications.'.\Illuminate\Support\Str::snake(class_basename($notification->type)))
                        @endif
                    @endforeach

                @empty
                    <div class="dropdown-item f-14 text-dark p-0">
                        <div class="card border-0 p-0 rounded-0">
                            <div class="card-horizontal align-items-center">
                                <div class="card-body border-0 pl-0 pr-0 py-2">
                                    <x-cards.no-record icon="bell-slash" :message="__('messages.noNotification')" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse

                @if ($notifications->hasPages())
                    <div class="p-3 border-top-grey d-flex justify-content-center align-items-center">
                        {{ $notifications->links('pagination::bootstrap-4') }}
                    </div>
                @endif

            </x-cards.data>
        </div>
    </div>

</div>

@endsection
