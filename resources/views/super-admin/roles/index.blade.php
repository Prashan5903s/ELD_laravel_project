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
                            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                                <!--begin::Toolbar wrapper-->
                                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                                    <!--begin::Page title-->
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <!--begin::Title-->
                                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Roles List</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">Settings</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">User Management</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">Roles</li>
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
                                <!--begin::Row-->
                                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
                                    @if(isset($roles) && count($roles) > 0)

                                        @foreach ($roles as $role)
                                            <!--begin::Col-->
                                            <div class="col-md-4">
                                                <!--begin::Card-->
                                                <div class="card card-flush h-md-100">
                                                    <!--begin::Card header-->
                                                    <div class="card-header">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>{{ $role->name }}</h2>
                                                        </div>
                                                        <!--end::Card title-->
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-1">
                                                        <!--begin::Users-->
                                                        <div class="fw-bold text-gray-600 mb-5">Total users with this role: 0</div>
                                                        <!--end::Users-->
                                                        <!--begin::Permissions-->
                                                        @if(isset($role->permissions) && count($role->permissions) > 0)
                                                            <div class="d-flex flex-column text-gray-600">
                                                                @foreach ($role->permissions as $key => $permission)

                                                                @if($key == 5)
                                                                    @break
                                                                @endif

                                                                <div class="d-flex align-items-center py-2">
                                                                    <span class="bullet bg-primary me-3"></span>
                                                                    {{ $permission->name }}
                                                                </div>

                                                                @endforeach
                                                                @if(count($role->permissions) > 5)
                                                                    <div class='d-flex align-items-center py-2'>
                                                                        <span class='bullet bg-primary me-3'></span>
                                                                        <em>and {{ count($role->permissions) - 5 }} more...</em>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <!--end::Permissions-->
                                                    </div>
                                                    <!--end::Card body-->
                                                    <!--begin::Card footer-->
                                                    <div class="card-footer flex-wrap pt-0">
                                                        {{-- <a href="apps/user-management/roles/view.html" class="btn btn-light btn-active-primary my-1 me-2">View Role</a> --}}
                                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-light btn-active-light-primary my-1">Edit Role</a>
                                                        {{-- <button type="button" class="btn btn-light btn-active-light-primary my-1" data-bs-toggle="modal" data-bs-target="#kt_modal_update_role">Edit Role</button> --}}
                                                    </div>
                                                    <!--end::Card footer-->
                                                </div>
                                                <!--end::Card-->
                                            </div>
                                            <!--end::Col-->
                                        @endforeach

                                    @endif
                                    <!--begin::Add new card-->
                                    <div class="ol-md-4">
                                        <!--begin::Card-->
                                        <div class="card h-md-100">
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-center">
                                                <!--begin::Button-->
                                                <a href="{{ route('roles.create') }}" class="btn btn-clear d-flex flex-column flex-center">
                                                    <!--begin::Illustration-->
                                                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7" />
                                                    <!--end::Illustration-->
                                                    <!--begin::Label-->
                                                    <div class="fw-bold fs-3 text-gray-600 text-hover-primary">Add New Role</div>
                                                    <!--end::Label-->
                                                </a>
                                                <!--begin::Button-->
                                            </div>
                                            <!--begin::Card body-->
                                        </div>
                                        <!--begin::Card-->
                                    </div>
                                    <!--begin::Add new card-->
                                </div>
                                <!--end::Row-->
                                <!--begin::Modals-->
                                <!--begin::Modal - Add role-->
                                <div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-750px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Add a Role</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body scroll-y mx-lg-5 my-7">
                                                <!--begin::Form-->
                                                <form id="kt_modal_add_role_form" class="form" action="{{ route('roles.store') }}" method="POST">
                                                    @csrf
                                                    <!--begin::Scroll-->
                                                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="fs-5 fw-bold form-label mb-2">
                                                                <span class="required">Role name</span>
                                                            </label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" required/>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Permissions-->
                                                        <div class="fv-row">
                                                            <!--begin::Label-->
                                                            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
                                                            <!--end::Label-->
                                                            <!--begin::Table wrapper-->
                                                            <div class="table-responsive">
                                                                <!--begin::Table-->
                                                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                                    <!--begin::Table body-->
                                                                    <tbody class="text-gray-600 fw-semibold">
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <td class="text-gray-800">Administrator Access
                                                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Allows a full access to the system">
                                                                                <i class="ki-outline ki-information fs-7"></i>
                                                                            </span></td>
                                                                            <td>
                                                                                <!--begin::Checkbox-->
                                                                                <label class="form-check form-check-custom form-check-solid me-9">
                                                                                    <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" />
                                                                                    <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                                                                </label>
                                                                                <!--end::Checkbox-->
                                                                            </td>
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">User Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Content Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Financial Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Reporting</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Payroll</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Disputes Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">API Controls</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Database Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Repository Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                    </tbody>
                                                                    <!--end::Table body-->
                                                                </table>
                                                                <!--end::Table-->
                                                            </div>
                                                            <!--end::Table wrapper-->
                                                        </div>
                                                        <!--end::Permissions-->
                                                    </div>
                                                    <!--end::Scroll-->
                                                    <!--begin::Actions-->
                                                    <div class="text-center pt-15">
                                                        <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Discard</button>
                                                        <button type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Add role-->
                                <!--begin::Modal - Update role-->
                                <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-750px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Update Role</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body scroll-y mx-5 my-7">
                                                <!--begin::Form-->
                                                <form id="kt_modal_update_role_form" class="form" action="#">
                                                    <!--begin::Scroll-->
                                                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="fs-5 fw-bold form-label mb-2">
                                                                <span class="required">Role name</span>
                                                            </label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" value="Developer" />
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Permissions-->
                                                        <div class="fv-row">
                                                            <!--begin::Label-->
                                                            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
                                                            <!--end::Label-->
                                                            <!--begin::Table wrapper-->
                                                            <div class="table-responsive">
                                                                <!--begin::Table-->
                                                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                                    <!--begin::Table body-->
                                                                    <tbody class="text-gray-600 fw-semibold">
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <td class="text-gray-800">Administrator Access
                                                                            <span class="ms-1" data-bs-toggle="tooltip" title="Allows a full access to the system">
                                                                                <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                                                            </span></td>
                                                                            <td>
                                                                                <!--begin::Checkbox-->
                                                                                <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                                                                    <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" />
                                                                                    <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                                                                </label>
                                                                                <!--end::Checkbox-->
                                                                            </td>
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">User Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Content Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="content_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Financial Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="financial_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Reporting</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="reporting_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Payroll</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="payroll_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Disputes Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="disputes_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">API Controls</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="api_controls_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Database Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="database_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                        <!--begin::Table row-->
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800">Repository Management</td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Input group-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_read" />
                                                                                        <span class="form-check-label">Read</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_write" />
                                                                                        <span class="form-check-label">Write</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                                        <input class="form-check-input" type="checkbox" value="" name="repository_management_create" />
                                                                                        <span class="form-check-label">Create</span>
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Input group-->
                                                                        </tr>
                                                                        <!--end::Table row-->
                                                                    </tbody>
                                                                    <!--end::Table body-->
                                                                </table>
                                                                <!--end::Table-->
                                                            </div>
                                                            <!--end::Table wrapper-->
                                                        </div>
                                                        <!--end::Permissions-->
                                                    </div>
                                                    <!--end::Scroll-->
                                                    <!--begin::Actions-->
                                                    <div class="text-center pt-15">
                                                        <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Discard</button>
                                                        <button type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Update role-->
                                <!--end::Modals-->
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                    <!--begin::Footer-->
                    <div id="kt_app_footer" class="app-footer">
                        <!--begin::Footer container-->
                        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <!--begin::Copyright-->
                            <div class="text-gray-900 order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">2024&copy;</span>
                                <a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">Keenthemes</a>
                            </div>
                            <!--end::Copyright-->
                            <!--begin::Menu-->
                            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                                <li class="menu-item">
                                    <a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
                                </li>
                                <li class="menu-item">
                                    <a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>
                                </li>
                                <li class="menu-item">
                                    <a href="https://1.envato.market/EA4JP" target="_blank" class="menu-link px-2">Purchase</a>
                                </li>
                            </ul>
                            <!--end::Menu-->
                        </div>
                        <!--end::Footer container-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection
