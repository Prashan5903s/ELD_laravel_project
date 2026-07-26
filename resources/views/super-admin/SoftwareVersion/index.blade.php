@extends('layouts.index')
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

                                        Software version
                                    </h1>

                                    <!--end::Title-->

                                    <!--begin::Breadcrumb-->

                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item text-muted">

                                            <a href="{{ route('admin.dashboard') }}"

                                                class="text-muted text-hover-primary">@lang('lang.dashboard')</a>

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

                                        <li class="breadcrumb-item text-muted">Software version</li>

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

                            <!--begin::Card-->

                            <div class="card card-flush">

                                <!--begin::Card header-->

                                <div class="card-header mt-6">

                                    <!--begin::Card title-->

                                    <div class="card-title">

                                        <!--begin::Search-->

                                        <div class="d-flex align-items-center position-relative my-1 me-5">

                                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>

                                            <input type="text" data-kt-packages-table-filter="search"

                                                class="form-control form-control-solid w-250px ps-13"

                                                placeholder="Search version" />

                                        </div>

                                        <!--end::Search-->

                                    </div>

                                    <!--end::Card title-->

                                </div>

                                <!--end::Card header-->

                                <!--begin::Card body-->

                                <div class="card-body pt-0">

                                    <!--begin::Table-->

                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0"

                                        id="kt_vehicles_table">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">

                                                <th class="min-w-125px">Software version</th>

                                                <th class="min-w-125px">Type</th>

                                                <th class="text-end min-w-100px">@lang('lang.actions')</th>

                                            </tr>

                                        </thead>

                                        <tbody class="fw-semibold text-gray-600">

                                            @if (isset($data) && count($data) > 0)

                                            @foreach ($data as $value)

                                            <tr>

                                                <td>{{ $value->version_id }}</td>

                                                <td>{{ $value->type == 1 ? "Web" : "Mobile App" }}</td>

                                                </td>

                                                <td class="text-end">

                                                    {{-- <button class="btn btn-icon btn-active-light-primary w-30px h-30px" data-url="{{ route('packages.destroy', $package->id) }}" data-packages-table-filter="delete_row">

                                                    <i class="ki-outline ki-trash fs-3"></i>

                                                    </button> --}}

                                                    <button

                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"

                                                        data-version-table-filter="update_row"

                                                        data-url="{{ route('admin.software.version.update', [$value->app_version_id]) }}"

                                                        data-bs-toggle="modal"

                                                        data-bs-target="#kt_modal_update_version">

                                                        <i class="ki-outline ki-pencil fs-3"></i>

                                                    </button>

                                                </td>

                                            </tr>

                                            @endforeach

                                            @endif

                                        </tbody>

                                    </table>

                                    <!--end::Table-->

                                </div>

                                <!--end::Card body-->

                            </div>

                            <!--end::Card-->

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

    <div class="modal fade" id="kt_modal_update_version" tabindex="-1" aria-hidden="true">

        <!--begin::Modal dialog-->

        <div class="modal-dialog modal-dialog-centered mw-650px">

            <!--begin::Modal content-->

            <div class="modal-content">

                <!--begin::Modal header-->

                <div class="modal-header">

                    <!--begin::Modal title-->

                    <h2 class="fw-bold">Update software version</h2>

                    <!--end::Modal title-->

                    <!--begin::Close-->

                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"

                        data-kt-packages-modal-action="close">

                        <i class="ki-outline ki-cross fs-1"></i>

                    </div>

                    <!--end::Close-->

                </div>

                <!--end::Modal header-->

                <!--begin::Modal body-->

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <!--begin::Form-->

                    <form id="kt_modal_update_version_form" class="form" action="#">
                        @csrf
                        <input type="hidden" name="version_type" id="version_type">

                        <div class="fv-row mb-7 d-flex gap-4">
                            @foreach(['major', 'minor', 'patches'] as $value)
                            <button
                                type="button"
                                class="btn btn-secondary p-4 flex-fill version-button"
                                data-value="{{ $value }}">
                                {{ ucfirst($value) }}
                            </button>
                            @endforeach
                        </div>

                        <div class="text-center pt-15">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
                                @lang('lang.discard')
                            </button>

                            <button type="button" class="btn btn-primary" id="kt_modal_update_version_form_submit">
                                <span class="indicator-label">@lang('lang.submit')</span>
                                <span class="indicator-progress">
                                    @lang('lang.pleaseWait')
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>


                    <!--end::Form-->

                </div>

                <!--end::Modal body-->

            </div>

            <!--end::Modal content-->

        </div>

        <!--end::Modal dialog-->

    </div>

    <!--end::Modal - Update packages-->

    <!--end::Modals-->

    @endsection



    @section('footer-script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            const t = $("#kt_vehicles_table").DataTable();

            const updateForm = document.getElementById('kt_modal_update_version_form');
            const submitUpdateButton = document.getElementById('kt_modal_update_version_form_submit');
            const versionInput = document.getElementById('version_type');
            const updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_version'));

            // Replace with actual ID dynamically or use data-attributes
            let updateUrl = null;

            const updateButtons = document.querySelectorAll('[data-version-table-filter="update_row"]');
            updateButtons.forEach(button => {
                button.addEventListener('click', () => {
                    updateUrl = button.getAttribute('data-url'); // Set update URL dynamically
                    updateModal.show(); // Optional: if not already triggered by data-bs-toggle
                });
            });

            // Handle version button selection
            const versionButtons = document.querySelectorAll('.version-button');
            versionButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    versionInput.value = button.getAttribute('data-value');

                    versionButtons.forEach(btn => {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-secondary');
                    });
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-primary');
                });
            });

            // // Form validation using FormValidation plugin
            const updateValidator = FormValidation.formValidation(updateForm, {
                fields: {
                    version_type: {
                        validators: {
                            notEmpty: {
                                message: 'Version type is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    // Omit Bootstrap5 plugin to avoid classList errors
                }
            });


            // Handle form submit
            submitUpdateButton.addEventListener('click', function(e) {
                e.preventDefault();

                updateValidator.validate().then(function(status) {
                    console.log("Status", status);

                    if (status === 'Valid') {
                        submitUpdateButton.setAttribute('data-kt-indicator', 'on');
                        submitUpdateButton.disabled = true;

                        $.ajax({
                            url: updateUrl,
                            method: 'PUT',
                            data: $(updateForm).serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                submitUpdateButton.removeAttribute('data-kt-indicator');
                                submitUpdateButton.disabled = false;

                                Swal.fire({
                                    text: response.error ? "{{ __('lang.vNotUpdatedSuccess') }}" : "Software version updated successfully!",
                                    icon: response.error ? "error" : "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(() => {
                                    updateModal.hide();
                                    updateForm.reset();
                                    versionButtons.forEach(btn => {
                                        btn.classList.remove('btn-primary');
                                        btn.classList.add('btn-secondary');
                                    });
                                    location.reload();
                                });
                            },
                            error: function() {
                                submitUpdateButton.removeAttribute('data-kt-indicator');
                                submitUpdateButton.disabled = false;

                                Swal.fire({
                                    text: "{{ __('lang.vNotUpdatedSuccess') }}",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(() => {
                                    updateModal.hide();
                                    updateForm.reset();
                                    versionButtons.forEach(btn => {
                                        btn.classList.remove('btn-primary');
                                        btn.classList.add('btn-secondary');
                                    });
                                    location.reload();
                                });
                            }
                        });

                    } else {
                        // ❌ Show validation error (in a sweetalert popup)
                        Swal.fire({
                            text: "Please select a version type before submitting.",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "{{ __('lang.okGotIt') }}",
                            customClass: {
                                confirmButton: "btn btn-warning"
                            }
                        });
                    }
                });
            });

        });
    </script>



    @endsection