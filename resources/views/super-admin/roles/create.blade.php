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
                                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Add a Role</h1>
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
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('roles.index') }}" class="text-muted text-hover-primary">Roles</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">Add</li>
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
                                <div>
                                    <!--begin::Form-->
                                    <form class="form" action="{{ route('roles.store') }}" method="POST">
                                        @csrf
                                        <!--begin::Scroll-->
                                        <div class="d-flex flex-column overflow-auto me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px">
                                            <!--begin::Input group-->
                                            <div class="fv-row mb-10">
                                                <!--begin::Label-->
                                                <label class="fs-5 fw-bold form-label mb-2">
                                                    <span class="required">Role name</span>
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" value="" required/>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Permissions-->
                                            <div class="fv-row" id="permissionsTable">
                                                <!--begin::Label-->
                                                <div class="d-flex">
                                                    <label class="fs-5 fw-bold form-label mb-2 col-lg-2">Role Permissions
                                                    </label>
                                                    <label class="form-check form-check-custom form-check-solid fw-semibold fs-6 me-9 cursor-pointer">
                                                        <input class="form-check-input" type="checkbox" value="" onchange="checkAll(this)" id="kt_roles_select_all">
                                                        <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                                    </label>
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Table wrapper-->
                                                <div class="table-responsive">

                                                    <div class="text-gray-600 fw-semibold fs-6">
                                                        @if(isset($modules) && count($modules) > 0)
                                                            @foreach ($modules as $module)
                                                            @if(count($module->permissions) < 1)
                                                                @continue
                                                            @endif
                                                            <div class="permissionSection">
                                                                <div class="d-flex text-gray-800">
                                                                    <div class="col-lg-2 py-5 me-9{{ count($module->permissions) > 1 ? ' form-check form-check-solid' : '' }}">
                                                                        @if(count($module->permissions) > 1)
                                                                            <input class="form-check-input" type="checkbox" value="" onchange="sectionCheckAll(this)" id="kt_roles_select_all_{{ Str::snake($module->name) }}">
                                                                        @endif
                                                                        {{ $module->name }}
                                                                    </div>
                                                                    {{-- <div class="py-5">
                                                                        <label class="form-check form-check-custom form-check-solid me-9 cursor-pointer">
                                                                            <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all">
                                                                            <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                                                        </label>
                                                                    </div> --}}
                                                                </div>
                                                                <div class="d-flex flex-wrap border-bottom-dashed border-bottom">
                                                                    @foreach ($module->permissions as $permission)

                                                                    <div class="col-lg-3 pe-3 py-5">
                                                                        <label class="form-check form-check-custom form-check-solid me-9 cursor-pointer">
                                                                            <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" onchange="isChecked();" name="permissions[]" id="{{ Str::snake($permission->name) }}">
                                                                            <span class="form-check-label" for="{{ Str::snake($permission->name) }}">{{ $permission->name }}</span>
                                                                        </label>
                                                                    </div>

                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <!--begin::Table-->
                                                    <!--end::Table-->
                                                </div>
                                                <!--end::Table wrapper-->
                                            </div>
                                            <!--end::Permissions-->
                                        </div>
                                        <!--end::Scroll-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="reset" class="btn btn-light me-3">Cancel</button>
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

@section('footer-script')

    <script>

        function checkAll(ele) {
            const permissionsTable = document.getElementById('permissionsTable');
            const checkboxes = permissionsTable.getElementsByTagName('input');

            if (ele.checked) {
                for (let i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (let i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }

        function sectionCheckAll(ele) {
            const permissionSection = ele.closest('.permissionSection');
            const checkboxes = permissionSection.getElementsByTagName('input');

            if (ele.checked) {
                for (let i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (let i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }

        function isChecked(){
            let permissions = document.querySelectorAll('input[name="permissions[]"]');

            for (let i = 0; i < permissions.length; i++) {
                const element = permissions[i];
                if(element.checked){
                    checkboxElement = element.closest('.permissionSection').getElementsByTagName('div')[0].querySelector('input[type="checkbox"]');
                    if(checkboxElement){
                        checkboxElement.checked = true;
                    }
                }
            }
        }

        isChecked();
    </script>
@endsection
