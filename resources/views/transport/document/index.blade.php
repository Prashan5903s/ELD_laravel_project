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
                                            @lang('lang.document') @lang('lang.list')</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ isset(request()->lang) ? route('transport.dashboard', request()->lang) : route('transport.dashboard') }}"
                                                    class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">@lang('lang.document')</li>
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
                                                <input type="text" data-kt-documents-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="@lang('lang.searchDocument')" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        @if ($permissions->contains(7))
                                            <div class="card-toolbar">
                                                <!--begin::Button-->
                                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_add_document">
                                                    <i class="ki-outline ki-plus-square fs-3"></i>@lang('lang.addDocument')</button>
                                                {{-- <a href="{{ route('document.create') }}" class="btn btn-light-primary">
                                            <i class="ki-outline ki-plus-square fs-3"></i>Add document</a> --}}
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
                                            id="kt_documents_table">
                                            <thead>
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-125px">Driver</th>
                                                    <th class="min-w-125px">Document Type</th>
                                                    <th class="min-w-125px">File</th>
                                                    <th class="min-w-125px">Note</th>
                                                    <th class="min-w-125px">Status</th>
                                                    <th class="min-w-125px">Created Date</th>
                                                    <th class="text-end min-w-100px">@lang('lang.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @if (isset($documents) && count($documents) > 0)
                                                    @foreach ($documents as $document)
                                                        <tr>
                                                            <td data-id="{{ $document->driver_id }}"
                                                                class="d-flex align-items-center">
                                                                <div
                                                                    class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                                    <a href="#">
                                                                        <div class="symbol-label">
                                                                            <img src="{{ !empty($document->driver->avatar_image) ? asset('driverss/' . $document->driver->avatar_image) : asset('assets/media/svg/avatars/blank.svg') }}"
                                                                                alt="{{ $document->driver->first_name }}"
                                                                                class="w-100" />
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                                <div class="d-flex flex-column">
                                                                    <a href="#"
                                                                        class="text-gray-800 text-hover-primary mb-1">{{ $document->driver->first_name }}
                                                                        {{ $document->driver->last_name }}</a>
                                                                    <span>{{ $document->driver->email }}</span>
                                                                </div>
                                                            </td>
                                                            {{-- <td data-id="{{ $document->driver_id }}">{{ $document->driver->first_name . " " . $document->driver->last_name }}</td> --}}
                                                            <td data-id="{{ $document->document_type }}"
                                                                data-image="{{ $document->image }}">
                                                                {{ isset($document_types) && count($document_types) > 0 ? $document_types->where('option_id', $document->document_type)->first()->title ?? '' : '' }}
                                                            </td>
                                                            <td>
                                                                @if (isset($document->image) && $document->image != '')
                                                                    <button
                                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"
                                                                        data-documents-table-filter="view_file"
                                                                        data-file="{{ asset('documents/' . $document->image) }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#kt_modal_view_document">
                                                                        <i class="ki-outline ki-eye fs-3"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                            <td>{{ Str::limit($document->note, 50) }}</td>
                                                            <td data-status-id="{{ $document->status }}">
                                                                <div
                                                                    class="badge badge-light-{{ $document->status ? 'success' : 'danger' }}">
                                                                    {{ $document->status ? __('lang.active') : __('lang.deActive') }}
                                                                </div>
                                                            </td>
                                                            <td>{{ date('d M Y, h:i a', strtotime($document->created_at)) }}
                                                            </td>
                                                            <td class="text-end">
                                                                @if ($permissions->contains(8))
                                                                    <button
                                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"
                                                                        data-documents-table-filter="update_row"
                                                                        data-url="{{ isset(request()->lang) ? route('document.update', [request()->lang, $document->id]) : route('document.update', ['en', $document->id]) }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#kt_modal_update_document">
                                                                        <i class="ki-outline ki-pencil fs-3"></i>
                                                                    </button>
                                                                @endif
                                                                {{-- <button class="btn btn-icon btn-active-light-primary w-30px h-30px" data-url="{{ route('documents.destroy', $package->id) }}" data-documents-table-filter="delete_row">
                                                                <i class="ki-outline ki-trash fs-3"></i>
                                                            </button> --}}
                                                                <label class="form-switch form-check-solid">
                                                                    <input class="form-check-input border" type="checkbox"
                                                                        data-url="{{ isset(request()->lang) ? route('document.destroy', [request()->lang, $document->id]) : route('document.destroy', ['en', $document->id]) }}"
                                                                        data-documents-table-filter="change_status_row"
                                                                        value=""
                                                                        {{ $document->status ? 'checked' : '' }} />
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
        <!--begin::Modal - View document-->
        <div class="modal fade" id="kt_modal_view_document" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">File</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-document-modal-action="close"
                            data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <div id="file-preview" class="h-600px"></div>
                        <!--end::Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - View document-->
        <!--begin::Modal - Add documents-->
        <div class="modal fade" id="kt_modal_add_document" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">@lang('lang.addADocument')</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-document-modal-action="close"
                            data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <form id="kt_modal_add_document_form" class="form" action="#" method="post"
                            enctype='multipart/form-data'>
                            @csrf

                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Driver</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Driver is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="driver_id"
                                    data-dropdown-parent="#kt_modal_add_document" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" required>
                                    <option></option>
                                    @if (isset($drivers))
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->first_name }}
                                                {{ $driver->last_name }}
                                                {{ '(' . trim(explode(',', $driver->country_code)[0]) }}
                                                {{ $driver->mobile_no . ')' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                {{-- <input class="form-control form-control-solid" placeholder="@lang('lang.enterName')" name="name" /> --}}
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Document Type</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Document Type is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="document_type"
                                    data-dropdown-parent="#kt_modal_add_document" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" required>
                                    <option></option>
                                    @if (isset($document_types))
                                        @foreach ($document_types as $document_type)
                                            <option value="{{ $document_type->option_id }}">{{ $document_type->title }}
                                            </option>
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
                                    <span class="required">Image</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Image is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <div class="form-control form-control-transparent">
                                    <!--begin::Image input-->
                                    <div class="image-input image-input-outline image-input-empty"
                                        data-kt-image-input="true"
                                        style="background-image: url(/assets/media/svg/files/blank-image.svg);">
                                        <!--begin::Image preview wrapper-->
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-position: center"></div>
                                        <!--end::Image preview wrapper-->

                                        <!--begin::Edit button-->
                                        <label
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Change image">
                                            <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span
                                                    class="path2"></span></i>

                                            <!--begin::Inputs-->
                                            <input type="file" name="image" accept=".jpg, .jpeg, .pdf" required />
                                            <input type="hidden" name="image_remove" />
                                            <!--end::Inputs-->
                                        </label>
                                        <!--end::Edit button-->

                                        <!--begin::Cancel button-->
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Cancel image">
                                            <i class="ki-outline ki-cross fs-3"></i>
                                        </span>
                                        <!--end::Cancel button-->

                                        <!--begin::Remove button-->
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Remove image">
                                            <i class="ki-outline ki-cross fs-3"></i>
                                        </span>
                                        <!--end::Remove button-->
                                    </div>
                                    <!--end::Image input-->
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.note')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterNotes')" name="note" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.status')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Status is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="status"
                                    data-dropdown-parent="#kt_modal_add_document" data-control="select2"
                                    data-minimum-results-for-search="Infinity" data-placeholder="@lang('lang.selectOption')"
                                    required>
                                    <option></option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center pt-15">
                                <button type="reset" class="btn btn-light me-3" data-kt-addresses-modal-action="cancel"
                                    data-bs-dismiss="modal">@lang('lang.discard')</button>
                                <button type="submit" class="btn btn-primary" data-kt-addresses-modal-action="submit"
                                    id="kt_modal_add_document_form_submit">
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
        <!--end::Modal - Add documents-->
        <!--begin::Modal - Update documents-->
        <div class="modal fade" id="kt_modal_update_document" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">@lang('lang.updatedocument')</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"
                            data-kt-documents-modal-action="close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <form id="kt_modal_update_document_form" class="form" action="#"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Driver</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Driver is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="driver_id"
                                    data-dropdown-parent="#kt_modal_update_document" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" required>
                                    <option></option>
                                    @if (isset($drivers))
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->first_name }}
                                                {{ $driver->last_name }}
                                                {{ '(' . trim(explode(',', $driver->country_code)[0]) }}
                                                {{ $driver->mobile_no . ')' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                {{-- <input class="form-control form-control-solid" placeholder="@lang('lang.enterName')" name="name" /> --}}
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Document Type</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Document Type is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="document_type"
                                    data-dropdown-parent="#kt_modal_update_document" data-control="select2"
                                    data-placeholder="@lang('lang.selectOption')" required>
                                    <option></option>
                                    @if (isset($document_types))
                                        @foreach ($document_types as $document_type)
                                            <option value="{{ $document_type->option_id }}">{{ $document_type->title }}
                                            </option>
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
                                    <span class="required">Image</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Image is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <div class="form-control form-control-transparent">
                                    <!--begin::Image input-->
                                    <div class="image-input image-input-outline image-input-empty"
                                        data-kt-image-input="true"
                                        style="background-image: url(/assets/media/svg/files/blank-image.svg);">
                                        <!--begin::Image preview wrapper-->
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-position: center"></div>
                                        <!--end::Image preview wrapper-->

                                        <!--begin::Edit button-->
                                        <label
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Change image">
                                            <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span
                                                    class="path2"></span></i>

                                            <!--begin::Inputs-->
                                            <input type="file" name="image" accept=".jpg, .jpeg, .pdf" required />
                                            <input type="hidden" name="image_remove" />
                                            <!--end::Inputs-->
                                        </label>
                                        <!--end::Edit button-->

                                        <!--begin::Cancel button-->
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Cancel image">
                                            <i class="ki-outline ki-cross fs-3"></i>
                                        </span>
                                        <!--end::Cancel button-->

                                        <!--begin::Remove button-->
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            data-bs-dismiss="click" title="Remove image">
                                            <i class="ki-outline ki-cross fs-3"></i>
                                        </span>
                                        <!--end::Remove button-->
                                    </div>
                                    <!--end::Image input-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span>@lang('lang.note')</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control form-control-solid" placeholder="@lang('lang.enterNotes')" name="note" cols="30"
                                    rows="3"></textarea>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">@lang('lang.status')</span>
                                    <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-html="true" data-bs-content="Status is required.">
                                        <i class="ki-outline ki-information fs-7"></i>
                                    </span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" name="status"
                                    data-dropdown-parent="#kt_modal_update_document" data-control="select2"
                                    data-minimum-results-for-search="Infinity" data-placeholder="@lang('lang.selectOption')"
                                    required>
                                    <option></option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center pt-15">
                                <button type="reset" class="btn btn-light me-3" data-kt-documents-modal-action="cancel"
                                    data-bs-dismiss="modal">@lang('lang.discard')</button>
                                <button type="submit" class="btn btn-primary" id="kt_modal_update_document_form_submit"
                                    data-kt-vehicls-modal-action="submit">
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
        <!--end::Modal - Update documents-->
        <!--end::Modals-->
    @endsection

    @section('footer-script')
        <script>
            // Function to handle file input change
            function handleAddFileInput() {
                const fileInput = document.querySelector('#kt_modal_add_document_form input[type="file"]');
                const file = fileInput.files[0];
                const imagePreviewWrapper = document.querySelector('#kt_modal_add_document_form .image-input-wrapper');

                // Check if file is selected
                if (file) {
                    // Clear existing content
                    imagePreviewWrapper.innerHTML = '';

                    if (file.type === 'application/pdf') {
                        // Read PDF file as data URL
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Create embed element for PDF
                            const embedElement = document.createElement('embed');
                            embedElement.type = 'application/pdf';
                            embedElement.src = e.target.result;
                            embedElement.width = '100%';
                            embedElement.height = '100%';
                            // Append embed element to preview wrapper
                            imagePreviewWrapper.appendChild(embedElement);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#kt_modal_add_document_form .image-input-wrapper embed').remove();
                    }
                }
            }

            // Add event listener for file input change
            document.querySelector('#kt_modal_add_document_form input[type="file"]').addEventListener('change',
                handleAddFileInput);


            // Function to handle cancel button click
            function handleAddCancelClick() {
                const imagePreviewWrapper = document.querySelector('#kt_modal_add_document_form .image-input-wrapper');
                // Clear the content of the preview wrapper
                imagePreviewWrapper.innerHTML = '';
            }

            // Add event listener for cancel button click
            const addCancelButton = document.querySelector('#kt_modal_add_document_form [data-kt-image-input-action="cancel"]');
            addCancelButton.addEventListener('click', handleAddCancelClick);


            // Function to handle file input change
            function handleEditFileInput() {
                const fileInput = document.querySelector('#kt_modal_update_document_form input[type="file"]');
                const file = fileInput.files[0];
                const imagePreviewWrapper = document.querySelector('#kt_modal_update_document_form .image-input-wrapper');
                // Check if file is selected
                if (file) {
                    // Clear existing content
                    imagePreviewWrapper.innerHTML = '';

                    if (file.type === 'application/pdf') {
                        // Read PDF file as data URL
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Create embed element for PDF
                            const embedElement = document.createElement('embed');
                            embedElement.type = 'application/pdf';
                            embedElement.src = e.target.result;
                            embedElement.width = '100%';
                            embedElement.height = '100%';
                            // Append embed element to preview wrapper
                            imagePreviewWrapper.appendChild(embedElement);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#kt_modal_update_document_form .image-input-wrapper embed').remove();
                    }
                }
            }

            // Add event listener for file input change
            document.querySelector('#kt_modal_update_document_form input[type="file"]').addEventListener('change',
                handleEditFileInput);


            // Function to handle cancel button click
            function handleEditCancelClick() {
                const imagePreviewWrapper = document.querySelector('#kt_modal_update_document_form .image-input-wrapper');
                // Clear the content of the preview wrapper
                imagePreviewWrapper.innerHTML = '';
            }

            // Add event listener for cancel button click
            const cancelButton = document.querySelector('#kt_modal_update_document_form [data-kt-image-input-action="cancel"]');
            cancelButton.addEventListener('click', handleEditCancelClick);


            t = $("#kt_documents_table").DataTable({
                "language": {
                    "emptyTable": "@lang('lang.emptyTable')"
                }
            });


            // Add Address and Geofence start
            // Define form element
            const form = document.getElementById('kt_modal_add_document_form');
            modal = new bootstrap.Modal(document.getElementById('kt_modal_add_document'));

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'driver_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver is required'
                                }
                            }
                        },
                        'document_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Document Type is required'
                                }
                            }
                        },
                        'image': {
                            validators: {
                                notEmpty: {
                                    message: 'Image is required'
                                },
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: 3145728, // 3 * 1024 * 1024 (3 MB in bytes)
                                    message: 'The selected file is not valid',
                                },
                            }
                        },
                        'status': {
                            validators: {
                                notEmpty: {
                                    message: 'Status is required'
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
            const submitButton = document.getElementById('kt_modal_add_document_form_submit');
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
                            let formData = new FormData(form);

                            $.ajax({
                                url: "{{ isset(request()->lang) ? route('document.store', request()->lang) : route('document.store', 'en') }}",
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
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
                                                text: "document not added!",
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
                                                text: "document has been successfully added!",
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
                                        text: "document not added!",
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


            $(document).on('click', '[data-documents-table-filter="update_row"]', function() {
                updateUrl = $(this).data('url');
                td = $(this).closest('tr').children('td');

                var fileUrl = "documents/" + td.eq(1).data('image'); // Assuming 'file' attribute contains the file path
                var extension = fileUrl.split('.').pop().toLowerCase();

                if (extension === 'pdf') {
                    // If the file is a PDF
                    var embedElement = $('<embed>');
                    embedElement.attr('src', fileUrl);
                    embedElement.attr('type', 'application/pdf');
                    embedElement.css('width', '100%');
                    embedElement.css('height', '100%');

                    $('#kt_modal_update_document_form .image-input-wrapper').html(embedElement);
                } else {
                    // If the file is an image
                    var imageUrl = "documents/" + td.eq(1).data('image');
                    $('#kt_modal_update_document_form .image-input-wrapper').css('background-image', 'url(' + imageUrl +
                        ')');
                    $('#kt_modal_update_document_form .image-input-wrapper embed').remove();
                }

                // imageUrl = "documents/"+td.eq(1).data('image');
                // $('#kt_modal_update_document_form .image-input-wrapper').css('background-image', 'url(' + imageUrl + ')');
                $('#kt_modal_update_document .modal-body select[name="driver_id"]').val(td.eq(0).data('id')).trigger(
                    'change');
                $('#kt_modal_update_document .modal-body select[name="document_type"]').val(td.eq(1).data('id'))
                    .trigger('change');
                $('#kt_modal_update_document .modal-body textarea[name="note"]').val(td.eq(3).text());
                $('#kt_modal_update_document .modal-body select[name="status"]').val(td.eq(4).data('status-id'))
                    .trigger('change');
                // console.log(td);
            });


            $(document).on('click', '[data-documents-table-filter="view_file"]', function() {
                updateUrl = $(this).data('url');
                td = $(this).closest('tr').children('td');

                var fileUrl = $(this).data('file'); // Assuming 'file' attribute contains the file path
                var extension = fileUrl.split('.').pop().toLowerCase();

                if (extension === 'pdf') {
                    // If the file is a PDF
                    var embedElement = $('<embed>');
                    embedElement.attr('src', fileUrl + "#toolbar=0");
                    embedElement.attr('type', 'application/pdf');
                    embedElement.css('width', '100%');
                    embedElement.css('height', '100%');

                    $('#kt_modal_view_document #file-preview').html(embedElement);
                } else {
                    // If the file is an image
                    var imageUrl = $(this).data('file');
                    var imageElement = $('<img>');
                    imageElement.attr('src', imageUrl);
                    // embedElement.attr('type', 'application/pdf');
                    imageElement.css('width', '100%');
                    imageElement.css('height', '100%');
                    imageElement.css('object-fit', 'contain');

                    $('#kt_modal_view_document #file-preview').html(imageElement);
                    // $('#kt_modal_view_document #file-preview').css('background-image', 'url(' + imageUrl + ')');
                    $('#kt_modal_view_document #file-preview embed').remove();
                }

                // imageUrl = "documents/"+td.eq(1).data('image');
                // $('#kt_modal_update_document_form .image-input-wrapper').css('background-image', 'url(' + imageUrl + ')');
                $('#kt_modal_update_document .modal-body select[name="driver_id"]').val(td.eq(0).data('id')).trigger(
                    'change');
                $('#kt_modal_update_document .modal-body select[name="document_type"]').val(td.eq(1).data('id'))
                    .trigger('change');
                $('#kt_modal_update_document .modal-body textarea[name="note"]').val(td.eq(3).text());
                $('#kt_modal_update_document .modal-body select[name="status"]').val(td.eq(3).data('status-id'))
                    .trigger('change');
                // console.log(td);
            });

            // Define form element
            const updateForm = document.getElementById('kt_modal_update_document_form');
            updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_document'));

            var updateValidator = FormValidation.formValidation(
                updateForm, {
                    fields: {
                        'driver_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Driver is required'
                                }
                            }
                        },
                        'document_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Document Type is required'
                                }
                            }
                        },
                        'image': {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: 3145728, // 3 * 1024 * 1024 (3 MB in bytes)
                                    message: 'The selected file is not valid',
                                },
                            }
                        },
                        'status': {
                            validators: {
                                notEmpty: {
                                    message: 'Status is required'
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
            const submitUpdateButton = document.getElementById('kt_modal_update_document_form_submit');
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

                            let formData = new FormData(updateForm);

                            $.ajax({
                                url: updateUrl,
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: formData,
                                processData: false,
                                contentType: false,
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
                                                text: "{{ __('lang.vNotUpdatedSuccess') }}",
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
                                                // $('#kt_modules_table').DataTable().draw()
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
                                                text: "@lang('lang.vUpdatedSuccess')",
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
                                                // $('#kt_documents_table').DataTable().draw()
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
                                        text: "Document not updated!",
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
            const changeStatusButtons = document.querySelectorAll('[data-documents-table-filter="change_status_row"]');

            changeStatusButtons.forEach(d => {
                // Delete button on click
                d.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log(d.checked);

                    // Get url
                    const url = $(d).data('url');

                    // Select parent row
                    const parent = e.target.closest('tr');

                    // Get package name
                    // const documentName = parent.querySelectorAll('td')[0].innerText;
                    const documentName = "Document";

                    if (d.checked) {

                        Swal.fire({
                            text: "{{ __('lang.areSure') }} {{ __('lang.activate') }} " +
                                documentName + "?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "{{ __('lang.yes') }}, {{ __('lang.activate') }}!",
                            cancelButtonText: "{{ __('lang.no') }}, {{ __('lang.cancel') }}",
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                Swal.fire({
                                    text: "Activating " + documentName,
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
                                                    text: documentName +
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
                                                        documentName + "!.",
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
                                                text: documentName +
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
                                    text: documentName + " was not activated.",
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
                                documentName + "?",
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
                                    text: "Deactivating " + documentName,
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
                                                    text: documentName +
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
                                                        documentName + "!.",
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
                                                text: documentName +
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
                                    text: documentName + " was not deactivated.",
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