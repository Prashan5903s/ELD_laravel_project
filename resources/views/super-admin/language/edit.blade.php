@extends('super-admin.layout.index')
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
                                            Edit Language</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ url('/') }}" class="text-muted text-hover-primary">Home</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('language.index') }}"
                                                    class="text-muted text-hover-primary">Language</a>
                                            </li>
                                            <!--end::Item-->
                                        </ul>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div id="kt_app_content" class="app-content flex-column-fluid">

                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <!--begin::Form-->
                                <form method="POST" action="{{ route('language.update', [$data->id]) }}"
                                    class="form d-flex flex-column flex-lg-row" enctype="multipart/form-data">
                                    @method('PATCH')
                                    @csrf
                                    <!--begin::Aside column-->
                                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                                        <!--begin::Thumbnail settings-->

                                        <!--begin::Status-->

                                    </div>
                                    <!--end::Aside column-->
                                    <!--begin::Main column-->
                                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                        <!--begin:::Tabs-->
                                        <ul
                                            class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                                            <!--begin:::Tab item-->
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                                                    href="#kt_ecommerce_add_product_general">Language</a>
                                            </li>
                                            <!--end:::Tab item-->
                                        </ul>
                                        <!--end:::Tabs-->
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
                                                                <label class="required form-label">Language name</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <input type="text" name="language_name"
                                                                    class="form-control mb-2"
                                                                    value="{{ $data->language_name }}"
                                                                    placeholder="Language name" />
                                                                @error('language_name')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Input-->

                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Short name</label>
                                                                <!--end::Label-->

                                                                <!--begin::Editor-->
                                                                <input type="text" name="short_name"
                                                                    value="{{ $data->Short_name }}"
                                                                    class="form-control mb-2" placeholder="Short name" />
                                                                @error('short_name')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Editor-->
                                                            </div>
                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label for="image"
                                                                    class="required form-label">Logo</label>
                                                                <!--end::Label-->
                                                                <div id="imagePreviewContainer" class="mb-3">
                                                                    <img id="imagePreview" height="80" width="90"
                                                                        src="{{ $data->logo ? asset('logo/' . $data->logo) : '#' }}"
                                                                        alt="Image Preview"
                                                                        style="{{ $data->logo ? 'display: block;' : 'display: none;' }} max-width: 100%; height: auto;" />
                                                                </div>
                                                                <!--begin::Input-->
                                                                <!--begin::Input-->
                                                                <input id="image" type="file" name="image"
                                                                    class="form-control mb-2"
                                                                    value="{{ asset('logo/'.$data->logo) }}" />
                                                                <!--end::Input-->

                                                                <!--end::Input-->

                                                                <!--begin::Description-->
                                                                @error('image')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                                <!--end::Description-->
                                                            </div>

                                                            <!-- Container to display the selected image -->
                                                            <script>
                                                                document.addEventListener("DOMContentLoaded", function() {
                                                                    var imageInput = document.getElementById('image');
                                                                    var imagePreview = document.getElementById('imagePreview');

                                                                    imageInput.addEventListener('change', function() {
                                                                        var file = this.files[0];
                                                                        if (file) {
                                                                            var reader = new FileReader();
                                                                            reader.onload = function(e) {
                                                                                imagePreview.src = e.target.result;
                                                                                imagePreview.style.display = 'block';
                                                                            }
                                                                            reader.readAsDataURL(file);
                                                                        } else {
                                                                            imagePreview.src = '#';
                                                                            imagePreview.style.display = 'none';
                                                                        }
                                                                    });
                                                                });
                                                            </script>

                                                            <div class="mb-10 fv-row">
                                                                <!--begin::Label-->
                                                                <label class="required form-label">Status</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <select name="is_active" class="form-control mb-2" required>
                                                                    <option value="" disabled>Select Status</option>
                                                                    <option value="1"
                                                                        {{ $data->is_active == 1 ? 'selected' : '' }}>
                                                                        Active
                                                                    </option>
                                                                    <option value="0"
                                                                        {{ $data->is_active == 0 ? 'selected' : '' }}>
                                                                        Inactive</option>
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
                                            <!--end::Tab pane-->                                            <!--end::Tab pane-->
                                        </div>
                                        <!--end::Tab content-->
                                        <div class="d-flex justify-content-end">
                                            <!--begin::Button-->
                                            <a href="{{ route('language.index') }}" id="kt_ecommerce_add_product_cancel"
                                                class="btn btn-light me-5">Cancel</a>
                                            <!--end::Button-->
                                            <!--begin::Button-->
                                            <button type="submit" id="kt_ecommerce_add_product_submit"
                                                class="btn btn-primary">
                                                <span class="indicator-label">Save Changes</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
            </div>
            <!--end::Card body-->

            <!--end::Card footer-->
        </div>
        <!--end::Messenger-->
    </div>

    <!--begin::Javascript-->
@endsection
