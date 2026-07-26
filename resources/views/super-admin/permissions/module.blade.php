@extends('layouts.index')
@section('main-section')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
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
                                            Modules</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('admin.dashboard') }}"
                                                    class="text-muted text-hover-primary">Dashboard</a>
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
                                            <li class="breadcrumb-item text-muted">User Management</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">Permissions</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">Modules</li>
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
                                                <input type="text" data-kt-modules-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search Modules" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Button-->
                                            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_add_module">
                                                <i class="ki-outline ki-plus-square fs-3"></i>Add module</button>
                                            <!--end::Button-->
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Table-->
                                        <div id="kt_modules_table_wrapper"
                                            class="dataTables_wrapper dt-bootstrap4 no-footer">
                                            <div class="table-responsive">
                                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0"
                                                    id="kt_modules_table">
                                                    <thead>
                                                        <tr
                                                            class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="min-w-125px">Name</th>
                                                            <th class="min-w-125px">Created Date</th>
                                                            <th class="text-end min-w-100px">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="fw-semibold text-gray-600">
                                                        @if (isset($modules) && count($modules) > 0)
                                                            @foreach ($modules as $module)
                                                                <tr>
                                                                    <td>{{ $module->name }}</td>
                                                                    <td>{{ date('d M Y, h:i a', strtotime($module->created_at)) }}
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <button
                                                                            class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"
                                                                            data-modules-table-filter="update_row"
                                                                            data-url="{{ route('modules.update', $module->id) }}"
                                                                            data-bs-toggle="modal"
                                                                            data-module-name="{{ $module->name }}"
                                                                            onclick="editModule({{ $module->id }})"
                                                                            data-bs-target="#kt_modal_update_module">
                                                                            <i class="ki-outline ki-pencil fs-3"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            {{-- @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">No records found!</td>
                                                            </tr> --}}
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                {{-- {{ $modules->links() }} --}}
                                            </div>
                                        </div>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                                <!--begin::Modals-->
                                <!--begin::Modal - Add modules-->
                                <div class="modal fade" id="kt_modal_add_module" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-650px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Add a Module</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-kt-modules-modal-action="close" data-bs-dismiss="modal">
                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                <!--begin::Form-->
                                                <form id="kt_modal_add_module_form" class="form" action="#"
                                                    method="post">
                                                    @csrf
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span class="required">Module Name</span>
                                                            <span class="ms-2" data-bs-toggle="popover"
                                                                data-bs-trigger="hover" data-bs-html="true"
                                                                data-bs-content="module names is required to be unique.">
                                                                <i class="ki-outline ki-information fs-7"></i>
                                                            </span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input class="form-control form-control-solid"
                                                            placeholder="Enter a module name" name="module_name" />
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Actions-->
                                                    <div class="text-center pt-15">
                                                        <button type="reset" class="btn btn-light me-3"
                                                            data-kt-modules-modal-action="cancel"
                                                            data-bs-dismiss="modal">Discard</button>
                                                        <button type="submit" class="btn btn-primary"
                                                            data-kt-modules-modal-action="submit"
                                                            id="kt_modal_add_module_form_submit">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                                <span
                                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Add modules-->
                                <!--begin::Modal - Update modules-->
                                <div class="modal fade" id="kt_modal_update_module" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-dialog-centered mw-650px">
                                        <!--begin::Modal content-->
                                        <div class="modal-content">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Update module</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-kt-modules-modal-action="close"
                                                    data-bs-dismiss="modal">
                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                <!--begin::Notice-->
                                                <!--begin::Notice-->
                                                <div
                                                    class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                                                    <!--begin::Icon-->
                                                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                                                    <!--end::Icon-->
                                                    <!--begin::Wrapper-->
                                                    <div class="d-flex flex-stack flex-grow-1">
                                                        <!--begin::Content-->
                                                        <div class="fw-semibold">
                                                            <div class="fs-6 text-gray-700">
                                                                <strong class="me-1">Warning!</strong>By editing the
                                                                module name, you might break the system modules
                                                                functionality. Please ensure you're absolutely certain
                                                                before proceeding.
                                                            </div>
                                                        </div>
                                                        <!--end::Content-->
                                                    </div>
                                                    <!--end::Wrapper-->
                                                </div>
                                                <!--end::Notice-->
                                                <!--end::Notice-->
                                                <!--begin::Form-->
                                                <form id="kt_modal_update_module_form" class="form" action="#">
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold form-label mb-2">
                                                            <span class="required">Module Name</span>
                                                            <span class="ms-2" data-bs-toggle="popover"
                                                                data-bs-trigger="hover" data-bs-html="true"
                                                                data-bs-content="module names is required to be unique.">
                                                                <i class="ki-outline ki-information fs-7"></i>
                                                            </span>
                                                        </label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input class="form-control form-control-solid"
                                                            placeholder="Enter a module name" id="kt_modal_update_module_input" name="module_name" />
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Actions-->
                                                    <div class="text-center pt-15">
                                                        <button type="reset" class="btn btn-light me-3"
                                                            data-kt-modules-modal-action="cancel" data-bs-dismiss="modal">Discard</button>
                                                        <button type="submit" class="btn btn-primary"
                                                            id="kt_modal_update_module_form_submit"
                                                            data-kt-modules-modal-action="submit">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                                <span
                                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Update modules-->
                                <!--end::Modals-->
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                    <!--begin::Footer-->
                    <div id="kt_app_footer" class="app-footer">
                        <!--begin::Footer container-->
                        <div
                            class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <!--begin::Copyright-->
                            <div class="text-gray-900 order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">2024&copy;</span>
                                <a href="https://keenthemes.com" target="_blank"
                                    class="text-gray-800 text-hover-primary">Keenthemes</a>
                            </div>
                            <!--end::Copyright-->
                            <!--begin::Menu-->
                            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                                <li class="menu-item">
                                    <a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
                                </li>
                                <li class="menu-item">
                                    <a href="https://devs.keenthemes.com" target="_blank"
                                        class="menu-link px-2">Support</a>
                                </li>
                                <li class="menu-item">
                                    <a href="https://1.envato.market/EA4JP" target="_blank"
                                        class="menu-link px-2">Purchase</a>
                                </li>
                            </ul>
                            <!--end::Menu-->
                        </div>
                        <!--end::Footer container-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->

