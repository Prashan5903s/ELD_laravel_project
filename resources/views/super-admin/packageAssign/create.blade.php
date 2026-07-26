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
                                            Package Assign
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
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
                                                <a href="{{ route('package.assign.index') }}"
                                                    class="text-muted text-hover-primary">
                                                    Package Assign
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
                                                <a href="{{ route('package.assign.create') }}"
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
                                <form method="POST" action="{{ route('package.assign.store', [request()->lang]) }}"
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
                                                                <label class="required form-label">Company name</label>
                                                                <select name="user_id" id="driverStatus" required
                                                                    data-control="select2"
                                                                    data-placeholder="Select driver status"
                                                                    class="form-control mb-2">
                                                                    <option value="" selected disabled>Select company
                                                                    </option>
                                                                    @foreach ($user as $value)
                                                                        <option value='{{ $value->id }}'>
                                                                            {{ $value->first_name }} {{ $value->last_name }}
                                                                            ({{ $value->mobile_no }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('user_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Plane</label>
                                                                <select name="package_id" data-control="select2"
                                                                    data-placeholder="Select Driver"
                                                                    class="form-control mb-2" required>
                                                                    <option value="" selected disabled>
                                                                        Select plane
                                                                    </option>
                                                                    @foreach ($package as $value)
                                                                        <option value='{{ $value->id }}'>
                                                                            {{ $value->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('driver_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Package start Date</label>
                                                                <input type="text" placeholder="Selectt the start date" name="start_date" id="start_date"
                                                                    class="form-control" required>
                                                                @error('start_date')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            {{-- <div class="mb-10 fv-row">
                                                                <label class="required form-label">End date</label>
                                                                <input type="text" name="end_date" id="end_date"
                                                                    class="form-control" required>
                                                                @error('end_date')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div> --}}
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
            var driverStatus = document.getElementById('driverStatus');
            var messageReasonContainer = document.getElementById('messageReasonContainer');
            const form = document.getElementById('kt_ecommerce_add_product_form');
            const submitButton = document.getElementById('kt_ecommerce_add_product_submit');

            // Init form validation rules. For more info check the FormValidation plugin's official documentation: https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'user_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver Id is required'
                                }
                            }
                        },
                        'package_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Vehicle id is required'
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

            driverStatus.addEventListener('change', function() {
                if (this.value == '5') {
                    messageReasonContainer.style.display = 'block';
                    // Add validation rule for message reason
                    validator.addField('message_reason', {
                        validators: {
                            notEmpty: {
                                message: 'Message reason is required'
                            }
                        }
                    });
                } else {
                    messageReasonContainer.style.display = 'none';
                    // Remove validation rule for message reason
                    validator.removeField('message_reason');
                }
            });

            submitButton.addEventListener('click', function(e) {
                e.preventDefault();

                if (validator) {
                    validator.validate().then(function(status) {
                        console.log('validated!');

                        if (status === 'Valid') {
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;

                            form.submit(); // Submit form
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
