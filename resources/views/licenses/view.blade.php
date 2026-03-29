@extends('layouts/default')

{{-- Page title --}}
@section('title')
  {{ trans('admin/licenses/general.view') }}
  - {{ $license->name }}
  @parent
@stop

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
                            name="seats"
                            class="active"
                            icon_type="checkedout"
                            label="{{ trans('general.assigned') }}"
                            count="{{ $license->assignedCount()->count() }}"
                    />

                    <x-tabs.nav-item
                            name="available"
                            icon_type="available"
                            label="{{ trans('general.available') }}"
                            count="{{ $license->availCount()->count() }}"
                    />

                <x-tabs.nav-item
                        name="files"
                        icon_type="files"
                        label="{{ trans('general.files') }}"
                        count="{{ $license->uploads()->count() }}"
                />

                <x-tabs.nav-item
                        name="history"
                        icon_type="history"
                        label="{{ trans('general.history') }}"
                        tooltip="{{ trans('general.history') }}"
                />


                @can('update', $license)
                    <x-tabs.nav-item-upload />
                @endcan

                <x-tabs.nav-item
                        name="users"
                        icon_type="users"
                        label="Usuarios"
                        tooltip="Usuarios registrados para esta licencia"
                />

                </x-slot:tabnav>

                <x-slot:tabpanes>

                    <x-tabs.pane name="seats" class="in active">
                        <x-slot:header>
                            {{ trans('general.assigned') }}
                        </x-slot:header>
                        <x-slot:content>

                            <x-table
                                    api_url="{{ route('api.licenses.seats.index', [$license->id, 'status' => 'assigned']) }}"
                                    :presenter="\App\Presenters\LicensePresenter::dataTableLayoutSeats()"
                                    export_filename="export-{{ str_slug($license->name) }}-assigned-{{ date('Y-m-d') }}"
                            />

                        </x-slot:content>
                    </x-tabs.pane>


                    <x-tabs.pane name="available">
                        <x-slot:header>
                            {{ trans('general.available') }}
                        </x-slot:header>
                        <x-slot:content>

                            <x-table
                                    api_url="{{ route('api.licenses.seats.index', [$license->id, 'status' => 'available']) }}"
                                    :presenter="\App\Presenters\LicensePresenter::dataTableLayoutSeats()"
                                    export_filename="export-{{ str_slug($license->name) }}-available-{{ date('Y-m-d') }}"
                            />

                        </x-slot:content>
                    </x-tabs.pane>


                    <!-- start history tab pane -->
                    <x-tabs.pane name="history">
                        <x-slot:header>
                            {{ trans('general.history') }}
                        </x-slot:header>
                        <x-slot:content>
                            <x-table
                                    name="locationHistory_{{ $license->id }}"
                                    api_url="{{ route('api.activity.index', ['item_id' => $license->id, 'item_type' => 'license']) }}"
                                    :presenter="\App\Presenters\HistoryPresenter::dataTableLayout()"
                                    export_filename="export-licenses-{{ str_slug($license->name) }}-{{ date('Y-m-d') }}"
                            />
                        </x-slot:content>
                    </x-tabs.pane>
                    <!-- end history tab pane -->


                    <!-- start files tab pane -->
                    @can('licenses.files', $license)
                    <x-tabs.pane name="files">
                        <x-slot:header>
                            {{ trans('general.files') }}
                        </x-slot:header>
                        <x-slot:content>
                            <x-filestable object_type="licenses" :object="$license" />
                        </x-slot:content>
                    </x-tabs.pane>
                    @endcan
                    <!-- end files tab pane -->

                    <!-- start users tab pane -->
                    <x-tabs.pane name="users">
                        <x-slot:header>
                            Usuarios
                        </x-slot:header>
                        <x-slot:content>
                            <div class="row">
                                <div class="col-md-12 text-right" style="margin-bottom: 10px;">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                                        <i class="fas fa-plus icon-white"></i> Agregar Usuario
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped snipe-table">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Contraseña</th>
                                            <th>Creado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($license->licenseUsers as $userCred)
                                            <tr>
                                                <td>{{ $userCred->username }}</td>
                                                <td>
                                                    <div class="input-group" style="width: 250px;">
                                                        <input type="password" class="form-control input-sm" id="pwd-{{ $userCred->id }}" value="{{ \Illuminate\Support\Facades\Crypt::decryptString($userCred->password) }}" readonly>
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default btn-sm toggle-password" type="button" data-target="pwd-{{ $userCred->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ $userCred->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('licenses.users.destroy', ['licenseId' => $license->id, 'id' => $userCred->id]) }}" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($license->licenseUsers->isEmpty())
                                            <tr>
                                                <td colspan="4" class="text-center">No hay usuarios agregados todavía.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </x-slot:content>
                    </x-tabs.pane>
                    <!-- end users tab pane -->

                </x-slot:tabpanes>
            </x-tabs>
        </x-page-column>

        <x-page-column class="col-md-3">
            <x-box>
                <x-box.info-panel :infoPanelObj="$license" img_path="{{ app('licenses_upload_url') }}">

                    <x-slot:before_list>

                        @can('update', $license)
                            <a href="{{ route('licenses.edit', $license->id) }}" class="btn btn-warning btn-sm btn-social btn-block hidden-print" style="margin-bottom: 5px;">
                                <x-icon type="edit" />
                                {{ trans('admin/licenses/general.edit') }}
                            </a>
                            <a href="{{ route('clone/license', $license->id) }}" class="btn btn-info btn-block btn-sm btn-social hidden-print" style="margin-bottom: 5px;">
                                <x-icon type="clone" />
                                {{ trans('admin/licenses/general.clone') }}</a>
                        @endcan

                        <div class="hidden-print" style="padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid transparent;"></div>
                        <a href="{{ route('remision.show', ['licenses' => [$license->id]]) }}" class="btn btn-sm btn-primary bg-purple btn-social btn-block hidden-print" style="margin-bottom: 5px;">
                            <x-icon type="file" />
                            Generar remisión
                        </a>

                        @can('checkout', $license)

                            @if (($license->availCount()->count() > 0) && (!$license->isInactive()))

                                <a href="{{ route('licenses.checkout', $license->id) }}" class="btn bg-maroon btn-sm btn-social btn-block hidden-print" style="margin-bottom: 5px;">
                                    <x-icon type="checkout" />
                                    {{ trans('general.checkout') }}
                                </a>

                                <a href="#" class="btn bg-maroon btn-sm btn-social btn-block hidden-print" style="margin-bottom: 5px;" data-toggle="modal" data-tooltip="true" title="{{ trans('admin/licenses/general.bulk.checkout_all.enabled_tooltip') }}" data-target="#checkoutFromAllModal">
                                    <x-icon type="checkout-all" />
                                    {{ trans('admin/licenses/general.bulk.checkout_all.button') }}
                                </a>

                            @else
                                <span data-tooltip="true" title="{{ ($license->availCount()->count() == 0) ? trans('admin/licenses/general.bulk.checkout_all.disabled_tooltip') : trans('admin/licenses/message.checkout.license_is_inactive') }}" class="btn bg-maroon btn-sm btn-social btn-block hidden-print disabled" style="margin-bottom: 5px;" data-tooltip="true" title="{{ trans('general.checkout') }}">
                                    <x-icon type="checkout" />
                                    {{ trans('general.checkout') }}
                                  </span>
                                                        <span data-tooltip="true" title="{{ ($license->availCount()->count() == 0) ? trans('admin/licenses/general.bulk.checkout_all.disabled_tooltip') : trans('admin/licenses/message.checkout.license_is_inactive') }}" class="btn bg-maroon btn-sm btn-social btn-block hidden-print disabled" style="margin-bottom: 5px;" data-tooltip="true" title="{{ trans('general.checkout') }}">
                                      <x-icon type="checkout" />
                                      {{ trans('admin/licenses/general.bulk.checkout_all.button') }}
                                  </span>
                            @endif
                        @endcan

                            @can('checkin', $license)

                                @if (($license->seats - $license->availCount()->count()) <= 0 )
                                    <span data-tooltip="true" title=" {{ trans('admin/licenses/general.bulk.checkin_all.disabled_tooltip') }}">
                                        <a href="#"  class="btn btn-primary bg-purple btn-sm btn-social btn-block hidden-print disabled"  style="margin-bottom: 25px;">
                                          <x-icon type="checkin" />
                                         {{ trans('admin/licenses/general.bulk.checkin_all.button') }}
                                        </a>
                                    </span>
                                @else
                                    <a href="#"  class="btn btn-primary bg-purple btn-sm btn-social btn-block hidden-print" style="margin-bottom: 25px;" data-toggle="modal" data-tooltip="true"  data-target="#checkinFromAllModal" data-content="{{ trans('general.sure_to_delete') }} data-title="{{  trans('general.delete') }}" onClick="return false;">
                                    <x-icon type="checkin" />
                                    {{ trans('admin/licenses/general.bulk.checkin_all.button') }}
                                    </a>
                                @endif
                            @endcan

                            @can('delete', $license)

                                @if ($license->availCount()->count() == $license->seats)
                                    <a class="btn btn-block btn-danger btn-sm btn-social delete-asset" data-icon="fa fa-trash" data-toggle="modal" data-title="{{ trans('general.delete') }}" data-content="{{ trans('general.delete_confirm', ['item' => $license->name]) }}" data-target="#dataConfirmModal" onClick="return false;">
                                        <x-icon type="delete" />
                                        {{ trans('general.delete') }}
                                    </a>
                                @else
                                    <span data-tooltip="true" title=" {{ trans('admin/licenses/general.delete_disabled') }}">
                                        <a href="#" class="btn btn-block btn-danger btn-sm btn-social delete-asset disabled" onClick="return false;">
                                          <x-icon type="delete" />
                                          {{ trans('general.delete') }}
                                        </a>
                                      </span>
                                @endif
                            @endcan




                    </x-slot:before_list>
                </x-box.info-panel>
            </x-box>

        </x-page-column>
    </x-container>

@can('checkout', \App\Models\License::class)
    @include ('modals.confirm-action',
          [
              'modal_name' => 'checkoutFromAllModal',
              'route' => route('licenses.bulkcheckout', $license->id),
              'title' => trans('general.modal_confirm_generic'),
              'body' => trans_choice('admin/licenses/general.bulk.checkout_all.modal', 2, ['available_seats_count' => $available_seats_count])
          ])
@endcan


  @can('update', \App\Models\License::class)
    @include ('modals.upload-file', ['item_type' => 'license', 'item_id' => $license->id])

    <!-- Modal Add User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="{{ route('licenses.users.store', ['licenseId' => $license->id]) }}">
            @csrf
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="addUserModalLabel">Añadir Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="text" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan

@stop


@section('moar_scripts')
  @include ('partials.bootstrap-table')
  <script>
    $(function() {
        $('.toggle-password').click(function() {
            var inputId = $(this).data('target');
            var input = $('#' + inputId);
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
  </script>
@stop
