@extends('layouts/default')

{{-- Page title --}}
@section('title')
  {{ $consumable->name }}
  — {{ trans('general.consumable') }}
  — {{ trans('general.remaining_var', ['count' => $consumable->numRemaining()]) }}
  @parent
@endsection

@section('header_right')
    <i class="fa-regular fa-2x fa-square-caret-right pull-right" id="expand-info-panel-button" data-tooltip="true" title="{{ trans('button.show_hide_info') }}"></i>
@endsection

{{-- Page content --}}
@section('content')

    <x-container columns="2">
        <x-page-column class="col-md-9 main-panel">
            <x-tabs>
                <x-slot:tabnav>

                    <x-tabs.nav-item
                            name="delivery_transactions"
                            class="active"
                            icon_type=""
                            label="Entregas"
                    />

                    <x-tabs.nav-item
                            name="assigned"
                            class=""
                            icon_type="checkedout"
                            label="{{ trans('general.assigned') }}"
                            count="{{ $consumable->numCheckedOut() }}"
                    />

                    <x-tabs.files-tab count="{{ $consumable->uploads()->count() }}" />

                    <x-tabs.history-tab model="\App\Models\Consumable::class"/>

                    @can('update', $consumable)
                        <x-tabs.nav-item-upload />
                    @endcan

                </x-slot:tabnav>

                <x-slot:tabpanes>

                    <x-tabs.pane name="delivery_transactions" class="in active">
                        <x-slot:header>
                            Registro de entregas
                        </x-slot:header>
                        <x-slot:content>
                            <div class="table-responsive">
                                <table class="table table-striped snipe-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Destino</th>
                                            <th>Cantidad</th>
                                            <th>Entregado Por</th>
                                            <th>Notas</th>
                                            <th>Remisión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(\App\Models\ConsumableTransaction::where('consumable_id', $consumable->id)->orderBy('created_at', 'desc')->get() as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at }}</td>
                                            <td>
                                                @if($transaction->type == 'user' && $transaction->user)
                                                    <i class="fas fa-user"></i> {{ $transaction->user->present()->fullName }}
                                                @elseif($transaction->type == 'location' && $transaction->location)
                                                    <i class="fas fa-map-marker-alt"></i> {{ $transaction->location->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $transaction->quantity }}</td>
                                            <td>{{ $transaction->admin ? $transaction->admin->present()->fullName : '' }}</td>
                                            <td>{{ $transaction->notes }}</td>
                                            <td>
                                                <form action="{{ url('remision/pdf') }}" method="POST" target="_blank" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="consumables[]" value="{{ $transaction->id }}">
                                                    <button type="submit" class="btn btn-sm btn-primary" data-tooltip="true" title="Re-imprimir Remisión">
                                                        <i class="fas fa-file-pdf"></i> Imprimir
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-slot:content>
                    </x-tabs.pane>

                    <x-tabs.pane name="assigned" class="">

                        <x-slot:content>
                            <x-table
                                    :presenter="\App\Presenters\ConsumablePresenter::checkedOut()"
                                    :api_url="route('api.consumables.show.users', $consumable->id)"
                            />
                        </x-slot:content>

                    </x-tabs.pane>

                    <x-tabs.pane name="files">
                        <x-slot:header>
                            {{ trans('general.files') }}
                        </x-slot:header>
                        <x-slot:content>
                            <x-filestable object_type="consumables" :object="$consumable" />
                        </x-slot:content>
                    </x-tabs.pane>

                    <!-- start history tab pane -->
                    <x-tabs.pane name="history">
                        <x-slot:header>
                            {{ trans('general.history') }}
                        </x-slot:header>
                        <x-slot:content>
                            <x-table
                                    name="consumableHistory"
                                    api_url="{{ route('api.activity.index', ['item_id' => $consumable->id, 'item_type' => 'consumable']) }}"
                                    :presenter="\App\Presenters\HistoryPresenter::dataTableLayout()"
                                    export_filename="export-licenses-{{ str_slug($consumable->name) }}-{{ date('Y-m-d') }}"
                            />
                        </x-slot:content>
                    </x-tabs.pane>
                </x-slot:tabpanes>

            </x-tabs>
        </x-page-column>

        <x-page-column class="col-md-3">
            <x-box>
                <x-box.info-panel :infoPanelObj="$consumable" img_path="{{ app('consumables_upload_url') }}">

                    <x-slot:before_list>

                        <x-button.wide-checkout :item="$consumable" :route="route('consumables.checkout.show', $consumable->id)" />
                        <x-button.wide-edit :item="$consumable" :route="route('consumables.edit', $consumable->id)" />
                        <x-button.wide-clone :item="$consumable" :route="route('consumables.clone.create', $consumable->id)" />
                        <x-button.wide-delete :item="$consumable" />

                    </x-slot:before_list>

                </x-box.info-panel>
            </x-box>
        </x-page-column>
    </x-container>

  @can('update', \App\Models\User::class)
    @include ('modals.upload-file', ['item_type' => 'consumable', 'item_id' => $consumable->id])
  @endcan



@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table', ['simple_view' => true])
@endsection
