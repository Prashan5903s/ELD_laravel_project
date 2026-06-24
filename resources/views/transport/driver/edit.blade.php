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
                                    action="{{ route('driver.auth.post.edit', [request()->lang, $user->id]) }}"
                                    id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                                    data-kt-redirect="{{ route('driver.auth.index', [request()->lang]) }}"
                                    enctype="multipart/form-data">

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
                                                        background-image: url('{{ !empty($user->avatar_image) ? asset("driverss/$user->avatar_image") : asset('assets/media/svg/files/blank-image.svg') }}');
                                                    }

                                                    [data-bs-theme="dark"] .image-input-placeholder {
                                                        background-image: url('{{ !empty($user->avatar_image) ? asset("driverss/$user->avatar_image") : asset('assets/media/svg/files/blank-image-dark.svg') }}');
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
                                                <div class="text-muted fs-7">@lang('lang.picDes')
                                                </div>
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
                                                            <input type="text" name="first_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.fname')"
                                                                value="{{ $user->first_name }}" required />
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
                                                            <input type="text" name="last_name" class="form-control mb-2"
                                                                placeholder="@lang('lang.lname')"
                                                                value="{{ $user->last_name }}" required />
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
                                                            <label class="required form-label">Email</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="email" name="email" id="email"
                                                                class="form-control mb-2" placeholder="@lang('lang.email')"
                                                                value="{{ $user->email }}" required />
                                                            <span class="input-error"></span>
                                                            <div class="text-danger" id="email-error"></div>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('email')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">Mobile no</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <div id="mobile_no_container" class="input-group">
                                                                <!--begin::Mobile Number Input-->
                                                                <input type="text" name="mobile_no" id="mobile_no"
                                                                    class="form-control mb-2" placeholder="Mobile no"
                                                                    value="{{ $user->mobile_no }}" required
                                                                    maxlength="10">
                                                                <!--end::Mobile Number Input-->
                                                            </div>
                                                            <!-- Begin: Country Code Input and Error -->
                                                            <div id="countryCodeContainer" class="input-group">
                                                                <input type="text" name="country_code"
                                                                    value="{{ $user->country_code }}" id="countryCode"
                                                                    hidden>
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
                                                                class="form-control mb-2" placeholder="Landline no"
                                                                value="{{ $user->landline_no }}" maxlength="20" />
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
                                                                    <option value="{{ $langu->id }}"
                                                                        @if (!is_null(old('language_id')) && old('language_id') == $langu->id) selected
                                                                     @elseif (!is_null($user) && $user->language_id == $langu->id) selected @endif>
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
                                                            <label class="required form-label">Country</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select name="country_id" data-control="select2"
                                                                data-placeholder="Select a Country"
                                                                class="form-control mb-2" id="country_id" required>
                                                                <option></option>
                                                                @foreach ($countries as $country)
                                                                    <option id="countrySel"
                                                                        value="{{ $country->country_id }}">
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
                                                            <select name="state_id" data-control="select2"
                                                                data-placeholder="Select a State"
                                                                class="form-control mb-2" id="state_id" required>
                                                                <option></option>
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
                                                            <select name="city_id" data-control="select2"
                                                                data-placeholder="Select a City" class="form-control mb-2"
                                                                id="city_id" required>
                                                                <option></option>
                                                            </select>
                                                            <!--end::Input-->
                                                            <!--begin::Description-->
                                                            @error('city_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <!--end::Description-->
                                                        </div>
                                                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                                                        <script></script>
                                                        <div class="mb-10 fv-row">
                                                            <!--begin::Label-->
                                                            <label class="required form-label">@lang('lang.pin')</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <input type="number" name="pincode"
                                                                class="form-control mb-2" placeholder="@lang('lang.pin')"
                                                                value="{{ $user->pin_code }}" required />
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
                                                                value="{{ $user->address }}" required />
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
                                                                data-placeholder="Select a @lang('lang.time')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($timezones as $value)
                                                                    <option value="{{ $value->timezone_key }}"
                                                                        {{ $user->timezone == $value->timezone_key ? 'selected' : '' }}>
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
                                                                <option value="" disabled>Select @lang('lang.status')
                                                                </option>
                                                                <option value="1"
                                                                    {{ $user->is_active == 1 ? 'selected' : '' }}>Active
                                                                </option>
                                                                <option value="0"
                                                                    {{ $user->is_active == 0 ? 'selected' : '' }}>Inactive
                                                                </option>
                                                                <!-- Add more statuses as needed -->
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
                                                                value="{{ $userInfo->username }}"
                                                                class="form-control mb-2" placeholder="@lang('lang.usern')"
                                                                required />
                                                            <span class="input-error inputUsername"
                                                                style="color: red"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.dlno')</label>
                                                            <input type="text" name="driver_license_number"
                                                                class="form-control mb-2"
                                                                value="{{ $userInfo->licenseNumber }}"
                                                                placeholder="@lang('lang.dlno')" required />
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.note')</label>
                                                            <input type="text" name="note"
                                                                value="{{ $userInfo->note }}" class="form-control mb-2"
                                                                placeholder="Note" required />
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.dlstate')</label>
                                                            <select name="driver_license_state" data-control="select2"
                                                                data-placeholder="Select a @lang('lang.dlstate')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($statess as $st)
                                                                    <option value="{{ $st->state_id }}"
                                                                        {{ $userInfo->driver_license_state == $st->state_id ? 'selected' : '' }}>
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
                                                                data-placeholder="Select a @lang('lang.hos')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($HOS as $key => $value)
                                                                    <option value="{{ $key }}"
                                                                        {{ $userInfo->hour_of_service == $key ? ' selected' : '' }}>
                                                                        {{ $value }}</option>
                                                                @endforeach
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="form-label">@lang('lang.EHour')</label>
                                                            <select name="eld_day_start_hour" data-control="select2"
                                                                data-placeholder="Select a @lang('lang.EHour')"
                                                                class="form-control mb-2">
                                                                <option></option>
                                                                @foreach ($EDSH as $key => $value)
                                                                    <option value="{{ $key }}"
                                                                        {{ $userInfo->eld_day_start_hour == $key ? ' selected' : '' }}>
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
                                                                data-placeholder="Select a @lang('lang.ue')" required>
                                                                <option></option>
                                                                @foreach ($UE as $key => $value)
                                                                    <option value="{{ $key }}"
                                                                        {{ $userInfo->us_short_haul_exemption == $key ? ' selected' : '' }}>
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
                                                                data-placeholder="Select a @lang('lang.HTimezone')" required>
                                                                <option></option>
                                                                @foreach ($timezones as $value)
                                                                    <option value="{{ $value->timezone_key }}"
                                                                        {{ $user->timezone == $value->timezone_key ? 'selected' : '' }}>
                                                                        {{ $value->timezone_value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-error"></span>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.CaName')</label>
                                                            <input type="text" name="career_name"
                                                                class="form-control mb-2" placeholder="@lang('lang.CaName')"
                                                                value="{{ $userInfo->career_name }}" required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.MAddress')</label>
                                                            <input type="text" name="main_office_address"
                                                                class="form-control mb-2"
                                                                value="{{ $userInfo->main_office_address }}"
                                                                placeholder="@lang('lang.MAddress')" required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.CUSNumber')</label>
                                                            <input type="text" name="carrer_us_dot_number"
                                                                class="form-control mb-2"
                                                                value="{{ $userInfo->carrer_us_dot_number }}"
                                                                placeholder="@lang('lang.CUSNumber')" required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.HName')</label>
                                                            <input type="text" name="home_terminal_name"
                                                                value="{{ $userInfo->home_terminal_name }}"
                                                                class="form-control mb-2" placeholder="@lang('lang.HName')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.HAddress')</label>
                                                            <select name="home_terminal_address" data-control="select2"
                                                                data-placeholder="Select a @lang('lang.HAddress')"
                                                                class="form-control mb-2" required>
                                                                <option></option>
                                                                @foreach ($location as $value)
                                                                    <option data-id={{ $value->id }}
                                                                        value="{{ $value->id }}"
                                                                        {{ $userInfo->home_terminal_address == $value->id ? ' selected' : '' }}>
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
                                                                value="{{ $userInfo->peer_group_tag }}"
                                                                class="form-control mb-2" placeholder="@lang('lang.PTag')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.VTag')</label>
                                                            <input type="text" name="vechile_selection_tag"
                                                                class="form-control mb-2"
                                                                value="{{ $userInfo->vechile_selection_tag }}"
                                                                placeholder="@lang('lang.VTag')" required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.TTag')</label>
                                                            <input type="text" name="trailor_selection_tag"
                                                                class="form-control mb-2"
                                                                value="{{ $userInfo->trailor_selection_tag }}"
                                                                placeholder="@lang('lang.TTag')" required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.IdCode')</label>
                                                            <input type="text" name="id_card_code"
                                                                value="{{ $userInfo->code_card }}"
                                                                class="form-control mb-2" placeholder="@lang('lang.IdCode')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.TCard')</label>
                                                            <input type="text" name="tachograph_card"
                                                                value="{{ $userInfo->tachograph_card }}"
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
                                                                <option value="1"
                                                                    {{ $userInfo->driver_status == 1 ? 'selected' : '' }}>
                                                                    Yes</option>
                                                                <option value="0"
                                                                    {{ $userInfo->driver_status == 0 ? 'selected' : '' }}>
                                                                    No</option>
                                                                <!-- Add more addresses as needed -->
                                                            </select>
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">@lang('lang.DAttribute')</label>
                                                            <input type="text" name="driver_attribute"
                                                                value="{{ $userInfo->driver_attribute }}"
                                                                class="form-control mb-2" placeholder="@lang('lang.DAttribute')"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">Driver Id</label>
                                                            <input type="text" name="driver_id"
                                                                value="{{ $userInfo->driver_id }}"
                                                                class="form-control mb-2" placeholder="Driver Id"
                                                                required />
                                                            <!-- Add more input fields as needed -->
                                                        </div>
                                                        <div class="mb-10 fv-row">
                                                            <label class="required form-label">Driver Ruleset Cycle</label>
                                                            <select name="driver_ruleset_cycle" class="form-control mb-2"
                                                                required>
                                                                <option value="" selected disabled>Select Driver
                                                                    Ruleset Cycle
                                                                </option>
                                                                <option value="1"
                                                                    {{ $userInfo->driver_ruleset_cycle == 1 ? 'selected' : '' }}>
                                                                    Yes</option>
                                                                <option value="0"
                                                                    {{ $userInfo->driver_ruleset_cycle == 0 ? 'selected' : '' }}>
                                                                    No</option>
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

    <!--begin::Javascript-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script></script>
@endsection
@section('foooter-all-script')
    <script>
        $(document).ready(function() {
            var nextButton = $('#kt_ecommerce_add_product_submit');

            var currentUsername = '{{ $userInfo->username }}'; // Store the current username

            $('#username').on('input', function() {

                var username = $(this).val().trim();

                if (username !== '') {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    // Make AJAX call to check uniqueness
                    $.ajax({
                        url: '/editUsername', // Replace with your Laravel route
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
                        },
                        data: {
                            username: username,
                            current_username: currentUsername
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

        $(document).ready(function() {
            // Set default selected country
            var defaultCountryId = {{ $user->country_id ?? 0 }};
            $('#country_id').val(defaultCountryId).trigger('change');

            $('#country_id').change(function() {
                var countryId = $(this).val();
                if (countryId) {
                    var country = {!! json_encode($countries->toArray(), JSON_HEX_TAG) !!}.find(country => country.country_id == countryId);

                    $('#state_id').empty().append('<option value="">Select State</option>');
                    $.each(country.states, function(key, state) {
                        $('#state_id').append('<option value="' + state.state_id + '">' + state
                            .state_name + '</option>');
                    });

                    $('#state_id').val({{ $user->state_id ?? 0 }}).trigger('change');

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
                if (stateId) {
                    var state = {!! json_encode($countries->pluck('states')->flatten()->toArray(), JSON_HEX_TAG) !!}.find(state => state.state_id == stateId);

                    $('#city_id').empty().append('<option value="">Select City</option>');
                    $.each(state.cities, function(key, city) {
                        $('#city_id').append('<option value="' + city.city_id + '">' + city
                            .city_name + '</option>');
                    });

                    $('#city_id').val({{ $user->city_id ?? 0 }});

                    $('#city_container').show();
                } else {
                    $('#city_container').hide();
                    $('#city_id').empty();
                }
            });

            // Trigger change event initially to populate state and city dropdowns if a country is already selected
            $('#country_id').trigger('change');
        });

        document.addEventListener("DOMContentLoaded", function() {
            var emailInput = document.getElementById("email");
            var emailError = document.getElementById("email-error");
            var nextButton = document.getElementById("kt_ecommerce_add_product_submit");
            var form = document.getElementById('kt_ecommerce_add_product_form');
            var currentTab = 0;
            var currentUserEmail =
                "{{ $user->email }}"; // Assuming $user->email is PHP, you may need to adjust this

            // Function to check if both conditions are fulfilled
            function checkConditions() {
                var emailAvailable = emailError.innerText === "";
                nextButton.disabled = !(emailAvailable);
            }

            // Function to validate email and show error message if needed
            function validateEmail(email) {
                if (email !== currentUserEmail) {
                    // Perform validation only if the email is different
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
                } else {
                    // If the email is the same as the user's email, clear any previous error message
                    emailError.innerText = '';
                    checkConditions(); // Check both conditions after email validation
                }
            }

            // Email input event listener
            emailInput.addEventListener("input", function() {
                var email = this.value.trim();
                validateEmail(email);
            });

            // Rest of your code...
            var form = document.getElementById('kt_ecommerce_add_product_form');
            var currentTab = 0;

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

            function nextTab() {
                var inputs = form.querySelectorAll(
                    '.tab-pane.active input[required], .tab-pane.active select[required]');
                var isValid = true;

                inputs.forEach(function(input) {
                    if ((!input.value.trim() && input.nodeName.toLowerCase() === 'input') || (input.nodeName
                            .toLowerCase() === 'select' && input.value === '')) {
                        isValid = false;
                        var errorMessage = '@lang('lang.empty')';
                        var errorSpan = input.parentElement.querySelector('.input-error');
                        if (errorSpan) {
                            errorSpan.textContent = errorMessage;
                            errorSpan.style.color = 'red';
                        }
                    } else {
                        // If input is filled, remove the error message
                        var errorSpan = input.parentElement.querySelector('.input-error');
                        if (errorSpan) {
                            errorSpan.textContent = "";
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
                }

                // Scroll to the first empty input field or select element
                scrollToEmptyInput();
            }



            var nextButton = document.getElementById('kt_ecommerce_add_product_submit');
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

            showTab(currentTab);
            // Additional functions and event listeners should be placed here
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
                initialCountry: "{{ trim(explode(',', $user->country_code)[1]) }}", // Select default country based on IP
            });

            // Set the initial country code
            var defaultCountryCode = "{{ trim(explode(',', $user->country_code)[0]) }}";
            countryCodeInput.value = [defaultCountryCode, "{{ trim(explode(',', $user->country_code)[1]) }}"];

            input.addEventListener("countrychange", function() {
                var countryData = iti.getSelectedCountryData();
                var countCode = document.querySelector('.iti__selected-dial-code').innerText.trim();
                flagElement.classList.add("iti__" + countryData.iso2);
                input.parentNode.insertBefore(flagElement, input.nextSibling);

                var countryCode = iti.getSelectedCountryData().iso2; // Get the selected country code

                // If the selected country is the default one, display the default country code
                if (countCode == defaultCountryCode) {
                    countryCodeInput.value = ["{{ trim(explode(',', $user->country_code)[0]) }}",
                        "{{ trim(explode(',', $user->country_code)[1]) }}"
                    ];
                    console.log('Value 1', countryCodeInput.value)
                } else {
                    // Otherwise, display the newly selected country code
                    var countryCodeValue = [countCode, countryCode];
                    console.log('Value 2', countryCodeValue)
                    countryCodeInput.value = countryCodeValue;
                }
            });
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
