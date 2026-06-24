@extends('transport.layout.index')
@section('main-transport-container')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('transport.layout.navbar')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('transport.layout.left-slidebar')
                <!--end::Sidebar-->
                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">
                        <!--begin::Toolbar-->
                        <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
                            <!--begin::Toolbar container-->
                            <div id="kt_app_toolbar_container"
                                class="app-container container-fluid d-flex align-items-stretch">
                                <!--begin::Toolbar wrapper-->
                                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                                    <!--begin::Page title-->
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <!--begin::Title-->
                                        <h1
                                            class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                                            @lang('lang.vechile')
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">@lang('lang.home')</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('driver.report.vechile', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.vechile')
                                                </a>
                                            </li>
                                            <!--begin::Item-->
                                            <!--end::Item-->
                                        </ul>
                                        <!--end::Breadcrumb-->
                                    </div>
                                    <!--end::Page title-->
                                </div>
                                <!--end::Toolbar wrapper-->
                            </div>
                            <!--end::Toolbar container-->
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <!--begin::Card-->
                                <div class="card">
                                    <!--begin::Card header-->
                                    <div class="card-header border-0 pt-6">
                                        <!--begin::Card title-->
                                        <div class="card-titl">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                                <input type="text" id="searchInput"
                                                    data-kt_tr_report_table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--begin::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body py-4">
                                        <!--begin::Table-->
                                        <div class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                id="kt_tr_report_table">
                                                <thead>
                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-125px">Id</th>
                                                        <th class="min-w-125px">Name</th>
                                                        <th class="min-w-125px max-w-500px">Location</th>
                                                        <th class="min-w-125px">Fuel</th>
                                                        <th class="min-w-125px">Speed</th>
                                                        <th class="min-w-125px">Status</th>
                                                        <th class="text-end min-w-100px">Last recieved time</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold" id="userTableBody">
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Truck 100</td>
                                                        <td
                                                            style="max-width: 200px; word-wrap: break-word; vertical-align: middle;">
                                                            {{ $locations[0]['name'] ?? 'Unknown' }}
                                                        </td>
                                                        @php
                                                            $progressClass = 'bg-success'; // Default to green
                                                            if ($fuelData < 30) {
                                                                $progressClass = 'bg-danger'; // Red
                                                            } elseif ($fuelData < 50) {
                                                                $progressClass = 'bg-warning'; // Yellow
                                                            }
                                                        @endphp

                                                        <td>
                                                            <div class="progress h-25px">
                                                                <div class="progress-bar {{ $progressClass }}"
                                                                    role="progressbar" style="width: {{ $fuelData }}%;"
                                                                    aria-valuenow="{{ $fuelData }}" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                    <span>{{ $fuelData }}%</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                           @if($speed !== null)
                                                              {{$speed}} MPH
                                                           @else
                                                              N/A
                                                           @endif
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <!-- Display Ignition On or Ignition Off based on the value in $ignitionOnIds array -->
                                                            @if (isset($ignitionOnIds[0]))
                                                                @if ($ignitionOnIds[0] == 'IgnitionOn')
                                                                    <div class="badge badge-light-success">Moving</div>
                                                                @else
                                                                    <div class="badge badge-light-danger">Not moving</div>
                                                                @endif
                                                            @else
                                                                <div class="badge badge-secondary">Off</div>
                                                            @endif

                                                        </td>
                                                        <td class="text-end" style="vertical-align: middle;">
                                                            {{ \Carbon\Carbon::parse($eventDateTime)->format('h:i A d-m-Y') }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- Add at the end of your Blade template -->
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                </div>
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection
@section('footer-script-link')
    <script src="{{ asset('assets/js/custom/apps/superAdmin/tr/report/list.js') }}"></script>
@endsection
