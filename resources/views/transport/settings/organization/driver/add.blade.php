@extends('transport.layout.index')
@section('main-transport-container')
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
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ url('/') }}" class="text-muted text-hover-primary">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('setting.driver.organisation', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.vAssign')
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('setting.driver.organisation.add', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    Add
                                                </a>
                                            </li>
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
                                <!--begin::Form-->
                                <form method="POST"
                                    action="{{ route('setting.driver.organisation.add.post', [request()->lang]) }}"
                                    id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                                    data-kt-redirect="{{ route('setting.driver.organisation.add.post') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                                role="tab-panel">
                                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                                    <div class="card card-flush py-4">
                                                        <div class="card-body pt-0">
                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Driver</label>
                                                                <select name="driver_id" data-control="select2"
                                                                    data-placeholder="Select Driver"
                                                                    class="form-control mb-2" required>
                                                                    <option value="" selected disabled>Select Driver
                                                                    </option>
                                                                    @foreach ($driver as $value)
                                                                        <option value='{{ $value->id }}'>
                                                                            {{ $value->first_name }} {{ $value->last_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('driver_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Vehicle</label>
                                                                <select name="vechile_id" data-control="select2"
                                                                    data-placeholder="Select Vehicle"
                                                                    class="form-control mb-2" required>
                                                                    <option value="" selected disabled>Select Vehicle
                                                                    </option>
                                                                    @foreach ($vechile as $value)
                                                                        <option value='{{ $value->id }}'>
                                                                            {{ $value->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('vechile_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                {{-- @error('vechile_id.message')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror --}}
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Status</label>
                                                                <select name="is_active" class="form-control mb-2" required>
                                                                    <option value="" selected disabled>Select Status
                                                                    </option>
                                                                    <option value=1>Active</option>
                                                                    <option value=0>Inactive</option>
                                                                </select>
                                                                @error('is_active')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('setting.driver.organisation', [request()->lang]) }}"
                                                id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a>
                                            <button type="submit" id="kt_ecommerce_add_product_submit"
                                                class="btn btn-primary">
                                                <span id="kt_ecommerce_add_product_submit" class="indicator-label">Save
                                                    Changes</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const form = document.getElementById('kt_ecommerce_add_product_form');

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {

                        'driver_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver Id is required'
                                }
                            }
                        },

                        'vechile_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Vechile id is required'
                                }
                            }
                        },

                        'is_active': {
                            validators: {
                                notEmpty: {
                                    message: 'Status is required'
                                }
                            }
                        },

                    },

                    plugins: {

                        trigger: new FormValidation.plugins.Trigger(),

                        bootstrap: new FormValidation.plugins.Bootstrap5({

                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''

                        })

                    }

                }

            );

            const submitButton = document.getElementById('kt_ecommerce_add_product_submit');

            submitButton.addEventListener('click', function(e) {

                e.preventDefault();

                if (validator) {

                    validator.validate().then(function(status) {

                        console.log('validated!');

                        if (status == 'Valid') {

                            submitButton.setAttribute('data-kt-indicator', 'on');

                            submitButton.disabled = true;

                            form.submit(); // Submit form
                        }
                    });
                }
            });
        });
    </script>
@endsection
