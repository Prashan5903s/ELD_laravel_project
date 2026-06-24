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
                                            Package List</h1>
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
                                            <li class="breadcrumb-item text-muted">Packages</li>
                                            <!--end::Item-->
                                        </ul>
                                        <!--end::Breadcrumb-->
                                    </div>
                                    <!--end::Page title-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                                        <a href="{{ route('packages.create') }}" class="btn btn-light-primary">
                                            <i class="ki-outline ki-plus-square fs-3"></i>Add Package</a>
                                    </div>
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
                                <!--begin::Row-->
                                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
                                    @if (isset($packages) && count($packages) > 0)

                                        @foreach ($packages as $package)
                                            <!--begin::Col-->
                                            <div class="col-md-4">
                                                <!--begin::Card-->
                                                <div class="card card-flush h-md-100">
                                                    <!--begin::Card header-->
                                                    <div class="card-header">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>{{ $package->name }}</h2>
                                                        </div>
                                                        <!--end::Card title-->
                                                        <div class="card-toolbar">
                                                            <label class="form-switch form-check-solid">
                                                                <input class="form-check-input border cursor-pointer"
                                                                    type="checkbox"
                                                                    data-url="{{ route('packages.destroy', $package->id) }}"
                                                                    data-packages-table-filter="delete_row" value=""
                                                                    {{ $package->status ? 'checked' : '' }} />
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-1">
                                                        <!--begin::Users-->
                                                        <div class="fw-bold text-gray-600 mb-3">{{ $package->package_code }}
                                                        </div>
                                                        <div class="fw-bold text-gray-600 mb-3">
                                                            {{ $package->duration->title }}
                                                        </div>
                                                        <div class="fw-bold text-gray-600 mb-3">
                                                            @if ($package->packageType)
                                                                <div class="fw-bold text-gray-600 mb-3">
                                                                    {{ $package->packageType->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="fw-bold text-gray-600 mb-3">
                                                            {{ $package->currency->symbol }} {{ $package->price }}
                                                        </div>
                                                        <div class="fw-bold text-gray-600 mb-5">
                                                            {{ $package->description }}
                                                        </div>
                                                        <!--end::Users-->
                                                        <!--begin::Permissions-->
                                                        @if (isset($package->modules) && count($package->modules) > 0)
                                                            <div class="d-flex flex-column text-gray-600">
                                                                @foreach ($package->modules as $key => $module)
                                                                    @if ($key == 5)
                                                                    @break
                                                                @endif
                                                                <div class="d-flex align-items-center py-2">
                                                                    <span class="bullet bg-primary me-3"></span>
                                                                    {{ $module->name }}
                                                                </div>
                                                            @endforeach
                                                            @if (count($package->modules) > 5)
                                                                <div class='d-flex align-items-center py-2'>
                                                                    <span class='bullet bg-primary me-3'></span>
                                                                    <em>and {{ count($package->modules) - 5 }}
                                                                        more...</em>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <!--end::Permissions-->
                                                </div>
                                                <!--end::Card body-->
                                                <!--begin::Card footer-->
                                                <div class="card-footer flex-wrap pt-0">
                                                    <a href="{{ route('packages.edit', $package->id) }}"
                                                        class="btn btn-light btn-active-light-primary my-1">Edit
                                                        Package</a>
                                                </div>
                                                <!--end::Card footer-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->
                                    @endforeach


                                @endif
                                <!--begin::Add new card-->
                                <div class="ol-md-4">
                                    <!--begin::Card-->
                                    <div class="card h-md-100">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center">
                                            <!--begin::Button-->
                                            <a href="{{ route('packages.create') }}"
                                                class="btn btn-clear d-flex flex-column flex-center">
                                                <!--begin::Illustration-->
                                                <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}"
                                                    alt="" class="mw-100 mh-150px mb-7" />
                                                <!--end::Illustration-->
                                                <!--begin::Label-->
                                                <div class="fw-bold fs-3 text-gray-600 text-hover-primary">Add New
                                                    Package</div>
                                                <!--end::Label-->
                                            </a>
                                            <!--begin::Button-->
                                        </div>
                                        <!--begin::Card body-->
                                    </div>
                                    <!--begin::Card-->
                                </div>
                                <!--begin::Add new card-->
                            </div>
                            <!--end::Row-->
                            <!--begin::Card-->
                            {{-- <div class="card card-flush">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1 me-5">
                                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                                <input type="text" data-kt-packages-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search Packages" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Button-->
                                            <a href="{{ route('packages.create') }}" class="btn btn-light-primary">
                                            <i class="ki-outline ki-plus-square fs-3"></i>Add Package</a>
                                            <!--end::Button-->
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_packages_table">
                                            <thead>
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-125px">Name</th>
                                                    <th class="min-w-125px">Code</th>
                                                    <th class="min-w-125px">Description</th>
                                                    <th class="min-w-125px">Duration</th>
                                                    <th class="min-w-125px">Status</th>
                                                    <th class="min-w-125px">Created Date</th>
                                                    <th class="text-end min-w-100px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @if (isset($packages) && count($packages) > 0)
                                                @foreach ($packages as $package)
                                                    <tr>
                                                        <td>{{ $package->name }}</td>
                                                        <td>{{ $package->package_code }}</td>
                                                        <td>{{ Str::limit($package->description, 50) }}</td>
                                                        <td data-duration="{{ $package->duration }}">{{ $durations[$package->duration] }}</td>
                                                        <td><div class="badge badge-light-{{ $package->status ? 'success' : 'danger' }}">{{ $package->status ? "Active" : 'Deactive' }}</div></td>
                                                        <td>{{ date('d M Y, h:i a', strtotime($package->created_at)) }}</td>
                                                        <td class="text-end">
                                                            <button class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" data-packages-table-filter="update_row" data-url="{{ route('packages.update', $package->id) }}" data-bs-toggle="modal" data-bs-target="#kt_modal_update_package">
                                                                <i class="ki-outline ki-setting-3 fs-3"></i>
                                                            </button>
                                                            <label class="form-switch form-check-solid">
                                                                <input class="form-check-input border" type="checkbox" data-url="{{ route('packages.destroy', $package->id) }}" data-packages-table-filter="delete_row" value="" {{ $package->status ? 'checked' : '' }}/>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Card body-->
                                </div> --}}
                            <!--end::Card-->
                            <!--begin::Modals-->
                            <!--begin::Modal - Update packages-->
                            <div class="modal fade" id="kt_modal_update_package" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header">
                                            <!--begin::Modal title-->
                                            <h2 class="fw-bold">Update Package</h2>
                                            <!--end::Modal title-->
                                            <!--begin::Close-->
                                            <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                data-bs-dismiss="modal" data-kt-packages-modal-action="close">
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
                                                            package name, you might break the system packages
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
                                            <form id="kt_modal_update_package_form" class="form" action="#">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold form-label mb-2">
                                                        <span class="required">Package Name</span>
                                                        <span class="ms-2" data-bs-toggle="popover"
                                                            data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-content="Package name is required to be unique.">
                                                            <i class="ki-outline ki-information fs-7"></i>
                                                        </span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-solid"
                                                        placeholder="Enter a package name" name="package_name" />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="fs-5 fw-bold form-label mb-2">
                                                        <span class="required">Package code</span>
                                                        <span class="ms-2" data-bs-toggle="popover"
                                                            data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-content="Package code is required to be unique.">
                                                            <i class="ki-outline ki-information fs-7"></i>
                                                        </span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-solid"
                                                        placeholder="Enter a package code" name="package_code"
                                                        value="" required />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold form-label mb-2">
                                                        <span>Description</span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <textarea class="form-control form-control-solid" name="description" placeholder="Enter description here"
                                                        cols="30" rows="4"></textarea>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold form-label mb-2">
                                                        <span class="required">Duration</span>
                                                        <span class="ms-2" data-bs-toggle="popover"
                                                            data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-content="Package duration is required.">
                                                            <i class="ki-outline ki-information fs-7"></i>
                                                        </span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select class="form-select form-select-solid" name="duration"
                                                        data-control="select2"
                                                        data-dropdown-parent="#kt_modal_update_package"
                                                        data-placeholder="Select a duration">
                                                        <option></option>
                                                        @if (isset($durations) && count($durations) > 1)
                                                            @foreach ($durations as $key => $value)
                                                                <option value="{{ $key }}">
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-10">
                                                    <!--begin::Label-->
                                                    <label class="fs-5 fw-bold form-label mb-2">
                                                        <span>Module</span>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select name="module_id[]" class="form-select form-select-solid"
                                                        data-control="select2" data-placeholder="Select a module"
                                                        multiple="multiple">
                                                        <option></option>
                                                        @if (isset($modules) && count($modules) > 0)
                                                            @foreach ($modules as $module)
                                                                <option value="{{ $module->id }}">
                                                                    {{ $module->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="text-center pt-15">
                                                    <button type="reset" class="btn btn-light me-3"
                                                        data-kt-packages-modal-action="cancel"
                                                        data-bs-dismiss="modal">Discard</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        id="kt_modal_update_package_form_submit"
                                                        data-kt-packages-modal-action="submit">
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
                            <!--end::Modal - Update packages-->
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
    let table = $('#kt_packages_table').DataTable({
        'info': false
    });

    $(document).on('click', '[data-packages-table-filter="update_row"]', function() {
        updateUrl = $(this).data('url');
        td = $(this).closest('tr').children('td');
        $('.modal-body input[name="package_name"]').val(td.eq(0).text());
        $('.modal-body input[name="package_code"]').val(td.eq(1).text());
        $('.modal-body textarea[name="description"]').val(td.eq(2).text());
        $('.modal-body select[name="duration"]').select2("val", td.eq(3).data('duration').toString());
    });

    // Define form element
    const updateForm = document.getElementById('kt_modal_update_package_form');
    updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_package'));

    var updateValidator = FormValidation.formValidation(
        updateForm, {
            fields: {
                'package_name': {
                    validators: {
                        notEmpty: {
                            message: 'Package name is required'
                        }
                    }
                },
                'package_code': {
                    validators: {
                        notEmpty: {
                            message: 'Package code is required'
                        }
                    }
                },
                'duration': {
                    validators: {
                        notEmpty: {
                            message: 'Duration is required'
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
    const submitUpdateButton = document.getElementById('kt_modal_update_package_form_submit');
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
                                        text: "Package not updated!",
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
                                        text: "Package has been successfully updated!",
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
                                        $('#kt_packages_table')
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
                                text: "Package not updated!",
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


    // Select all delete buttons
    const deleteButtons = document.querySelectorAll('[data-packages-table-filter="delete_row"]');

    deleteButtons.forEach(d => {
        // Delete button on click
        d.addEventListener('click', function(e) {
            e.preventDefault();
            console.log(d.checked);

            // Get url
            const url = $(d).data('url');

            // Select parent row
            const parent = e.target.closest('.card-header');

            // Get package name
            const packageName = parent.querySelectorAll('.card-title *')[0].innerText;

            if (d.checked) {

                Swal.fire({
                    text: "Are you sure you want to activate " + packageName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, activate!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            text: "Activating " + packageName,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                success: function(response) {
                                    if (response.error) {
                                        Swal.fire({
                                            text: packageName +
                                                " was not activated.",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function(t) {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            text: "You have activated " +
                                                packageName + "!.",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function(t) {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        text: packageName +
                                            " was not activated.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                },

                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: packageName + " was not activated.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });

            } else {

                Swal.fire({
                    text: "Are you sure you want to deactivate " + packageName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, deactivate!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            text: "Deactivating " + packageName,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                success: function(response) {
                                    if (response.error) {
                                        Swal.fire({
                                            text: packageName +
                                                " was not deactivated.",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function(t) {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            text: "You have deactivated " +
                                                packageName + "!.",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function(t) {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        text: packageName +
                                            " was not deactivated.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                },

                            });
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: packageName + " was not deactivated.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            }
        })
    });
</script>
@endsection
