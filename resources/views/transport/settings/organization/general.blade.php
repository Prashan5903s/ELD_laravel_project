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
                            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                                <!--begin::Toolbar wrapper-->
                                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                                    <!--begin::Page title-->
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <!--begin::Title-->
                                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">General</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ isset(request()->lang) ? route('transport.dashboard', request()->lang) : route('transport.dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
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
                                            <li class="breadcrumb-item text-muted">Organization</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">General</li>
                                            <!--end::Item-->
                                        </ul>
                                        <!--end::Breadcrumb-->
                                    </div>
                                    <!--end::Page title-->
                                    <!--begin::Actions-->
                                    <!--end::Actions-->
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
                                    action="{{ isset(request()->lang) ? route('organization.general.update', [request()->lang, $organization->id]) : route('organization.general.update', ['en', $organization->id]) }}"
                                    id="kt_update_organization_general_form" class="form d-flex flex-column flex-lg-row"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <!-- Add your form fields here, including the input for uploading images -->
                                    <!--begin::Aside column-->
                                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                                        <!--begin::Thumbnail settings-->
                                        <div class="card card-flush py-4">
                                            <!--begin::Card header-->
                                            <div class="card-header">
                                                <!--begin::Card title-->
                                                <div class="card-title">
                                                    <h2>Logo</h2>
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
                                                        background-image: url('{{ !empty($organization->avatar_image) ? asset("companyss/$organization->avatar_image") : asset("assets/media/svg/files/blank-image.svg") }}');
                                                        background-position: center;
                                                    }

                                                    [data-bs-theme="dark"] .image-input-placeholder {
                                                        background-image: url('{{ !empty($organization->avatar_image) ? asset("companyss/$organization->avatar_image") : asset("assets/media/svg/files/blank-image-dark.svg") }}');
                                                        background-position: center;
                                                    }
                                                </style>
                                                <!--end::Image input placeholder-->
                                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                                                    data-kt-image-input="true">
                                                    <!--begin::Preview existing image-->
                                                    <div class="image-input-wrapper w-150px h-150px"></div>
                                                    <!--end::Preview existing image-->
                                                    <!--begin::Label-->
                                                    <label
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                        title="Change logo">
                                                        <i class="ki-outline ki-pencil fs-7"></i>
                                                        <!--begin::Inputs-->
                                                        <input type="file" name="avatar_image" value="" accept=".png,.jpg,.jpeg" />
                                                        <input type="hidden" name="image_remove" />
                                                        <!--end::Inputs-->
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Cancel-->
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                        title="Cancel logo">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                    <!--end::Cancel-->
                                                    <!--begin::Remove-->
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                        title="Remove logo">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                    <!--end::Remove-->
                                                </div>
                                                <!--end::Image input-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set the organization logo. Only *.png, *.jpg, *.jpeg image files are accepted</div>
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
                                            <div class="card card-flush py-4">
                                                <div class="card-body">
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span class="required">Oragnization</span>
                                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Organization name is required.">
                                                                <i class="ki-outline ki-information fs-7"></i>
                                                            </span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input class="form-control form-control-solid" placeholder="@lang('lang.enterName')" name="organization_name" value="{{ isset($organization) ? $organization->comp_name : '' }}" />
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span>Local</span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select class="form-select form-select-solid" name="local" data-control="select2" data-placeholder="@lang('lang.selectOption')" required>
                                                            <option></option>
                                                            @if(isset($locals))
                                                                @foreach($locals as $local)
                                                                <option value="{{ $local->country_id }}"{{ $local->country_id == $organization->country_id ? ' selected' : '' }}>{{ $local->country_name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span>Timezone</span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select class="form-select form-select-solid" name="timezone" data-control="select2" data-placeholder="@lang('lang.selectOption')" required>
                                                            <option></option>
                                                            @if(isset($timezones))
                                                                @foreach($timezones as $timezone)
                                                                <option value="{{ $timezone  }}"{{ $timezone == $organization->timezone ? ' selected' : '' }}>{{ $timezone }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="fv-row">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span>@lang('lang.lang')</span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select class="form-select form-select-solid" name="language_id" data-control="select2" data-minimum-results-for-search="Infinity" data-placeholder="@lang('lang.selectOption')" required>
                                                            <option></option>
                                                            @if(isset($languages))
                                                                @foreach($languages as $language)
                                                                <option value="{{ $language->id  }}"{{ $language->id == $organization->language_id ? ' selected' : '' }}>{{ $language->language_name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <!--begin::Button-->
                                            <a href="{{ isset(request()->lang) ? route('transport.dashboard', request()->lang) : route('transport.dashboard', 'en') }}" id="kt_organization_general_update_cancel"
                                                class="btn btn-light me-5">@lang('lang.cancel')</a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="submit" class="btn btn-primary" data-kt-addresses-modal-action="submit" id="kt_update_organization_general_form_submit">
                                                <span class="indicator-label">@lang('lang.submit')</span>
                                                <span class="indicator-progress">@lang('lang.pleaseWait')
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                            <!-- Save button -->
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
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection
@section('footer-script')
<script>
    const updateForm = document.getElementById('kt_update_organization_general_form');
    const form = $('#kt_update_organization_general_form');

    var updateValidator = FormValidation.formValidation(
        updateForm,
        {
            fields: {
                'organization_name': {
                    validators: {
                        notEmpty: {
                            message: 'Organization name is required',
                            trim: true,
                        }
                    }
                }
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

    // Submit button handler
    const submitUpdateButton = document.getElementById('kt_update_organization_general_form_submit');

    $(document).ready(function(e){
        $('#kt_update_organization_general_form').on('submit', function(e){

            // Prevent default button action
            e.preventDefault();

            // Validate form before submit
            if (updateValidator) {
                updateValidator.validate().then(function (status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show loading indication
                        submitUpdateButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        submitUpdateButton.disabled = true;

                        $.ajax({
                            url: "{{ isset(request()->lang) ? route('organization.general.update', [request()->lang, $organization->id]) : route('organization.general.update', ['en', $organization->id]) }}",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: new FormData(document.getElementById("kt_update_organization_general_form")),
                            dataType: 'JSON',
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(response){
                                if(response.error){
                                    setTimeout(function () {
                                        // Remove loading indication
                                        submitUpdateButton.removeAttribute('data-kt-indicator');

                                        // Enable button
                                        submitUpdateButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Organization not updated!",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "{{ __('lang.okGotIt') }}",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && location.reload();
                                            // $('#kt_modules_table').DataTable().draw()
                                        }
                                        ));

                                    }, 2000);
                                }else{
                                    setTimeout(function () {
                                        // Remove loading indication
                                        submitUpdateButton.removeAttribute('data-kt-indicator');

                                        // Enable button
                                        submitUpdateButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Organization updated successfully!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "{{ __('lang.okGotIt') }}",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && location.reload();

                                            // $('#kt_documents_table').DataTable().draw()
                                        }
                                        ));

                                    }, 2000);
                                }
                            },
                            error: function(){

                                // Remove loading indication
                                submitUpdateButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitUpdateButton.disabled = false;

                                // Show popup confirmation
                                Swal.fire({
                                    text: "Organization not updated!",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then((function(t) {
                                    t.isConfirmed && location.reload();
                                }
                                ));

                            }
                        });
                    }
                });
            }
        });
    });
    // Update form end
</script>
@endsection
