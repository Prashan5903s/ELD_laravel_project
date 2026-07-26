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
                                <div class="app-toolbar-wrapper d-fl ex flex-stack flex-wrap gap-4 w-100">
                                    <!--begin::Page title-->
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <!--begin::Title-->
                                        <h1
                                            class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                                            @lang('lang.driver')
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.home')
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
                                                <a href="{{ route('driver.auth.index', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.driver')
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
                                                <a href="{{ route('driver.auth.add', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.add')
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
                                <form method="POST" action="{{ route('driver.auth.addForm', [request()->lang]) }}"
                                    id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                                    data-kt-redirect="{{ route('driver.auth.index') }}" enctype="multipart/form-data">
                                    @csrf
                                    <!--begin::Aside column-->
                                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                                        <!--begin::Thumbnail settings-->
                                        <div class="card card-flush py-4">
                                            <!--begin::Card header-->
                                            <div class="card-header">
                                                <!--begin::Card title-->
                                                <div class="card-title">
                                                    <h2>@lang('lang.thumb')</h2>
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
                                                <div class="text-muted fs-7">@lang('lang.picDes')</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                    <!--end::Aside column-->

                                    <!--begin::Main column-->
                                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                        <!--begin::Tab content-->
                                        <div class="tab-content">
                                            <!--begin::Tab pane - General options-->
                                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                                role="tabpanel">
                                                <div class="card card-flush py-4">
                                                    <div class="card-body pt-0">
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.fname')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="text" name="first_name" id="first_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.fname')"
                                                                value="" required />
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('first_name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.lname')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="text" name="last_name" id="last_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.lname')"
                                                                value="" required />
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('last_name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.email')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="email" id="email" name="email"
                                                                class="form-control mb-2" placeholder="@lang('lang.email')"
                                                                required />
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            <div class="text-danger" id="email-error"></div>
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.pass')</label>
                                                            <div class="input-group">
                                                                <input id="password" type="password" name="password"
                                                                    class="form-control mb-2" autocomplete="off"
                                                                    placeholder="@lang('lang.pass')" value=""
                                                                    required />
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary"
                                                                        type="button" id="togglePassword">
                                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <span class="input-error"></span>
                                                            </div>
                                                            @error('password')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.cpass')</label>
                                                            <div class="input-group">
                                                                <input id="confirm_password" type="password"
                                                                    name="confirm_password" class="form-control mb-2"
                                                                    autocomplete="off" placeholder="@lang('lang.cpass')"
                                                                    value="" required />
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary"
                                                                        type="button" id="toggleConfirmPassword">
                                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <span class="input-error"></span>
                                                            </div>
                                                            @error('confirm_password')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <span id="password_match_message" class="text-danger"></span>
                                                        </div>
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
                                                                <input type="text" name="country_code" value=""
                                                                    id="countryCode" hidden>
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
                                                            <label class="form-label">@lang('lang.lno')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="tel" name="landline_no"
                                                                class="form-control mb-2" placeholder="@lang('lang.lno')"
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
                                                            <label class="required form-label">@lang('lang.ydl')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="language_id" data-control="select2"
                                                                data-placeholder="Select @lang('lang.ydl')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($lang as $langu)
                                                                    <option value="{{ $langu->id }}">
                                                                        {{ $langu->language_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('language_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.country')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="country_id" class="form-control mb-2"
                                                                id="country_id" data-control="select2"
                                                                data-placeholder="Select @lang('lang.country')" required>
                                                                <option></option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country->country_id }}">
                                                                        {{ $country->country_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
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
                                                            <label class="required form-label">@lang('lang.state')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="state_id" data-control="select2"
                                                                data-placeholder="Select @lang('lang.state')"
                                                                class="form-control mb-2" id="state_id" required>
                                                                <option></option>
                                                            </select>
                                                            <span class="input-error"></span>
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
                                                            <label class="required form-label">@lang('lang.city')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="city_id" data-control="select2"
                                                                data-placeholder="Select @lang('lang.city')"
                                                                class="form-control mb-2" id="city_id" required>
                                                                <option></option>
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('city_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.pin')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="number" name="pincode"
                                                                class="form-control mb-2" placeholder="@lang('lang.pin')"
                                                                value="" />
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('pincode')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.address')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="text" name="address"
                                                                class="form-control mb-2" placeholder="@lang('lang.address')"
                                                                value="" required />
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('address')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.time')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="timezone" data-control="select2"
                                                                data-placeholder="Select @lang('lang.time')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($timezones as $value)
                                                                    <option value="{{ $value->timezone_key }}">
                                                                        {{ $value->timezone_value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('timezone')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.status')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="is_active" class="form-control mb-2" required>
                                                                <option value="" selected disabled>Select
                                                                    @lang('lang.status')
                                                                </option>
                                                                <option value=1>Active</option>
                                                                <option value=0>Inactive</option>
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('status')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Tab pane - General options-->

                                            <!--begin::Tab pane - Driving-related features-->
                                            <div class="tab-pane fade" id="kt_ecommerce_add_product_driving"
                                                role="tabpanel">
                                                <div class="card card-flush py-4">
                                                    <div class="card-body pt-0">
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.usern')</label>
                                                            <input type="text" name="username" id="username"
                                                                value="" class="form-control mb-2"
                                                                placeholder="@lang('lang.usern')" required />
                                                            <span class="input-error inputUsername"
                                                                style="color: red"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.dlno')</label>
                                                            <input type="number" name="driver_license_number"
                                                                class="form-control mb-2" placeholder="@lang('lang.dlno')"
                                                                required />
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.note')</label>
                                                            <input type="text" name="note"
                                                                class="form-control mb-2" placeholder="@lang('lang.note')"
                                                                required />
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.dlstate')</label>
                                                            <select name="driver_license_state" data-control="select2"
                                                                data-placeholder="Select @lang('lang.dlstate')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($statess as $st)
                                                                    <option value="{{ $st->state_id }}">
                                                                        {{ $st->state_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.hos')</label>
                                                            <select name="hour_of_service" data-control="select2"
                                                                data-placeholder="Select @lang('lang.hos')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($HOS as $key => $value)
                                                                    <option value="{{ $key }}">
                                                                        {{ $value }}</option>
                                                                @endforeach
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class=" form-label">@lang('lang.EHour')</label>
                                                            <select name="eld_day_start_hour" data-control="select2"
                                                                data-placeholder="Select @lang('lang.EHour')"
                                                                class="form-control mb-2">
                                                                <option></option>
                                                                @foreach ($EDSH as $key => $value)
                                                                    <option value="{{ $key }}">
                                                                        {{ $value }}</option>
                                                                @endforeach
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.ue')</label>
                                                            <select name="us_short_haul_exemption"
                                                                class="form-control mb-2" data-control="select2"
                                                                data-placeholder="Select @lang('lang.ue')" required>
                                                                <option></option>
                                                                @foreach ($UE as $key => $value)
                                                                    <option value="{{ $key }}">
                                                                        {{ $value }}</option>
                                                                @endforeach
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <!-- Add more input fields for driving-related features -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="kt_ecommerce_add_product_driving"
                                                role="tabpanel">
                                                <div class="card card-flush py-4">
                                                    <div class="card-body pt-0">
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.HTimezone')</label>
                                                            <select name="home_terminal_timezone"
                                                                class="form-control mb-2" data-control="select2"
                                                                data-placeholder="Select @lang('lang.HTimezone')" required>
                                                                <option></option>
                                                                @foreach ($timezones as $value)
                                                                    <option value="{{ $value->timezone_key }}">
                                                                        {{ $value->timezone_value }}
                                                                    </option>
                                                                @endforeach
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.CaName')</label>
                                                            <input type="text" name="career_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.CaName')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.MAddress')</label>
                                                            <input type="text" name="main_office_address"
                                                                class="form-control mb-2" placeholder="@lang('lang.MAddress')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.CUSNumber')</label>
                                                            <input type="text" name="carrer_us_dot_number"
                                                                class="form-control mb-2" placeholder="@lang('lang.CUSNumber')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.HName')</label>
                                                            <input type="text" name="home_terminal_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.HName')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.HAddress')</label>
                                                            <select name="home_terminal_address" data-control="select2"
                                                                data-placeholder="Select
                                                            @lang('lang.HAddress')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($location as $value)
                                                                    <option value="{{ $value->id }}">
                                                                        {{ $value->address }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.PTag')</label>
                                                            <input type="text" name="peer_group_tag"
                                                                class="form-control mb-2" placeholder="@lang('lang.PTag')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.VTag')</label>
                                                            <input type="text" name="vechile_selection_tag"
                                                                class="form-control mb-2" placeholder="@lang('lang.VTag')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.TTag')</label>
                                                            <input type="text" name="trailor_selection_tag"
                                                                class="form-control mb-2" placeholder="@lang('lang.TTag')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.IdCode')</label>
                                                            <input type="text" name="id_card_code"
                                                                class="form-control mb-2" placeholder="@lang('lang.IdCode')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.TCard')</label>
                                                            <input type="text" name="tachograph_card"
                                                                class="form-control mb-2" placeholder="@lang('lang.TCard')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.driver')
                                                                @lang('lang.status')</label>
                                                            <select name="driver_status" class="form-control mb-2"
                                                                required>
                                                                <option value="" selected disabled>Select
                                                                    @lang('lang.driver') @lang('lang.status')
                                                                </option>
                                                                <option value=1>Active</option>
                                                                <option value=0>Inactive</option>
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.DAttribute')</label>
                                                            <input type="text" name="driver_attribute"
                                                                class="form-control mb-2" placeholder="@lang('lang.DAttribute')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">Driver Id</label>
                                                            <input type="number" name="driver_id"
                                                                class="form-control mb-2" placeholder="Driver Id"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.DCycle')</label>
                                                            <select name="driver_ruleset_cycle" class="form-control mb-2"
                                                                required>
                                                                <option value="" selected disabled>Select
                                                                    @lang('lang.DCycle')
                                                                </option>
                                                                <option value=1>Yes</option>
                                                                <option value=0>No</option>
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <!-- Add more input fields for driving-related features -->
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Tab pane - Driving-related features-->
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <!--begin::Button-->
                                            <a href="{{ route('driver.auth.index', [request()->lang]) }}"
                                                id="kt_ecommerce_add_product_cancel"
                                                class="btn btn-light me-5">@lang('lang.cancel')</a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="button" id="kt_ecommerce_add_product_submit"
                                                class="btn btn-primary">
                                                <span class="indicator-label">@lang('lang.next')</span>
                                                <span class="indicator-progress d-none">
                                                    Please wait... <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                            <!-- Save button -->
                                            <button type="submit" id="kt_ecommerce_add_product_save"
                                                class="btn btn-primary d-none">
                                                <span class="indicator-label">@lang('lang.save')</span>
                                                <span class="indicator-progress d-none">
                                                    Please wait... <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
@section('foooter-all-script')
    <script>
        // On change of first_name or last_name
        $('#first_name, #last_name').on('input', function() {
            var firstName = $('#first_name').val().trim();
            var lastName = $('#last_name').val().trim();

            if (firstName !== '' && lastName !== '') {
                // Get the CSRF token value from the page's meta tags
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Make AJAX call to generate unique username
                $.ajax({
                    url: '/generate-username', // Replace with your Laravel backend route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
                    },
                    data: {
                        first_name: firstName,
                        last_name: lastName
                    },
                    success: function(response) {

                        $('#username').val(response.username);

                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        // Optionally handle errors here, e.g., display an error message
                    }
                });
            } else {
                // Handle case where first_name or last_name is empty
                console.log('First name or last name is empty. Cannot generate username.');
                // Optionally, you can clear the username field or display an error message
            }

        });

        $(document).ready(function() {
            var nextButton = $('#kt_ecommerce_add_product_submit');

            $('#username').on('input', function() {
                var username = $(this).val().trim();

                if (username !== '') {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    // Make AJAX call to check uniqueness
                    $.ajax({
                        url: '/check-username', // Replace with your Laravel route
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
                        },
                        data: {
                            username: username
                        },
                        success: function(response) {
                            if (response.unique) {
                                $('.inputUsername').text('');
                                nextButton.prop('disabled', false); // Enable next button
                            } else {
                                $('.inputUsername').text('Username should be unique');
                                nextButton.prop('disabled', true); // Disable next button
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    $('.inputUsername').text('');
                    nextButton.prop('disabled', true); // Disable next button if username is empty
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
            var errorTabIndex = -1; // Store the index of tab where error occurs

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

                fetch('/check-email', {
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

            // Function to show current tab
            function showTab(tabIndex) {
                var tabs = form.querySelectorAll('.tab-pane');
                tabs.forEach(function(tab, index) {
                    if (index === tabIndex) {
                        tab.classList.add('show', 'active');
                    } else {
                        tab.classList.remove('show', 'active');
                    }
                });
            }

            // Function to scroll to empty input
            function scrollToEmptyInput() {
                var emptyInput = form.querySelector(
                    '.tab-pane.active input[required]:not([value]), .tab-pane.active select[required]:not(:checked)'
                );
                if (emptyInput) {
                    emptyInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    emptyInput.focus();
                } else {
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }

            // Function to move to next tab
            function nextTab() {
                var inputs = form.querySelectorAll(
                    '.tab-pane.active input[required], .tab-pane.active select[required]');
                var isValid = true;
                errorTabIndex = -1; // Reset the errorTabIndex

                inputs.forEach(function(input, index) {
                    if ((!input.value.trim() && input.nodeName.toLowerCase() === 'input') || (input.nodeName
                            .toLowerCase() === 'select' && input.value === '')) {
                        isValid = false;
                        errorTabIndex = currentTab; // Set the index of tab where error occurs
                        var errorMessage = 'This field is required.';
                        var errorSpan = input.parentElement.querySelector('.input-error');
                        if (errorSpan) {
                            errorSpan.textContent = errorMessage;
                            errorSpan.style.color = 'red';
                        }
                    }
                });

                if (isValid) {
                    currentTab++;
                    showTab(currentTab);

                    if (currentTab === form.querySelectorAll('.tab-pane').length - 1) {
                        document.getElementById('kt_ecommerce_add_product_submit').classList.add('d-none');
                        document.getElementById('kt_ecommerce_add_product_save').classList.remove('d-none');
                    }
                } else {
                    showTab(errorTabIndex);
                }

                // Scroll to the first empty input field or select element
                scrollToEmptyInput();
            }

            // Event listener for next button
            nextButton.addEventListener('click', function(event) {
                event.preventDefault();

                if (currentTab === form.querySelectorAll('.tab-pane').length - 1) {
                    nextButton.querySelector('.indicator-progress').classList.remove('d-none');
                    nextButton.querySelector('.indicator-label').classList.add('d-none');

                    form.submit();
                } else {
                    nextTab();
                }
            });

            // Show initial tab
            showTab(currentTab);
        });

        $(document).ready(function() {
            var selectedCityId = null; // Store selected city ID

            $('#country_id').change(function() {
                var countryId = $(this).val();
                if (countryId) {
                    var country = {!! json_encode($countries->toArray(), JSON_HEX_TAG) !!}.find(country => country.country_id == countryId);

                    $('#state_id, #driver_license_state_id').empty().append(
                        '<option value="">Select State</option>');
                    $.each(country.states, function(key, state) {
                        $('#state_id, #driver_license_state_id').append('<option value="' + state
                            .state_id + '">' +
                            state.state_name + '</option>');
                    });

                    $('#city_container').hide();
                    $('#city_id').empty();

                    $('#city_container, #driver_license_city_container').hide();
                    $('#state_container, #driver_license_state_container').show();
                } else {
                    $('#state_container, #driver_license_state_container').hide();
                    $('#city_container, #driver_license_city_container').hide();
                    $('#state_id, #driver_license_state_id').empty();
                    $('#city_id, #driver_license_city_id').empty();
                }
            });

            $('#state_id, #driver_license_state_id').change(function() {
                var stateId = $(this).val();
                if (stateId) {
                    var state = {!! json_encode($countries->pluck('states')->flatten()->toArray(), JSON_HEX_TAG) !!}.find(state => state.state_id == stateId);

                    $('#city_id').empty().append('<option value="">Select City</option>');
                    $.each(state.cities, function(key, city) {
                        $('#city_id').append('<option value="' + city.city_id + '">' + city
                            .city_name + '</option>');
                    });

                    // If a city was previously selected, re-select it
                    if (selectedCityId) {
                        $('#city_id').val(selectedCityId);
                    }

                    $('#city_container').show();
                } else {
                    $('#city_container').hide();
                    $('#city_id').empty();
                }
            });

            // Store selected city ID when it changes
            $('#city_id').change(function() {
                selectedCityId = $(this).val();
            });

            // Additional logic for driver license city selection
            $('#driver_license_state_id').change(function() {
                var stateId = $(this).val();
                if (stateId) {
                    var state = {!! json_encode($countries->pluck('states')->flatten()->toArray(), JSON_HEX_TAG) !!}.find(state => state.state_id == stateId);

                    $('#driver_license_city_id').empty().append('<option value="">Select City</option>');
                    $.each(state.cities, function(key, city) {
                        $('#driver_license_city_id').append('<option value="' + city.city_id +
                            '">' + city
                            .city_name + '</option>');
                    });

                    $('#driver_license_city_container').show();
                } else {
                    $('#driver_license_city_container').hide();
                    $('#driver_license_city_id').empty();
                }
            });

            // Store selected driver license city ID when it changes
            $('#driver_license_city_id').change(function() {
                selectedCityId = $(this).val();
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
                countryCodeInput.value = countryCodeValue;
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
