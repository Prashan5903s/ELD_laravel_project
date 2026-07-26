@extends('white-label.layout.index')
@section('main-section-container')
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
                                            Reseller
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
                                                <a href="{{ route('reseller.index') }}"
                                                    class="text-muted text-hover-primary">
                                                    Reseller
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
                                                <a href="{{ route('reseller.add') }}" class="text-muted text-hover-primary">
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
                                <form method="POST" action="{{ route('reseller.addForm') }}"
                                    id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                                    data-kt-redirect="{{ route('reseller.index') }}" enctype="multipart/form-data">
                                    <!-- Add your form fields here, including the input for uploading images -->
                                    <!--begin::Aside column-->
                                    @csrf
                                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                                        <!--begin::Thumbnail settings-->
                                        <div class="card card-flush py-4">
                                            <!--begin::Card header-->
                                            <div class="card-header">
                                                <!--begin::Card title-->
                                                <div class="card-title">
                                                    <h2>Thumbnail</h2>
                                                </div>
                                                <!--end::Card title-->
                                            </div>
                                            <!--end::Card header-->
                                            <!--begin::Card body-->
                                            <div class="card-body text-center pt-0">
                                                <!--begin::Image input-->
                                                <!--begin::Image input placeholder-->
                                                <style>
                                                    .image-input-placeholder {
                                                        background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}');
                                                    }

                                                    [data-bs-theme="dark"] .image-input-placeholder {
                                                        background-image: url('{{ asset('assets/media/svg/files/blank-image-dark.svg') }}');
                                                    }
                                                </style>
                                                <!--end::Image input placeholder-->
                                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                                                    data-kt-image-input="true">
                                                    <!--begin::Preview existing avatar-->
                                                    <div class="image-input-wrapper w-150px h-150px"></div>
                                                    <!--end::Preview existing avatar-->
                                                    <!--begin::Label-->
                                                    <label
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                        title="Change avatar">
                                                        <i class="ki-outline ki-pencil fs-7"></i>
                                                        <!--begin::Inputs-->
                                                        <input type="file" name="file" />
                                                        <input type="hidden" name="avatar_remove" />
                                                        <!--end::Inputs-->
                                                    </label>
                                                    @error('avatar_image')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Label-->
                                                    <!--begin::Cancel-->
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                        title="Cancel avatar">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                    <!--end::Cancel-->
                                                    <!--begin::Remove-->
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                        title="Remove avatar">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                    <!--end::Remove-->
                                                </div>
                                                <!--end::Image input-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set the reseller profile image. Only *.png,
                                                    *.jpg and *.jpeg image files are accepted
                                                </div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Thumbnail settings-->
                                        <!--end::Template settings-->
                                    </div>
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
                                                                <label class="required form-label">Company Name</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="comp_name"
                                                                    class="form-control mb-2" placeholder="Company name"
                                                                    value="" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('comp_name')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">First Name</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="first_name"
                                                                    class="form-control mb-2" placeholder="First name"
                                                                    value="" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('first_name')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Last Name</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="last_name"
                                                                    class="form-control mb-2" placeholder="Last name"
                                                                    value="" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('last_name')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Email</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="email" name="email" id="email"
                                                                    class="form-control mb-2" placeholder="Email"
                                                                    value="" required />
                                                                <div class="text-danger" id="email-error"></div>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('email')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Password</label>
                                                                <div class="input-group">
                                                                    <input id="password" type="password" name="password"
                                                                        class="form-control mb-2" placeholder="Password"
                                                                        value="" required />
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary"
                                                                            type="button" id="togglePassword">
                                                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                @error('password')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <label class="required form-label">Confirm Password</label>
                                                                <div class="input-group">
                                                                    <input id="confirm_password" type="password"
                                                                        name="confirm_password" class="form-control mb-2"
                                                                        placeholder="Confirm Password" value=""
                                                                        required />
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary"
                                                                            type="button" id="toggleConfirmPassword">
                                                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                @error('confirm_password')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <span id="password_match_message"
                                                                    class="text-danger"></span>
                                                            </div>
                                                            <script>
                                                                // JavaScript validation for password and confirm password
                                                            </script>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Mobile no</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <div></div>
                                                                <div id="mobile_no_container" class="input-group">
                                                                    <!--begin::Mobile Number Input-->
                                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                                        class="form-control mb-2" placeholder="Mobile no"
                                                                        value="" required maxlength="10">
                                                                    <!--end::Mobile Number Input-->
                                                                </div>
                                                                <!-- Begin: Country Code Input and Error -->
                                                                <div id="countryCodeContainer" class="input-group">
                                                                    <input type="text" name="country_code"
                                                                        value="" id="countryCode" hidden>
                                                                    <div id="countryCodeError" class="text-danger"
                                                                        style="display: none;">Please select the country
                                                                        code</div>
                                                                </div>
                                                                <!-- End: Country Code Input and Error -->
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('mobile_no')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <style>
                                                                .iti {
                                                                    width: 100%;
                                                                }
                                                            </style>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="form-label">Landline no</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="tel" name="landline_no"
                                                                    class="form-control mb-2" placeholder="Landline no"
                                                                    value="" maxlength="20" />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('landline_no')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Country</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="country_id" class="form-control mb-2"
                                                                    id="country_id" data-control="select2"
                                                                    data-placeholder="Select a country" required>
                                                                    <option></option>
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{ $country->country_id }}">
                                                                            {{ $country->country_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('country_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>

                                                            <div class="mb-10 fv-row" id="state_container"
                                                                style="display: none;">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">State</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="state_id" class="form-control mb-2"
                                                                    id="state_id" data-control="select2"
                                                                    data-placeholder="Select a state" required>
                                                                    <option></option>
                                                                    <option value="">Select State</option>
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('state_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row" id="city_container"
                                                                style="display: none;">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">City</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="city_id" class="form-control mb-2"
                                                                    id="city_id" data-control="select2"
                                                                    data-placeholder="Select a city" required>
                                                                    <option></option>
                                                                    <option value="">Select City</option>
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('city_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Pincode</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="number" name="pincode"
                                                                    class="form-control mb-2" placeholder="Pincode"
                                                                    value="" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('pincode')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Address</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="address"
                                                                    class="form-control mb-2" placeholder="Address"
                                                                    value="" required />
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('address')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Timezone</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="timezone" class="form-control mb-2"
                                                                    data-control="select2"
                                                                    data-placeholder="Select a timezone" required>
                                                                    <option></option>
                                                                    <option value="" selected disabled>Select
                                                                        Timezone
                                                                    </option>
                                                                    @foreach ($timezones as $value)
                                                                        <option value="{{ $value->timezone_key }}">
                                                                            {{ $value->timezone_value }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <!--end::Input-->
                                                                <!--begin::Description-->
                                                                @error('timezone')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Status</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="is_active" class="form-control mb-2"
                                                                    required>
                                                                    <option value="" selected disabled>Select Status
                                                                    </option>
                                                                    <option value=1>Active</option>
                                                                    <option value=0>Inactive</option>
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
                                            <a href="{{ route('reseller.index') }}" id="kt_ecommerce_add_product_cancel"
                                                class="btn btn-light me-5">Cancel</a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="submit" id="kt_ecommerce_add_product_submit"
                                                class="btn btn-primary" disabled>
                                                <span id="submitLabel" class="indicator-label">Save Changes</span>
                                                <span class="indicator-progress" style="display: none;">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
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
        const form = document.getElementById('kt_ecommerce_add_product_form');

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var validator = FormValidation.formValidation(
            form, {
                fields: {
                    'comp_name': {
                        validators: {
                            notEmpty: {
                                message: 'Company name is required'
                            }
                        }
                    },
                    'first_name': {
                        validators: {
                            notEmpty: {
                                message: 'First name is required'
                            }
                        }
                    },
                    'last_name': {
                        validators: {
                            notEmpty: {
                                message: 'Last name is required'
                            }
                        }
                    },
                    'email': {
                        validators: {
                            notEmpty: {
                                message: 'Email Id is required'
                            },
                            emailAddress: {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'Password is required'
                            }
                        }
                    },
                    'confirm_password': {
                        validators: {
                            notEmpty: {
                                message: 'Confirm Password is required'
                            },
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="password"]').value;
                                },
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    },
                    'mobile_no': {
                        validators: {
                            notEmpty: {
                                message: 'Mobile number is required'
                            },
                            stringLength: {
                                min: 10,
                                max: 15,
                                message: 'The mobile number must be between 10 and 15 digits long'
                            },
                            regexp: {
                                regexp: /^[0-9]+$/,
                                message: 'The mobile number can only contain digits'
                            }
                        }
                    },
                    'landline_no': {
                        validators: {
                            callback: {
                                message: 'The landline number must be between 10 and 15 digits long',
                                callback: function(input) {
                                    // If the field is empty, it is considered valid
                                    if (input.value === '') {
                                        return true;
                                    }
                                    // If the field is not empty, check the length and numeric value
                                    return input.value.length >= 10 && input.value.length <= 15 && /^[0-9]+$/.test(
                                        input.value);
                                }
                            }
                        }
                    },
                    'country_id': {
                        validators: {
                            notEmpty: {
                                message: 'Country is required'
                            }
                        }
                    },
                    'state_id': {
                        validators: {
                            notEmpty: {
                                message: 'State is required'
                            }
                        }
                    },
                    'city_id': {
                        validators: {
                            notEmpty: {
                                message: 'City is required'
                            }
                        }
                    },
                    'pincode': {
                        validators: {
                            notEmpty: {
                                message: 'Pincode is required'
                            },
                            stringLength: {
                                min: 6,
                                max: 10,
                                message: 'The pincode must be between 6 and 10 digits long'
                            },
                            regexp: {
                                regexp: /^[0-9]+$/,
                                message: 'The pincode can only contain digits'
                            }
                        }
                    },
                    'address': {
                        validators: {
                            notEmpty: {
                                message: 'Address is required'
                            }
                        }
                    },
                    'timezone': {
                        validators: {
                            notEmpty: {
                                message: 'Timezone is required'
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

        var mobileInput = document.getElementById('mobile_no');

        // Attach an event listener for input event
        mobileInput.addEventListener('input', function() {
            // Remove non-numeric characters using a regular expression
            this.value = this.value.replace(/[^0-9]/g, '');

            // Check if the length exceeds 10
            if (this.value.length > 10) {
                // Trim down to 10 characters
                this.value = this.value.slice(0, 10);
            }
        });

        $(document).ready(function() {
            $('#country_id').change(function() {
                var countryId = $(this).val();
                console.log("Selected Country ID:", countryId);
                if (countryId) {
                    var country = {!! json_encode($countries->toArray(), JSON_HEX_TAG) !!}.find(country => country.country_id == countryId);
                    console.log("Selected Country:", country);

                    $('#state_id').empty().append('<option value="">Select State</option>');
                    $.each(country.states, function(key, state) {
                        $('#state_id').append('<option value="' + state.state_id + '">' + state
                            .state_name + '</option>');
                    });

                    $('#city_container').hide();
                    $('#city_id').empty();

                    $('#state_container').show();
                } else {
                    $('#state_container').hide();
                    $('#city_container').hide();
                    $('#state_id').empty();
                    $('#city_id').empty();
                }
            });

            $('#state_id').change(function() {
                var stateId = $(this).val();
                console.log("Selected State ID:", stateId);
                if (stateId) {
                    var state = {!! json_encode($countries->pluck('states')->flatten()->toArray(), JSON_HEX_TAG) !!}.find(state => state.state_id == stateId);
                    console.log("Selected State:", state);

                    $('#city_id').empty().append('<option value="">Select City</option>');
                    $.each(state.cities, function(key, city) {
                        $('#city_id').append('<option value="' + city.city_id + '">' + city
                            .city_name + '</option>');
                    });

                    $('#city_container').show();
                } else {
                    $('#city_container').hide();
                    $('#city_id').empty();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            var password = document.getElementById("password");
            var confirm_password = document.getElementById("confirm_password");
            var password_match_message = document.getElementById("password_match_message");
            var togglePassword = document.getElementById("togglePassword");
            var toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
            var emailInput = document.getElementById("email");
            var emailError = document.getElementById("email-error");
            var nextButton = document.getElementById("kt_ecommerce_add_product_submit");
            var form = document.getElementById('kt_ecommerce_add_product_form');
            var currentTab = 0;

            // Function to check if both conditions are fulfilled
            function checkConditions() {
                var passwordsMatch = password.value === confirm_password.value;
                var emailAvailable = emailError.innerText === "";

                nextButton.disabled = !(passwordsMatch && emailAvailable);
            }

            // Password validation function
            function validatePassword() {
                if (password.value !== confirm_password.value) {
                    confirm_password.setCustomValidity("Passwords do not match");
                    password_match_message.textContent = "Passwords do not match";
                } else {
                    confirm_password.setCustomValidity("");
                    password_match_message.textContent = "";
                }
                checkConditions(); // Check both conditions after password validation
            }

            // Email input event listener
            emailInput.addEventListener("input", function() {
                var email = this.value.trim();
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/rs/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.available) {
                            emailError.innerText = '';
                        } else {
                            emailError.innerText = 'Email is already used, use some other email id.';
                        }
                        checkConditions(); // Check both conditions after email validation
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });

            // Toggle password visibility function
            function togglePasswordVisibility(inputField) {
                if (inputField.type === "password") {
                    inputField.type = "text";
                } else {
                    inputField.type = "password";
                }
            }

            // Event listeners for password fields
            password.addEventListener("input", validatePassword);
            confirm_password.addEventListener("input", validatePassword);

            // Event listeners for all input fields in each tab
            var inputs = form.querySelectorAll('.tab-pane input[required], .tab-pane select[required]');
            inputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    var errorSpan = input.parentElement.querySelector('.input-error');
                    if (errorSpan && input.value.trim()) {
                        errorSpan.textContent = ''; // Clear the error message if input is not empty
                    }
                });
            });

            // Toggle password visibility
            togglePassword.addEventListener("click", function() {
                togglePasswordVisibility(password);
            });

            toggleConfirmPassword.addEventListener("click", function() {
                togglePasswordVisibility(confirm_password);
            });
        });

        function initializeIntlTelInput() {
            var input = document.querySelector("#mobile_no");
            var flagElement = document.createElement("div");
            var submitButton = document.getElementById('kt_ecommerce_add_product_submit');
            var countryCodeError = document.getElementById('countryCodeError');
            var countryCodeInput = document.getElementById('countryCode');

            input.classList.add('w-full');

            var iti = window.intlTelInput(input, {
                separateDialCode: true,
                initialCountry: "auto", // Select default country based on IP
            });

            input.addEventListener("countrychange", function() {
                var countryData = iti.getSelectedCountryData();
                var countCode = document.querySelector('.iti__selected-dial-code').innerText.trim();
                flagElement.classList.add("iti__" + countryData.iso2);
                input.parentNode.insertBefore(flagElement, input.nextSibling);
                var countryCode = iti.getSelectedCountryData().iso2; // Get the selected country code
                var countryCodeValue = [countCode, countryCode];
                console.log(countryCodeValue); // Output the country code to console
                countryCodeInput.value = countryCodeValue;
                console.log(countryCodeInput.value)
                updateSubmitButton(); // Update the submit button state
            });

            // Function to update the submit button state
            function updateSubmitButton() {
                if (countryCodeInput.value.trim()) { // Check if country code is not empty
                    submitButton.disabled = false; // Enable the submit button
                    countryCodeError.style.display = 'none'; // Hide the error message
                } else {
                    submitButton.disabled = true; // Disable the submit button
                    countryCodeError.style.display = 'block'; // Show the error message
                }
            }
        }

        document.addEventListener("DOMContentLoaded", initializeIntlTelInput);

        document.addEventListener("DOMContentLoaded", function() {
            var countCode = document.querySelector('.iti__selected-dial-code');
            var submitButton = document.getElementById('kt_ecommerce_add_product_submit');
            var countryCodeError = document.getElementById('countryCodeError');
            var countryCodeInput = document.getElementById('countryCode');

            if (countCode) {
                var countryCode = countCode.innerText.trim();

                // Check if country code is initially empty
                if (!countryCode) {
                    submitButton.disabled = true; // Disable the submit button
                    countryCodeError.style.display = 'block'; // Show the error message
                }

                function logCountCode() {
                    updateSubmitButton(); // Update the submit button state
                }

                countCode.addEventListener('DOMSubtreeModified', logCountCode);

                // Function to update the submit button state
                function updateSubmitButton() {
                    if (countryCodeInput.value.trim()) { // Check if country code is not empty
                        submitButton.disabled = false; // Enable the submit button
                        countryCodeError.style.display = 'none'; // Hide the error message
                    } else {
                        submitButton.disabled = true; // Disable the submit button
                        countryCodeError.style.display = 'block'; // Show the error message
                    }
                }

                // Update the submit button when the country code input changes
                countryCodeInput.addEventListener('input', updateSubmitButton);

                // Call the function initially to set the initial state of the submit button
                updateSubmitButton();
            } else {
                console.error("Element with class 'iti__selected-dial-code' not found.");
            }
        });
    </script>
@endsection
