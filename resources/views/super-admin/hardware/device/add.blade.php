@extends('super-admin.layout.index')
@section('main-section')
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
                                            Device
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('admin.dashboard') }}"
                                                    class="text-muted text-hover-primary">
                                                    Home
                                                </a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('device.admin.data.index') }}"
                                                    class="text-muted text-hover-primary">
                                                    Device
                                                </a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('device.admin.data.create') }}"
                                                    class="text-muted text-hover-primary">
                                                    Add
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
                                <!--begin::Form-->
                                <form method="POST" action="{{ route('device.admin.data.store') }}"
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
                                                                <label class="required form-label">Serial Number</label>
                                                                <input type="text" name="serial_no" id="serial_no"
                                                                    class="form-control mb-2"
                                                                    placeholder="Enter Serial Number" value=""
                                                                    required />
                                                                @error('serial_no')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('device.admin.data.index') }}"
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
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection

@section('footer-script')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd" // Adjust the format as needed
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('kt_ecommerce_add_product_form');
            const submitButton = document.getElementById('kt_ecommerce_add_product_submit');

            // Custom validation function to check serial number uniqueness
            const checkSerialNumberUniqueness = async (serialNumber) => {
                // Replace this with the actual AJAX request to check serial number uniqueness in Laravel
                const response = await fetch(`/check-serial-number?serial_no=${serialNumber}`);
                const result = await response.json();
                return result.isUnique; // Assume the API returns { isUnique: true/false }
            };

            // Init form validation rules using FormValidation.io
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'serial_no': {
                            validators: {
                                notEmpty: {
                                    message: 'Serial number is required'
                                },
                                stringLength: {
                                    min: 12,
                                    max: 40,
                                    message: 'Serial number must be between 12 and 40 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]+$/,
                                    message: 'Serial number must contain only letters and numbers'
                                },
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

            // Handle form submission
            submitButton.addEventListener('click', function(e) {
                e.preventDefault();

                if (validator) {
                    validator.validate().then(function(status) {
                        if (status === 'Valid') {
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;

                            // Submit the form
                            form.submit();
                        } else {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                        }
                    });
                }
            });
        });
    </script>
@endsection
