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
                                            @lang('lang.dlog')</h1>
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
                                                <a href="{{ route('driver.report.data', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.dlog')
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
                                                <input type="text" id="searchInput" data-kt_tr_u_table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--end::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body py-4">
                                        <!--begin::Table-->
                                        <div class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                <thead>
                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-125px">Id</th>
                                                        <th class="min-w-125px">IP Address</th>
                                                        <th class="min-w-125px">Interval time</th>
                                                        <th class="min-w-125px">Request json</th>
                                                        <th class="text-end min-w-100px">Service call date</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold" id="userTableBody">
                                                    @php
                                                        // Get the total number of items
                                                        $totalItems = $data->total();
                                                    @endphp

                                                    @foreach ($data as $key => $value)
                                                        @php
                                                            // Calculate the current item position
                                                            $currentItemPosition =
                                                                ($data->currentPage() - 1) * $data->perPage() +
                                                                $key +
                                                                1;

                                                            // Determine the interval time
                                                            $intervalTime = '';
                                                            if ($key < $totalItems - 1) {
                                                                $nextRow = $data->get($key + 1);
                                                                if ($nextRow && isset($nextRow->service_call_date)) {
                                                                    $nextServiceCallDate = \Carbon\Carbon::parse(
                                                                        $nextRow->service_call_date,
                                                                    );
                                                                    $currentServiceCallDate = \Carbon\Carbon::parse(
                                                                        $value->service_call_date,
                                                                    );
                                                                    $intervalTime = $currentServiceCallDate
                                                                        ->diff($nextServiceCallDate)
                                                                        ->format('%H:%I:%S');
                                                                } else {
                                                                    // If data for the next row is not found, set interval time to "Data not found"
                                                                    $intervalTime = 'Data not found';
                                                                }
                                                            } else {
                                                                // If it's the last row of the last page, set interval time to N/A
                                                                $intervalTime = 'N/A';
                                                            }

                                                        @endphp
                                                        <tr>
                                                            <td>{{ $currentItemPosition }}</td>
                                                            <td>{{ $value->ip_address }}</td>
                                                            <td>{{ $intervalTime }}</td>
                                                            <td class="json-cell" data-log="{{ $value->request_json }}">
                                                                {{ Str::limit($value->request_json, 30) }}
                                                                @if (strlen($value->request_json) > 30)
                                                                    <a href="#" class="more-link">More</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                {{ \Carbon\Carbon::parse($value->service_call_date)->format('h:i A d-m-Y') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <!-- Pagination Links -->
                                            <div class="d-flex justify-content-center">
                                                {{ $data->links() }}
                                            </div>





                                            <!-- Modal for JSON display -->
                                            <div class="modal fade" id="jsonModal" tabindex="-1" role="dialog"
                                                aria-labelledby="jsonModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="jsonModalLabel">Full Request JSON
                                                                Data</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre id="jsonContent" style="white-space: pre-wrap;"></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- JavaScript code to handle click event and display modal -->
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const jsonCells = document.querySelectorAll('.json-cell');
                                                    const jsonContent = document.getElementById('jsonContent');
                                                    const modal = new bootstrap.Modal(document.getElementById('jsonModal'));

                                                    jsonCells.forEach(function(cell) {
                                                        cell.addEventListener('click', function(e) {
                                                            const jsonData = cell.dataset.log;
                                                            jsonContent.textContent = jsonData;
                                                            modal.show();
                                                        });

                                                        const moreLinks = document.querySelectorAll('.more-link');
                                                        moreLinks.forEach(function(link) {
                                                            link.addEventListener('click', function(e) {
                                                                e.preventDefault();
                                                                const jsonData = link.parentNode.dataset.log;
                                                                jsonContent.textContent = jsonData;
                                                                modal.show();
                                                            });
                                                        });
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <!--end::Table-->
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
