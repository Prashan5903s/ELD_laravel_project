@extends('layouts.index')
@section('main-section')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('super-admin.layout.navbar')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('super-admin.layout.left-slidebar')
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
                                            {{ ucwords(str_replace('-', ' ', request()->ut)) }} List
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ url('/') }}" class="text-muted text-hover-primary">Home</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                {{ ucwords(str_replace('-', ' ', request()->ut)) }}
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
                                        <div class="card-title">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                                <input type="text" data-kt-wc-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search user" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--begin::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    @if (session('message'))
                                        <div class="alert alert-success">
                                            {{ session('message') }}
                                        </div>
                                    @endif
                                    <div class="card-body py-4">
                                        <div class="dataTables_wrapper dt-bootstrap4 no-footer" id="kt_wc_table_wrapper">
                                            <div class="table-responsive">
                                                <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                    id="kt_wc_table">
                                                    <thead>
                                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="min-w-125px">User</th>
                                                            @if ($no_comp == 0)
                                                                <th class="min-w-125px">Company Name</th>
                                                            @endif
                                                            <th class="min-w-125px">Mobile No</th>
                                                            <th class="min-w-125px">Status</th>
                                                            <th class="min-w-125px">Joined Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-semibold">
                                                        @foreach ($user as $value)
                                                            <tr>
                                                                <td class="d-flex align-items-center">
                                                                    <!--begin:: Avatar -->
                                                                    <div
                                                                        class="symbol symbol-circle symbol-50px overflow-hidden me-3">

                                                                    </div>
                                                                    <!--end::Avatar-->
                                                                    <!--begin::User details-->
                                                                    <div class="d-flex flex-column">
                                                                        {{ $value->first_name }} {{ $value->last_name }}
                                                                        <span>{{ $value->email }}</span>
                                                                    </div>
                                                                    <!--begin::User details-->
                                                                </td>
                                                                @if ($no_comp == 0)
                                                                    <td>{{ $value->comp_name }}</td>
                                                                @endif
                                                                <td>{{ trim(explode(',', $value->country_code)[0]) }} {{$value->mobile_no}}</td>
                                                                <td>
                                                                    @if ($value->is_active == 1)
                                                                        <div class="badge badge-light-success">Active</div>
                                                                    @elseif ($value->is_active == 0)
                                                                        <div class="badge badge-light-danger">Inactive</div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($value->created_at)->format('h:i A d-m-Y') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">

                                            </div>
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
    <script src="{{ asset('assets/js/custom/apps/superAdmin/wc/list.js') }}"></script>
@endsection
