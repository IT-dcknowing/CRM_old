    <!-- TAB CONTENT START -->
<div class="row py-0 py-md-0 py-lg-3">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">

        <x-cards.data :title="__('modules.appreciations.appreciation')">

            <div class="table-responsive">
                <x-table class="table-bordered">
                    <x-slot name="thead">
                        <th>@lang('modules.appreciations.appreciationType')</th>
                        <th>@lang('modules.appreciations.awardDate')</th>
                    </x-slot>

                    @forelse ($appreciations as $count => $appreciation)
                        <tr class="tableRow{{$appreciation->id}}">
                            <td>
                                <x-award-icon :award="$appreciation->award"/>
                                <span class="align-self-center ml-2">{{ $appreciation->award->title }}</span>
                            </td>
                            <td>{{ $appreciation->award_date->translatedFormat($company->date_format) }}</td>
                        </tr>
                    @empty
                        <x-cards.no-record-found-list colspan="2"/>

                    @endforelse
                </x-table>
            </div>

        </x-cards.data>
    </div>
</div>
<!-- TAB CONTENT END -->
