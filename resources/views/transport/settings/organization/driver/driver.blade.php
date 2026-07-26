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
                                            @lang('lang.vAssign')
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
                                                <a href="{{ route('setting.driver.organisation', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.vAssign')
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
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            @if ($permissions->contains(27))
                                                <div class="d-flex justify-content-end" data-kt_tr_u_table-toolbar="base">
                                                    <!--begin::Add user-->
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_add_user"
                                                        onclick="window.location.href='{{ route('setting.driver.organisation.add', [request()->lang]) }}'">
                                                        <i class="ki-outline ki-plus fs-2"></i>@lang('lang.adriver')
                                                    </button>
                                                    <!--end::Add user-->
                                                </div>
                                            @endif
                                            <!--end::Toolbar-->
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
                                                        <th class="min-w-125px">Driver Name</th>
                                                        <th class="min-w-125px max-w-500px">Vechile name</th>
                                                        <th class="min-w-125px">Status</th>
                                                        @if ($permissions->contains(28))
                                                            <th class="text-end min-w-100px">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold" id="userTableBody">
                                                    @if (!is_null($data) && isset($data['assignments']))
                                                        @php $count = 1; @endphp
                                                        @foreach ($data['assignments'] as $assignment)
                                                            <tr>
                                                                <td class="d-flex align-items-center">{{ $count }}
                                                                </td>
                                                                <td>
                                                                    @if ($data['drivers']->contains('id', $assignment->driver_id))
                                                                        {{ $data['drivers']->firstWhere('id', $assignment->driver_id)->first_name }}
                                                                        {{ $data['drivers']->firstWhere('id', $assignment->driver_id)->last_name }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td
                                                                    style="max-width: 200px; word-wrap: break-word; vertical-align: middle;">
                                                                    @if ($data['vehicles']->contains('id', $assignment->vechile_id))
                                                                        {{ $data['vehicles']->firstWhere('id', $assignment->vechile_id)->name }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td style="vertical-align: middle;">
                                                                    <!-- Display Ignition On or Ignition Off based on the is_active value -->
                                                                    @if ($assignment->is_active)
                                                                        <div class="badge badge-light-success">Active</div>
                                                                    @else
                                                                        <div class="badge badge-light-danger">Active</div>
                                                                    @endif
                                                                </td>
                                                                @if ($permissions->contains(28))
                                                                    <td class="text-end">
                                                                        <a href="#"
                                                                            class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                                                            data-kt-menu-trigger="click"
                                                                            data-kt-menu-placement="bottom-end">
                                                                            Actions <i
                                                                                class="ki-outline ki-down fs-5 ms-1"></i>
                                                                        </a>
                                                                        <!--begin::Menu-->
                                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                            data-kt-menu="true">
                                                                            <!--begin::Menu item-->
                                                                            <div class="menu-item px-3">
                                                                                <a href="{{ route('setting.driver.organisation.edit', [request()->lang, $assignment->id]) }}"
                                                                                    class="menu-link px-3">Edit</a>
                                                                            </div>
                                                                            <!--end::Menu item-->
                                                                        </div>
                                                                        <!--end::Menu-->
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            @php $count++; @endphp
                                                        @endforeach
                                                    @endif

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
