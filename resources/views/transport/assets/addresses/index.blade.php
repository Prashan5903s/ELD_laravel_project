@extends('transport.layout.index')
@section('main-transport-container')
    <!--begin::App-->
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
                                            @lang('lang.addressList')</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">@lang('lang.asset')</li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">@lang('lang.locations')</li>
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
                                                <input type="text" data-kt-addresses-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="@lang('lang.searchAddress')" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        @if ($permissions->contains(4))
                                            <div class="card-toolbar">
                                                <!--begin::Button-->
                                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_add_address">
                                                    <i class="ki-outline ki-plus-square fs-3"></i>@lang('lang.addAddress')</button>
                                                {{-- <a href="{{ route('addresses.create') }}" class="btn btn-light-primary">
                                            <i class="ki-outline ki-plus-square fs-3"></i>Add Address</a> --}}
                                                <!--end::Button-->
                                            </div>
                                        @endif
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0"
                                            id="kt_addresses_table">
                                            <thead>
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-125px">@lang('lang.name')</th>
                                                    <th class="min-w-125px">@lang('lang.address')</th>
                                                    <th class="min-w-125px">@lang('lang.addressType')</th>
                                                    <th class="min-w-125px">@lang('lang.safetyEvExc')</th>
                                                    <th class="min-w-125px">@lang('lang.notes')</th>
                                                    <th class="min-w-125px">@lang('lang.status')</th>
                                                    <th class="min-w-125px">@lang('lang.created')</th>
                                                    <th class="text-end min-w-100px">@lang('lang.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @if (isset($locations) && count($locations) > 0)
                                                    @foreach ($locations as $location)
                                                        <tr>
                                                            <td>{{ $location->name }}</td>
                                                            <td>{{ $location->address }}</td>
                                                            <td data-type="{{ $location->type }}">
                                                                {{ $address_types[$location->type] }}</td>
                                                            <td>{{ $location->tags }}</td>
                                                            <td>{{ $location->notes }}</td>
                                                            <td>
                                                                <div
                                                                    class="badge badge-light-{{ $location->status ? 'success' : 'danger' }}">
                                                                    {{ $location->status ? __('lang.active') : __('lang.deActive') }}
                                                                </div>
                                                            </td>
                                                            <td>{{ date('d M Y, h:i a', strtotime($location->created_at)) }}
                                                            </td>
                                                            <td class="text-end">
                                                                @if ($permissions->contains(5))
                                                                    <button
                                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"
                                                                        data-locations-table-filter="update_row"
                                                                        data-url="{{ isset(request()->lang) ? route('addresses.update', [request()->lang, $location->id]) : route('addresses.update', ['en', $location->id]) }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#kt_modal_update_location">
                                                                        <i class="ki-outline ki-pencil fs-3"></i>
                                                                    </button>
                                                                @endif
                                                                {{-- <button class="btn btn-icon btn-active-light-primary w-30px h-30px" data-url="{{ route('packages.destroy', $package->id) }}" data-packages-table-filter="change_status_row">
                                                                <i class="ki-outline ki-trash fs-3"></i>
                                                            </button> --}}
                                                                <label class="form-switch form-check-solid">
                                                                    <input class="form-check-input border" type="checkbox"
                                                                        data-url="{{ isset(request()->lang) ? route('addresses.destroy', [request()->lang, $location->id]) : route('addresses.destroy', ['en', $location->id]) }}"
                                                                        data-addresses-table-filter="change_status_row"
                                                                        value=""
                                                                        {{ $location->status ? 'checked' : '' }} />
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
        <!--begin::Modals-->
        <!--begin::Modal - Add addresses-->
        <div class="modal fade" id="kt_modal_add_address" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">@lang('lang.addAnAddress')</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-addresses-modal-action="close"
                            data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <form id="kt_modal_add_address_form" class="form" action="#" method="post">
                            @csrf
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.name')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Name is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="@lang('lang.enterName')"
                                    name="name" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.address')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="address is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterAddress')" name="address" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.address') @lang('lang.addressType')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Address Type is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Tags-->
                                <!--begin::Input row-->
                                <div class="d-flex flex-wrap gap-3 justify-content-between">
                                    @if (isset($address_types) && count($address_types) > 0)
                                        @foreach ($address_types as $key => $address_type)
                                            <!--begin::Radio-->
                                            <div class="form-check form-check-custom form-check-solid">
                                                <!--begin::Input-->
                                                <input class="form-check-input me-3 cursor-pointer" name="type"
                                                    type="radio" value="{{ $key }}"
                                                    id="{{ Str::snake($address_type) }}">
                                                <!--end::Input-->
                                                <!--begin::Label-->
                                                <label class="form-check-label cursor-pointer"
                                                    for="{{ Str::snake($address_type) }}">
                                                    <div class="fw-bold text-gray-800">{{ $address_type }}</div>
                                                </label>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Radio-->
                                        @endforeach
                                    @endif
                                </div>
                                <!--end::Input row-->
                                <!--end::Tags-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.tags')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="tags[]" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" multiple>
                                    <option></option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.notes')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterNotes')" name="notes" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            {{-- <!--start:Map-->
                        <div id="map"></div>
                        <!--end:Map--> --}}
                            <!--begin::Actions-->
                            <div class="text-center pt-15">
                                <button type="reset" class="btn btn-light me-3" data-kt-addresses-modal-action="cancel"
                                    data-bs-dismiss="modal">@lang('lang.discard')</button>
                                <button type="submit" class="btn btn-primary" data-kt-addresses-modal-action="submit"
                                    id="kt_modal_add_address_form_submit">
                                    <span class="indicator-label">@lang('lang.submit')</span>
                                    <span class="indicator-progress">@lang('lang.pleaseWait')
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
        <!--end::Modal - Add addresses-->
        <!--begin::Modal - Update addresses-->
        <div class="modal fade" id="kt_modal_update_location" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">@lang('lang.updateAddress')</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-addresses-modal-action="close"
                            data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <form id="kt_modal_update_location_form" class="form" action="#">
                            @csrf
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.name')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Name is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="@lang('lang.enterName')"
                                    name="name" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.address')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="address is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterAddress')" name="address" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.address') @lang('lang.addressType')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Address Type is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Tags-->
                                <!--begin::Input row-->
                                <div class="d-flex flex-wrap gap-3 justify-content-between">
                                    @if (isset($address_types) && count($address_types) > 0)
                                        @foreach ($address_types as $key => $address_type)
                                            <!--begin::Radio-->
                                            <div class="form-check form-check-custom form-check-solid">
                                                <!--begin::Input-->
                                                <input class="form-check-input me-3 cursor-pointer" name="type"
                                                    type="radio" value="{{ $key }}"
                                                    id="{{ Str::snake($address_type) }}_update">
                                                <!--end::Input-->
                                                <!--begin::Label-->
                                                <label class="form-check-label cursor-pointer"
                                                    for="{{ Str::snake($address_type) }}_update">
                                                    <div class="fw-bold text-gray-800">{{ $address_type }}</div>
                                                </label>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Radio-->
                                        @endforeach
                                    @endif
                                </div>
                                <!--end::Input row-->
                                <!--end::Tags-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.tags')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="tags[]" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" multiple>
                                    <option></option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.notes')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterNotes')" name="notes" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center pt-15">
                                <button type="reset" class="btn btn-light me-3" data-kt-addresses-modal-action="cancel"
                                    data-bs-dismiss="modal">@lang('lang.discard')</button>
                                <button type="submit" class="btn btn-primary" id="kt_modal_update_location_form_submit"
                                    data-kt-addresses-modal-action="submit">
                                    <span class="indicator-label">@lang('lang.submit')</span>
                                    <span class="indicator-progress">@lang('lang.pleaseWait')
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
        <!--end::Modal - Update addresses-->
        <!--end::Modals-->
    @endsection

    @section('footer-script')
        <script>
            t = $("#kt_addresses_table").DataTable();

            document.querySelector('[data-kt-addresses-table-filter="search"]').addEventListener("keyup", (function(e) {
                t.search(e.target.value).draw()
            }));

            // Add Address and Geofence start
            // Define form element
            const form = document.getElementById('kt_modal_add_address_form');
            modal = new bootstrap.Modal(document.getElementById('kt_modal_add_address'));

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'name': {
                            validators: {
                                notEmpty: {
                                    message: 'Name is required'
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
                        'type': {
                            validators: {
                                notEmpty: {
                                    message: 'Address Type is required'
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
            const submitButton = document.getElementById('kt_modal_add_address_form_submit');
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
                                url: "{{ isset(request()->lang) ? route('addresses.store', request()->lang) : route('addresses.store', 'en') }}",
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
                                                text: "Address not added!",
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
                                                customClass: {
                                                    confirmButton: "btn btn-primary"
                                                }
                                            }).then((function(t) {
                                                t.isConfirmed && modal.hide();
                                                form.reset();
                                                location.reload();
                                                $('#kt_addresses_table')
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
                                                text: "Address has been successfully added!",
                                                icon: "success",
                                                buttonsStyling: false,
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                        text: "Address not added!",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "{{ __('lang.okGotIt') }}",
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

            // Add address end


            $(document).on('click', '[data-locations-table-filter="update_row"]', function() {
                updateUrl = $(this).data('url');
                td = $(this).closest('tr').children('td');
                $('.modal-body input[name="name"]').val(td.eq(0).text());
                $('.modal-body textarea[name="address"]').val(td.eq(1).text());
                $('.modal-body input[name="type"][value="' + td.eq(2).data('type') + '"]').prop('checked', true);
                $('.modal-body textarea[name="notes"]').val(td.eq(4).text());
            });

            // Define form element
            const updateForm = document.getElementById('kt_modal_update_location_form');
            updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_location'));

            var updateValidator = FormValidation.formValidation(
                updateForm, {
                    fields: {
                        'name': {
                            validators: {
                                notEmpty: {
                                    message: 'Name is required'
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
                        'type': {
                            validators: {
                                notEmpty: {
                                    message: 'Address Type is required'
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
            const submitUpdateButton = document.getElementById('kt_modal_update_location_form_submit');
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
                                                text: "Address not updated!",
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                                text: "Address has been successfully updated!",
                                                icon: "success",
                                                buttonsStyling: false,
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
                                                customClass: {
                                                    confirmButton: "btn btn-primary"
                                                }
                                            }).then((function(t) {
                                                t.isConfirmed && updateModal
                                                    .hide();
                                                updateForm.reset();
                                                location.reload();
                                                // $('#kt_packages_table').DataTable().draw()
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
                                        text: "Address not updated!",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "{{ __('lang.okGotIt') }}",
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
            // Update form end


            // Select all status buttons
            const changeStatusButtons = document.querySelectorAll('[data-addresses-table-filter="change_status_row"]');

            changeStatusButtons.forEach(d => {
                // Delete button on click
                d.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Get url
                    const url = $(d).data('url');

                    // Select parent row
                    const parent = e.target.closest('tr');

                    // Get package name
                    const packageName = parent.querySelectorAll('td')[0].innerText;

                    if (d.checked) {

                        Swal.fire({
                            text: "{{ __('lang.areSure') }} {{ __('lang.activate') }} " +
                                packageName + "?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "{{ __('lang.yes') }}, {{ __('lang.activate') }}!",
                            cancelButtonText: "__('lang.no'), {{ __('lang.cancel') }}",
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
                                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });

                    } else {

                        Swal.fire({
                            text: "{{ __('lang.areSure') }} {{ __('lang.deactivate') }} " +
                                packageName + "?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "{{ __('lang.yes') }}, {{ __('lang.deactivate') }}!",
                            cancelButtonText: "{{ __('lang.no') }}, {{ __('lang.cancel') }}",
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
                                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                                confirmButtonText: "{{ __('lang.okGotIt') }}",
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
                                    confirmButtonText: "{{ __('lang.okGotIt') }}",
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

    @section('footer-script-link')
        {{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJQWhNtw7sM42aBdnTpmeE92RR4v0U6Wg&loading=async&callback=initMap">
</script>
<script>
    function initMap() {
        const mapOptions = {
            center: { lat: -33.860664, lng: 151.208138 },
            zoom: 14
        };
        const mapDiv = document.getElementById('map');
        const map = new google.maps.Map(mapDiv, mapOptions);
        return map;
    }
</script> --}}
    @endsection