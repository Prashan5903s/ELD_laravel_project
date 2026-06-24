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
                                            Device
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ url('/') }}" class="text-muted text-hover-primary">
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
                                                <a href="{{ route('setting.device.index', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    Device
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
                                <form method="POST" action="{{ route('setting.device.update', [request()->lang, $device->id]) }}"
                                    id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                                    data-kt-redirect="{{ route('setting.device.store', [request()->lang]) }}" enctype="multipart/form-data">
                                    <!-- Add your form fields here, including the input for uploading images -->
                                    <!--begin::Aside column-->
                                    @csrf
                                    @method('put')
                                    <!--end::Aside column-->
                                    <!--begin::Main column-->
                                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                        <!--begin::Tab content-->
                                        <div class="tab-content">
                                            <!--begin::Tab pane-->
                                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                                role="tab-panel">
                                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                                    <!--begin::General options-->
                                                    <div class="card card-flush py-4">
                                                        <!--begin::Card body-->
                                                        <div class="card-body pt-0">
                                                            <!--begin::Input group-->
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Hardware</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="hardware_id" class="form-control mb-2"
                                                                    data-control="select2"
                                                                    data-placeholder="Select a Hardware" required>
                                                                    <option></option>
                                                                    @foreach ($hardware as $value)
                                                                        <option {{$device->hardware_id == $value->id ? 'selected' : ''}} value="{{ $value->id }}">
                                                                            {{ $value->hardware_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('hardware_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Device Type</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="device_type" class="form-control mb-2"
                                                                    data-control="select2"
                                                                    data-placeholder="Select a Device Type" required>
                                                                    <option></option>
                                                                    <option value="" selected disabled>Select Device
                                                                        Type</option>
                                                                    @foreach ($device_type as $value)
                                                                        <option {{$device->device_type_id == $value->id ? 'selected' : ''}} value="{{ $value->id }}">
                                                                            {{ $value->device_type_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('device_type')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Serial Number</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="serial_number"
                                                                    class="form-control mb-2" placeholder="Serial Number"
                                                                    value="{{$device->serial_number}}" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('serial_number')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Gateway Serial</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="gateway_serial"
                                                                    class="form-control mb-2" placeholder="Gateway Serial"
                                                                    value="{{$device->gateway_serial}}" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('gateway_serial')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Gateway</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="gateway"
                                                                    class="form-control mb-2" placeholder="Gateway"
                                                                    value="{{$device->gateway}}" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('gateway')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="form-label">Vehicle</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="vehicle_id" class="form-control mb-2"
                                                                    data-control="select2"
                                                                    data-placeholder="Select a Vehicle" required>
                                                                    <option></option>
                                                                    <option value="" selected disabled>Select Vehicle</option>
                                                                    @foreach ($vehicle as $value)
                                                                    {{$value->id    }}
                                                                        <option {{$value->id == $device->vehicle_id ? 'selected' : ''}} value="{{ $value->id }}">
                                                                            {{ $value->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('vehicle_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Status</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="is_active" class="form-control mb-2" required>
                                                                    <option value="" selected disabled>Select Status
                                                                    </option>
                                                                    <option {{$device->is_active == '1' ? 'selected' : ''}} value=1>Active</option>
                                                                    <option {{$device->is_active == '0' ? 'selected' : ''}} value=0>Inactive</option>
                                                                    <!-- Add more addresses as needed -->
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('status')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <!--end::Input group-->
                                                        </div>
                                                        <!--end::Card header-->
                                                    </div>
                                                    <!--end::General options-->
                                                </div>
                                            </div>
                                            <!--end::Tab pane-->
                                        </div>
                                        <!--end::Tab content-->
                                        <div class="d-flex justify-content-end">
                                            <!--begin::Button-->
                                            <a href="{{ url('white-label') }}" id="kt_ecommerce_add_product_cancel"
                                                class="btn btn-light me-5">Cancel</a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="submit" id="kt_ecommerce_add_product_submit"
                                                class="btn btn-primary">
                                                <span id="submitLabel" class="indicator-label">Save Changes</span>
                                                <span class="indicator-progress" style="display: none;">Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                            <!--end::Button-->
                                        </div>
                                    </div>
                                    <!--end::Main column-->
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
@section('foooter-all-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const form = document.getElementById('kt_ecommerce_add_product_form');

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'device_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Device Type is required'
                                }
                            }
                        },
                        'gateway_serial': {
                            validators: {
                                notEmpty: {
                                    message: 'Gateway Serial is required'
                                }
                            }
                        },
                        'gateway': {
                            validators: {
                                notEmpty: {
                                    message: 'Gateway is required'
                                }
                            }
                        },

                        'name': {
                            validators: {
                                notEmpty: {
                                    message: 'Name is required'
                                },
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
