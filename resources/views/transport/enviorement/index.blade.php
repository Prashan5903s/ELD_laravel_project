@extends('transport.layout.index')
@section('main-transport-container')
    <!--end::Theme mode setup on page load-->
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
                                            @lang('lang.enviorement')
                                        </h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">@lang('lang.home')</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('overview.enviorement.data', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.enviorement')
                                                </a>
                                            </li>
                                            <!--begin::Item-->
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
                                <!--begin::Card-->
                                <div class="container-fluid mt-4">
                                    <div class="row">
                                        <!-- Loop starts here -->
                                        @if ($obdCoolant != 'Data not received')
                                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                                <div class="card custom-box-height">
                                                    <div class="card-body">
                                                        <div style="position: relative;">
                                                            <img src="{{ asset('logo/truck-logo.png') }}" alt="Truck Logo">
                                                            <div class="temperature">{{ $obdCoolant }}&deg;F</div>
                                                            <!-- Replace with dynamic temperature -->
                                                        </div>
                                                        <div class="location">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <!-- Replace with the appropriate icon class if needed -->
                                                            <span>{{ $locations[0]['name'] }}</span>
                                                            <!-- Replace with dynamic location name -->
                                                        </div>

                                                    </div>
                                                </div>
                                                <!-- Repeat as many boxes as needed -->
                                            </div>
                                    </div>
                                @else
                                    <div class="box">
                                        <img src="{{ asset('logo/stop-truck.png') }}" alt="Vehicle is not moving!">
                                        <span class="text">Vehicle is not moving!</span>
                                    </div>
                                    @endif
                                    <!--end::Card-->
                                </div>
                                <!--end::Content container-->
                            </div>
                            <style>
                                .box {
                                    padding: 20px;
                                    max-width: 300px;
                                    height: auto;
                                    background-color: white;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
                                    font-size: 18px;
                                    font-family: Arial, sans-serif;
                                    display: flex;
                                    flex-direction: column;
                                    justify-content: center;
                                    align-items: center;
                                    text-align: center;
                                }

                                .box img {
                                    max-width: 100%;
                                    height: auto;
                                    margin-bottom: 10px;
                                }

                                .text {
                                    max-width: 100%;
                                    margin-bottom: 10px;
                                    /* Added margin-bottom for padding */
                                }

                                .custom-box-height {
                                    height: 600px;
                                    /* Set your desired box height here */
                                }

                                .card-body {
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                }

                                .card-body img {
                                    max-height: 300px;
                                    /* Set your desired image height here */
                                    max-width: 800px;
                                    /* Set your desired image width here */
                                    display: block;
                                    margin: 0 auto;
                                    position: relative;
                                }

                                .temperature {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    font-size: 16px;
                                    /* font-weight: bold; */
                                    color: rgb(3, 3, 3);
                                    margin-left: -6px;
                                    /* Adjust the color as needed */
                                }

                                .location {
                                    display: flex;
                                    align-items: baseline;
                                    margin-top: 10px;
                                    /* Adjust the margin as needed */
                                    padding-left: 10px;
                                    /* Adjust the padding as needed */
                                }

                                .location i {
                                    margin-right: 10px;
                                    color: green;
                                    /* Adjust the spacing as needed */
                                }
                            </style>
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
    @endsection
    @section('footer-script-link')
        <script src="{{ asset('assets/js/custom/apps/superAdmin/tr/report/list.js') }}"></script>
    @endsection
