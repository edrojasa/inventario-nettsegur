@extends('layouts/default')
{{-- Page title --}}
@section('title')
{{ trans('general.dashboard') }}
@parent
@stop


{{-- Page content --}}
@section('content')

@if ($snipeSettings->dashboard_message!='')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        {!!  Helper::parseEscapedMarkedown($snipeSettings->dashboard_message)  !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">

    <div class="col-lg-2 col-xs-6">
        @if ($counts['asset'] !== null)
        <a href="{{ route('hardware.index') }}">
        @endif
            <div class="dashboard small-box bg-teal" @if($counts['asset'] === null) style="opacity:0.65" @endif>
                <div class="inner">
                    <h3>{{ $counts['asset'] !== null ? number_format($counts['asset']) : '—' }}</h3>
                    <p>{{ trans('general.assets') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="assets" />
                </div>
                @if ($counts['asset'] !== null)
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
                @endif
            </div>
        @if ($counts['asset'] !== null)
        </a>
        @endif
    </div>

    <div class="col-lg-2 col-xs-6">
        @if ($counts['license'] !== null)
        <a href="{{ route('licenses.index') }}">
        @endif
            <div class="dashboard small-box bg-maroon" @if($counts['license'] === null) style="opacity:0.65" @endif>
                <div class="inner">
                    <h3>{{ $counts['license'] !== null ? number_format($counts['license']) : '—' }}</h3>
                    <p>{{ trans('general.licenses') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="licenses" />
                </div>
                @if ($counts['license'] !== null)
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
                @endif
            </div>
        @if ($counts['license'] !== null)
        </a>
        @endif
    </div>

    <div class="col-lg-2 col-xs-6">
        @if ($counts['accessory'] !== null)
        <a href="{{ route('accessories.index') }}">
        @endif
            <div class="dashboard small-box bg-orange" @if($counts['accessory'] === null) style="opacity:0.65" @endif>
                <div class="inner">
                    <h3>{{ $counts['accessory'] !== null ? number_format($counts['accessory']) : '—' }}</h3>
                    <p>{{ trans('general.accessories') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="accessories" />
                </div>
                @if ($counts['accessory'] !== null)
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
                @endif
            </div>
        @if ($counts['accessory'] !== null)
        </a>
        @endif
    </div>

    <div class="col-lg-2 col-xs-6">
        @if ($counts['consumable'] !== null)
        <a href="{{ route('consumables.index') }}">
        @endif
            <div class="dashboard small-box bg-purple" @if($counts['consumable'] === null) style="opacity:0.65" @endif>
                <div class="inner">
                    <h3>{{ $counts['consumable'] !== null ? number_format($counts['consumable']) : '—' }}</h3>
                    <p>{{ trans('general.consumables') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="consumables" />
                </div>
                @if ($counts['consumable'] !== null)
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
                @endif
            </div>
        @if ($counts['consumable'] !== null)
        </a>
        @endif
    </div>

    <div class="col-lg-2 col-xs-6">
        <a href="{{ route('remisiones.index') }}">
            <div class="dashboard small-box bg-navy">
                <div class="inner">
                    <h3>{{ number_format($counts['remisiones_7d'] ?? 0) }}</h3>
                    <p>{{ trans('dashboard.kpi_remisiones_7d') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="order" />
                </div>
                <span class="small-box-footer">
                    {{ trans('dashboard.kpi_remisiones_footer') }}
                    <x-icon type="arrow-circle-right" />
                </span>
            </div>
        </a>
    </div>

    <div class="col-lg-2 col-xs-6">
        @if ($counts['user'] !== null)
        <a href="{{ route('users.index') }}">
        @endif
            <div class="dashboard small-box bg-light-blue" @if($counts['user'] === null) style="opacity:0.65" @endif>
                <div class="inner">
                    <h3>{{ $counts['user'] !== null ? number_format($counts['user']) : '—' }}</h3>
                    <p>{{ trans('general.people') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <x-icon type="users" />
                </div>
                @if ($counts['user'] !== null)
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
                @endif
            </div>
        @if ($counts['user'] !== null)
        </a>
        @endif
    </div>

</div>

@if ($showInventoryOnboarding ?? false)

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ trans('general.dashboard_info') }}</h2>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="progress">
                                <div class="progress-bar progress-bar-yellow" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                    <span class="sr-only">{{ trans('general.60_percent_warning') }}</span>
                                </div>
                            </div>


                            <p><strong>{{ trans('general.dashboard_empty') }}</strong></p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            @can('create', \App\Models\Asset::class)
                            <a class="btn bg-teal" style="width: 100%" href="{{ route('hardware.create') }}">{{ trans('general.new_asset') }}</a>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('create', \App\Models\License::class)
                                <a class="btn bg-maroon" style="width: 100%" href="{{ route('licenses.create') }}">{{ trans('general.new_license') }}</a>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('create', \App\Models\Accessory::class)
                                <a class="btn bg-orange" style="width: 100%" href="{{ route('accessories.create') }}">{{ trans('general.new_accessory') }}</a>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('create', \App\Models\Consumable::class)
                                <a class="btn bg-purple" style="width: 100%" href="{{ route('consumables.create') }}">{{ trans('general.new_consumable') }}</a>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('create', \App\Models\Component::class)
                                <a class="btn bg-yellow" style="width: 100%" href="{{ route('components.create') }}">{{ trans('general.new_component') }}</a>
                            @endcan
                        </div>
                        <div class="col-md-2">
                            @can('create', \App\Models\User::class)
                                <a class="btn bg-light-blue" style="width: 100%" href="{{ route('users.create') }}">{{ trans('general.new_user') }}</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else

@if (($showActivityReportTable ?? false) || ($showFullSnipeWidgets ?? false))
<div class="row">
  @if ($showActivityReportTable ?? false)
  <div class="{{ ($showFullSnipeWidgets ?? false) ? 'col-md-8' : 'col-md-12' }}">
    <div class="box box-default">
      <div class="box-header with-border">
        <h2 class="box-title">{{ trans('general.recent_activity') }}</h2>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" aria-hidden="true">
                <x-icon type="minus" />
                <span class="sr-only">{{ trans('general.collapse') }}</span>
            </button>
        </div>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">

                <table
                    data-cookie-id-table="dashActivityReport"
                    data-height="500"
                    data-pagination="false"
                    data-side-pagination="server"
                    data-id-table="dashActivityReport"
                    data-sort-order="desc"
                    data-show-columns="false"
                    data-fixed-number="false"
                    data-fixed-right-number="false"
                    data-sort-name="created_at"
                    id="dashActivityReport"
                    class="table table-striped snipe-table"
                    data-url="{{ route('api.activity.index', ['limit' => 25]) }}">
                    <thead>
                    <tr>
                        <th data-field="icon" data-visible="true" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter"><span  class="sr-only">{{ trans('admin/hardware/table.icon') }}</span></th>
                        <th class="col-sm-3" data-visible="true" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                        <th class="col-sm-2" data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.created_by') }}</th>
                        <th class="col-sm-2" data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>
                        <th class="col-sm-3" data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
                        <th class="col-sm-2" data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>
                    </tr>
                    </thead>
                </table>
          </div>
          <div class="text-center col-md-12" style="padding-top: 10px;">
            <a href="{{ route('reports.activity') }}" class="btn btn-theme btn-sm" style="width: 100%">{{ trans('general.viewall') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  @if ($showFullSnipeWidgets ?? false)
  <div class="{{ ($showActivityReportTable ?? false) ? 'col-md-4' : 'col-md-12' }}">
        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">
                    {{ (\App\Models\Setting::getSettings()->dash_chart_type == 'name') ? trans('general.assets_by_status') : trans('general.assets_by_status_type') }}
                </h2>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" aria-hidden="true">
                        <x-icon type="minus" />
                        <span class="sr-only">{{ trans('general.collapse') }}</span>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="statusPieChart" height="260"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
  @endif

</div>
@endif

@if ($showFullSnipeWidgets ?? false)
<div class="row">
    <div class="col-md-6">

		@if ((($snipeSettings->scope_locations_fmcs!='1') && ($snipeSettings->full_multiple_companies_support=='1')))
			<div class="box box-default">
				<div class="box-header with-border">
					<h2 class="box-title">{{ trans('general.companies') }}</h2>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <x-icon type="minus" />
							<span class="sr-only">{{ trans('general.collapse') }}</span>
						</button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<table
									data-cookie-id-table="dashCompanySummary"
									data-height="400"
                                    data-pagination="false"
									data-side-pagination="server"
									data-sort-order="desc"
                                    data-show-columns="false"
                                    data-fixed-number="false"
                                    data-fixed-right-number="false"
									data-sort-field="assets_count"
									id="dashCompanySummary"
									class="table table-striped snipe-table"
									data-url="{{ route('api.companies.index', ['sort' => 'assets_count', 'order' => 'asc']) }}">

								<thead>
								<tr>
									<th class="col-sm-3" data-visible="true" data-field="name" data-formatter="companiesLinkFormatter" data-sortable="true">{{ trans('general.name') }}</th>
									<th class="col-sm-1" data-visible="true" data-field="users_count" data-sortable="true">
                                        <x-icon type="users" />
										<span class="sr-only">{{ trans('general.people') }}</span>
									</th>
									<th class="col-sm-1" data-visible="true" data-field="assets_count" data-sortable="true">
                                        <x-icon type="assets" />
										<span class="sr-only">{{ trans('general.asset_count') }}</span>
									</th>
									<th class="col-sm-1" data-visible="true" data-field="accessories_count" data-sortable="true">
                                        <x-icon type="accessories" />
										<span class="sr-only">{{ trans('general.accessories_count') }}</span>
									</th>
									<th class="col-sm-1" data-visible="true" data-field="consumables_count" data-sortable="true">
                                        <x-icon type="consumables" />
										<span class="sr-only">{{ trans('general.consumables_count') }}</span>
									</th>
									<th class="col-sm-1" data-visible="true" data-field="components_count" data-sortable="true">
                                        <x-icon type="components" />
										<span class="sr-only">{{ trans('general.components_count') }}</span>
									</th>
									<th class="col-sm-1" data-visible="true" data-field="licenses_count" data-sortable="true">
                                        <x-icon type="licenses" />
										<span class="sr-only">{{ trans('general.licenses_count') }}</span>
									</th>
								</tr>
								</thead>
							</table>
						</div>
						<div class="text-center col-md-12" style="padding-top: 10px;">
							<a href="{{ route('companies.index') }}" class="btn btn-theme btn-sm" style="width: 100%">{{ trans('general.viewall') }}</a>
						</div>
					</div>

				</div>
			</div>

		@else
			 <div class="box box-default">
				<div class="box-header with-border">
					<h2 class="box-title">{{ trans('dashboard.locations_box_title') }}</h2>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <x-icon type="minus" />
							<span class="sr-only">{{ trans('general.collapse') }}</span>
						</button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">

							<table
									data-cookie-id-table="dashLocationSummary"
									data-height="400"
									data-side-pagination="server"
                                    data-pagination="false"
									data-sort-order="desc"
                                    data-fixed-number="false"
                                    data-fixed-right-number="false"
									data-sort-field="assets_count"
									id="dashLocationSummary"
                                    data-show-columns="false"
									class="table table-striped snipe-table table-dashboard-summary"
									data-url="{{ route('api.locations.index', ['sort' => 'assets_count', 'order' => 'desc']) }}">
								<thead>
								<tr>
									<th class="col-sm-4" data-visible="true" data-field="name" data-formatter="locationsLinkFormatter" data-sortable="true">{{ trans('general.name') }}</th>
									<th class="col-sm-2 text-center" data-visible="true" data-field="assets_count" data-sortable="true" data-formatter="dashboardAssetCountBadgeFormatter" data-align="center">
                                        <x-icon type="assets" />
										<span class="sr-only">{{ trans('dashboard.th_equipment_count') }}</span>
									</th>
									<th class="col-sm-2 text-center" data-visible="true" data-field="assigned_assets_count" data-sortable="true" data-align="center">
										{{ trans('general.assigned') }}
									</th>
									<th class="col-sm-2 text-center" data-visible="true" data-field="users_count" data-sortable="true" data-align="center">
                                        <x-icon type="users" />
										<span class="sr-only">{{ trans('general.people') }}</span>
									</th>
								</tr>
								</thead>
							</table>
						</div>
						<div class="text-center col-md-12" style="padding-top: 10px;">
							<a href="{{ route('locations.index') }}" class="btn btn-theme btn-sm" style="width: 100%">{{ trans('general.viewall') }}</a>
						</div>
					</div>

				</div>
			</div>

		@endif

    </div>
    <div class="col-md-6">

        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">{{ trans('dashboard.categories_equipment_title') }}</h2>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <x-icon type="minus" />
                        <span class="sr-only">{{ trans('general.collapse') }}</span>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">

                        <table
                                data-cookie-id-table="dashCategorySummary"
                                data-height="400"
                                data-pagination="false"
                                data-side-pagination="server"
                                data-show-columns="false"
                                data-fixed-number="false"
                                data-fixed-right-number="false"
                                data-sort-order="desc"
                                data-sort-field="assets_count"
                                id="dashCategorySummary"
                                class="table table-striped snipe-table table-dashboard-summary"
                                data-url="{{ route('api.categories.index', ['sort' => 'assets_count', 'order' => 'desc', 'category_type' => 'asset']) }}">
                            <thead>
                            <tr>
                                <th class="col-sm-6" data-visible="true" data-field="name" data-formatter="categoriesLinkFormatter" data-sortable="true">{{ trans('general.name') }}</th>
                                <th class="col-sm-2 text-center" data-visible="true" data-field="category_type" data-sortable="true" data-formatter="dashboardCategoryTypeBadgeFormatter">{{ trans('general.type') }}</th>
                                <th class="col-sm-4 text-center" data-visible="true" data-field="assets_count" data-sortable="true" data-formatter="dashboardAssetCountBadgeFormatter" data-align="center">
                                    <x-icon type="assets" />
                                    <span class="sr-only">{{ trans('dashboard.th_equipment_count') }}</span>
                                </th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                    <div class="text-center col-md-12" style="padding-top: 10px;">
                        <a href="{{ route('categories.index') }}" class="btn btn-theme btn-sm" style="width: 100%">{{ trans('general.viewall') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
@endif

@endif


@stop

@section('moar_scripts')
<script nonce="{{ csrf_token() }}">
    function dashboardAssetCountBadgeFormatter(value) {
        var n = (value === null || value === undefined || value === '') ? 0 : parseInt(value, 10);
        if (isNaN(n)) {
            n = 0;
        }
        var cls = n > 0 ? 'label-success' : 'label-default';
        return '<span class="label ' + cls + ' dashboard-asset-count-badge">' + n + '</span>';
    }
    function dashboardCategoryTypeBadgeFormatter(value) {
        if (!value) {
            return '';
        }
        var esc = String(value).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return '<span class="label label-default dashboard-category-type-badge">' + esc + '</span>';
    }
</script>
<style nonce="{{ csrf_token() }}">
    .table-dashboard-summary .dashboard-asset-count-badge { font-size: 13px; padding: 0.35em 0.65em; min-width: 2.25em; display: inline-block; }
    .table-dashboard-summary .dashboard-category-type-badge { font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .table-dashboard-summary tbody td { vertical-align: middle !important; }
</style>
@include ('partials.bootstrap-table', ['simple_view' => true, 'nopages' => true])
@stop

@push('js')
@if ($showFullSnipeWidgets ?? false)
        <script src="{{ url(mix('js/dist/Chart.min.js')) }}"></script>
<script nonce="{{ csrf_token() }}">
    // ---------------------------
    // - ASSET STATUS CHART -
    // ---------------------------
      var pieChartCanvas = $("#statusPieChart").get(0).getContext("2d");
      var pieChart = new Chart(pieChartCanvas);
      var ctx = document.getElementById("statusPieChart");
      var pieOptions = {
              legend: {
                  position: 'top',
                  responsive: true,
                  maintainAspectRatio: true,
              },
              tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        counts = data.datasets[0].data;
                        total = 0;
                        for(var i in counts) {
                            total += counts[i];
                        }
                        prefix = data.labels[tooltipItem.index] || '';
                        return prefix+" "+Math.round(counts[tooltipItem.index]/total*100)+"%";
                    }
                }
              }
          };

      $.ajax({
          type: 'GET',
          url: '{{ (\App\Models\Setting::getSettings()->dash_chart_type == 'name') ? route('api.statuslabels.assets.byname') : route('api.statuslabels.assets.bytype') }}',
          headers: {
              "X-Requested-With": 'XMLHttpRequest',
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
          },
          dataType: 'json',
          success: function (data) {
              var myPieChart = new Chart(ctx,{
                  type   : 'pie',
                  data   : data,
                  options: pieOptions
              });
          },
          error: function (data) {
              // window.location.reload(true);
          },
      });
        var last = document.getElementById('statusPieChart').clientWidth;
        addEventListener('resize', function() {
        var current = document.getElementById('statusPieChart').clientWidth;
        if (current != last) location.reload();
        last = current;
    });
</script>
@endif
@endpush