@endsection

@section('footer-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const updateButtons = document.querySelectorAll('[data-modules-table-filter="update_row"]');
            const modalInput = document.getElementById('kt_modal_update_module_input');

            updateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const moduleName = this.getAttribute('data-module-name');
                    modalInput.value = moduleName;
                    console.log(moduleName);
                });
            });
        });

        t = $("#kt_modules_table").DataTable();

        document.querySelector('[data-kt-modules-table-filter="search"]').addEventListener("keyup", (function(e) {
            t.search(e.target.value).draw()
        }));

        // Add Module start
        // Define form element
        const form = document.getElementById('kt_modal_add_module_form');
        modal = new bootstrap.Modal(document.getElementById('kt_modal_add_module'));

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var validator = FormValidation.formValidation(
            form, {
                fields: {
                    'module_name': {
                        validators: {
                            notEmpty: {
                                message: 'Module name is required'
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

        // Submit button handler
        const submitButton = document.getElementById('kt_modal_add_module_form_submit');
        submitButton.addEventListener('click', function(e) {
            // Prevent default button action
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function(status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        submitButton.disabled = true;

                        $.ajax({
                            url: "{{ route('modules.store') }}",
                            method: 'POST',
                            data: $(form).serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.error) {
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Module not added!",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && modal.hide();
                                            form.reset();
                                            location.reload();
                                            $('#kt_modules_table')
                                                .DataTable().draw()
                                        }));

                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Module has been successfully added!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && modal.hide();
                                            form.reset();
                                            location.reload();
                                        }));

                                    }, 2000);
                                }
                            },
                            error: function() {

                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;

                                // Show popup confirmation
                                Swal.fire({
                                    text: "Module not added!",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then((function(t) {
                                    t.isConfirmed && modal.hide();
                                    form.reset();
                                    location.reload();
                                }));

                            }
                        });
                    }
                });
            }
        });

        // Add Module end



        $(document).on('click', '[data-modules-table-filter="update_row"]', function() {
            updateUrl = $(this).data('url');
        });

        // Define form element
        const updateForm = document.getElementById('kt_modal_update_module_form');
        updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_module'));

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var updateValidator = FormValidation.formValidation(
            updateForm, {
                fields: {
                    'module_name': {
                        validators: {
                            notEmpty: {
                                message: 'Module name is required'
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

        // Submit button handler
        const submitUpdateButton = document.getElementById('kt_modal_update_module_form_submit');
        submitUpdateButton.addEventListener('click', function(e) {
            // Prevent default button action
            e.preventDefault();

            // Validate form before submit
            if (updateValidator) {
                updateValidator.validate().then(function(status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show loading indication
                        submitUpdateButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        submitUpdateButton.disabled = true;

                        $.ajax({
                            url: updateUrl,
                            method: 'PUT',
                            data: $(updateForm).serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.error) {
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitUpdateButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitUpdateButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Module not updated!",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && modal.hide();
                                            updateForm.reset();
                                            location.reload();
                                            $('#kt_modules_table')
                                                .DataTable().draw()
                                        }));

                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        // Remove loading indication
                                        submitUpdateButton.removeAttribute(
                                            'data-kt-indicator');

                                        // Enable button
                                        submitUpdateButton.disabled = false;

                                        // Show popup confirmation
                                        Swal.fire({
                                            text: "Module has been successfully updated!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then((function(t) {
                                            t.isConfirmed && updateModal
                                                .hide();
                                            updateForm.reset();
                                            location.reload();
                                            $('#kt_modules_table')
                                                .DataTable().draw()
                                        }));

                                    }, 2000);
                                }
                            },
                            error: function() {

                                // Remove loading indication
                                submitUpdateButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitUpdateButton.disabled = false;

                                // Show popup confirmation
                                Swal.fire({
                                    text: "Module not updated!",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then((function(t) {
                                    t.isConfirmed && updateModal.hide();
                                    form.reset();
                                    location.reload();
                                }));

                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
