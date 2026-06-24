@extends('white-label.layout.index')
@section('main-section-container')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('white-label.layout.navbar')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('white-label.layout.left-slidebar')
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
                                            Reseller List
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('white-label.dashboard') }}"
                                                    class="text-muted text-hover-primary">Home</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('reseller.index') }}"
                                                    class="text-muted text-hover-primary">
                                                    Reseller
                                                </a>
                                            </li>
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
                                                <input type="text" id="searchInput" data-kt_wc_rs_table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--begin::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end" data-kt_wc_rs_table-toolbar="base">
                                                <!--begin::Filter-->
                                                <!--end::Menu 1-->
                                                <!--end::Filter-->
                                                <!--begin::Add user-->
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_add_user"
                                                    onclick="window.location.href='{{ route('reseller.add') }}'">
                                                    <i class="ki-outline ki-plus fs-2"></i>Add Reseller
                                                </button>
                                                <!--end::Add user-->
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body py-4">
                                        <!--begin::Table-->
                                        <div class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5 kt_table_reseller"
                                                id="kt_wc_rs_table">
                                                <thead>
                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-125px">User</th>
                                                        <th class="min-w-125px">Company Name</th>
                                                        <th class="min-w-125px">Mobile No</th>
                                                        <th class="min-w-125px">Status</th>
                                                        <th class="min-w-125px">Joined Date</th>
                                                        <th class="text-end min-w-100px">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold" id="userTableBody">
                                                    @foreach ($users as $value)
                                                        <tr>
                                                            <td class="d-flex align-items-center">
                                                                <div
                                                                    class="symbol symbol-circle symbol-50px overflow-hidden me-3">

                                                                        <div class="symbol-label">
                                                                            <img src="{{ !empty($value->avatar_image) ? asset('reSellerForlder/' . $value->avatar_image) : asset('assets/img/profile.jpg') }}"
                                                                                alt="{{ $value->first_name }}"
                                                                                class="w-100" />
                                                                        </div>

                                                                </div>
                                                                <div class="d-flex flex-column">
                                                                    {{ $value->first_name }} {{ $value->last_name }}
                                                                    <span>{{ $value->email }}</span>
                                                                </div>
                                                            </td>
                                                            <td>{{ $value->comp_name }}</td>
                                                            <td>{{ trim(explode(',', $value->country_code)[0]) }} {{$value->mobile_no}}</td>
                                                            <td>
                                                                @if ($value->is_active == 1)
                                                                    <div class="badge badge-light-success">Active</div>
                                                                @elseif ($value->is_active == 0)
                                                                    <div class="badge badge-light-danger">Inactive</div>
                                                                @endif
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($value->created_at)->format('h:i A d-m-Y') }}
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="#"
                                                                    class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                                                    data-kt-menu-trigger="click"
                                                                    data-kt-menu-placement="bottom-end">Actions
                                                                    <i class="ki-outline ki-down fs-5 ms-1"></i></a>
                                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                    data-kt-menu="true">
                                                                    <div class="menu-item px-3">
                                                                        <a href="{{ url('reseller/edit/' . $value->id) }}"
                                                                            class="menu-link px-3">Edit</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row"></div>
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
    <script src="{{ asset('assets/js/custom/apps/superAdmin/wc/rs/list.js') }}"></script>
@endsection
