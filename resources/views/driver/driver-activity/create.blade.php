@extends('driver.layout.index')
@section('main-driver-dashboard')
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('driver.layout.navbar')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('driver.layout.left-slidebar')
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
                                            @lang('lang.dactivity')
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
                                                <a href="{{ route('driver.activity.index', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.dactivity')
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
                                                <a href="{{ route('driver.activity.create', [request()->lang]) }}"
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
                                <form method="POST" action="{{ route('driver.activity.dashboard.store') }}"
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
                                                                <label class="required form-label">Driver status</label>
                                                                <select name="driver_status" id="driverStatus" required
                                                                    data-control="select2"
                                                                    data-placeholder="Select driver status"
                                                                    class="form-control mb-2">
                                                                    <option value="" selected disabled>Select Driver
                                                                        Status</option>
                                                                    @foreach ($listOption as $value)
                                                                        <option value='{{ $value->option_id }}'>
                                                                            {{ $value->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('driver_status')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Vehicle</label>
                                                                <select name="vehicle_id" required data-control="select2"
                                                                    data-placeholder="Select Vehicle"
                                                                    class="form-control mb-2">
                                                                    <option value="" selected disabled>Select Vehicle
                                                                    </option>
                                                                    @foreach ($vehicleAssigns as $value)
                                                                        <option value='{{ $value->vehicle->id }}'>
                                                                            {{ $value->vehicle  ->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('vehicle_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-3 fv-row" id="messageReasonContainer"
                                                                style="display:none;">
                                                                <label for="messageReason" class="required form-label">Message
                                                                    reason</label>
                                                                <textarea name="message_reason" id="messageReason" class="form-control" cols="10" rows="5"></textarea>
                                                                @error('message_reason')
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
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection
@section('footer-script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                        'driver_status': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver status is required'
                                }
                            }
                        },
                        'driver_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver Id is required'
                                }
                            }
                        },
                        'vehicle_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Vehicle id is required'
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
