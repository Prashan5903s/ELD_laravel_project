
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
                                            @lang('lang.compliance') List</h1>
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
                                                <a href="{{ route('driver.index', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.driver')
                                                </a>
                                            </li>
                                            <!--begin::Item-->

                                            <!--end::Item-->
                                        </ul>
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 mt-4">
                                            <!--begin::Item-->

                                            <li class="breadcrumb-item text-muted">
                                                {{ $dateTimeDayBefore }}
                                            </li>

                                            <!--end::Item-->
                                            <!--begin::Item-->

                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                {{ $currentDateTime }}
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
                                    <!-- Begin violation card -->
                                    <div class="container">
                                        <div class="row text-center mt-5">
                                            <div class="col-md-4">
                                                <h4>HOS Violations</h4>
                                                <div class="chart-container" style="width: 80%; margin: 0 auto;">
                                                    <canvas id="hosViolationsChart" class="chart-canvas"></canvas>
                                                </div>
                                                <div class="mt-4 mb-4">
                                                    <p>In Violation: 0.07% (5h 19m)</p>
                                                    <p>Compliant: 99.92% (7290h 40m)</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h4>Unassigned Driving</h4>
                                                <div class="chart-container" style="width: 80%; margin: 0 auto;">
                                                    <canvas id="unassignedDrivingChart" class="chart-canvas"></canvas>
                                                </div>
                                                <div class="mt-4 mb-4">
                                                    <p>Unassigned: {{$unassgn_per}}% (280h 22m)</p>
                                                    <p>Managed: {{$assgn_per}}% (925h 47m)</p>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4">
                                            <h4>Unassigned Segments</h4>
                                            <div class="chart-container" style="width: 80%; margin: 0 auto;">
                                                <canvas id="unassignedSegmentsChart" class="chart-canvas"></canvas>
                                            </div>
                                            <div class="mt-4 mb-4">
                                                <p>Unassigned: 100% (280h 22m)</p>
                                                <p>Managed: 0% (-)</p>
                                            </div>
                                        </div> --}}
                                        </div>
                                    </div>

                                    <style>
                                        .chart-canvas {
                                            height: 200px;
                                            /* Adjust the height as needed */
                                        }
                                    </style>

                                    <script>
                                        // Chart configurations
                                        const hosViolationsConfig = {
                                            type: 'doughnut',
                                            data: {
                                                labels: ['In Violation', 'Compliant'],
                                                datasets: [{
                                                    data: [11.07, 89.92],
                                                    backgroundColor: ['#FF0000', '#008000'] // Darker green color
                                                }]
                                            },
                                            options: {
                                                plugins: {
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                return `${tooltipItem.label}: ${tooltipItem.raw}%`;
                                                            }
                                                        }
                                                    }
                                                },
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                cutout: '80%' // Increase cutout percentage to reduce rotation width
                                            }
                                        };

                                        const unassignedDrivingConfig = {
                                            type: 'doughnut',
                                            data: {
                                                labels: ['Unassigned', 'Managed'],
                                                datasets: [{
                                                    data: [{{$unassgn_per}}, {{$assgn_per}}],
                                                    backgroundColor: ['#FF0000', '#008000'] // Darker green color
                                                }]
                                            },
                                            options: hosViolationsConfig.options
                                        };

                                        // const unassignedSegmentsConfig = {
                                        //     type: 'doughnut',
                                        //     data: {
                                        //         labels: ['Unassigned', 'Managed'],
                                        //         datasets: [{
                                        //             data: [90, 10],
                                        //             backgroundColor: ['#FF0000', '#008000'] // Darker green color
                                        //         }]
                                        //     },
                                        //     options: hosViolationsConfig.options
                                        // };

                                        // Render charts
                                        window.onload = function() {
                                            const hosViolationsCtx = document.getElementById('hosViolationsChart').getContext('2d');
                                            new Chart(hosViolationsCtx, hosViolationsConfig);

                                            const unassignedDrivingCtx = document.getElementById('unassignedDrivingChart').getContext('2d');
                                            new Chart(unassignedDrivingCtx, unassignedDrivingConfig);

                                            // const unassignedSegmentsCtx = document.getElementById('unassignedSegmentsChart').getContext('2d');
                                            // new Chart(unassignedSegmentsCtx, unassignedSegmentsConfig);
                                        };
                                    </script>

                                    <!-- End violation card -->
                                    <!--begin::Card header-->
                                    <div class="card-header border-0 pt-6">
                                        <!--begin::Card title-->
                                        <div class="card-titl">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                                <input type="text" id="searchInput" data-kt_tr_u_table-filter="search"
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
                                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_tr_u_table">
                                                <thead>
                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-125px">Drivers</th>
                                                        <th class="min-w-125px">Hours in violation</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold" id=userTableBody>
                                                    @foreach ($data as $value)

                                                    <td>
                                                        {{$value['name']}}
                                                    <td>
                                                        {{$value['violation']}}
                                                    </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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
    <script src="{{ asset('assets/js/custom/apps/superAdmin/tr/u/list.js') }}"></script>
@endsection
