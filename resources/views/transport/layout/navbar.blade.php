<div id="kt_app_header" class="app-header d-flex flex-column flex-stack">
    <!--begin::Header main-->
    <div class="d-flex flex-stack flex-grow-1">
        <div class="app-header-logo d-flex align-items-center ps-lg-12" id="kt_app_header_logo">
            <!--begin::Sidebar toggle-->
            <div id="kt_app_sidebar_toggle"
                class="app-sidebar-toggle btn btn-sm btn-icon bg-body btn-color-gray-500 btn-active-color-primary w-30px h-30px ms-n2 me-4 d-none d-lg-flex"
                data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                data-kt-toggle-name="app-sidebar-minimize">
                <i class="ki-outline ki-abstract-14 fs-3 mt-1"></i>
            </div>
            <!--end::Sidebar toggle-->
            <!--begin::Sidebar mobile toggle-->
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px ms-3 me-2 d-flex d-lg-none"
                id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i>
            </div>
            <!--end::Sidebar mobile toggle-->
            <!--begin::Logo-->

            <a href="{{ route('transport.dashboard', [request()->lang]) }}" class="app-sidebar-logo">
                <img alt="Logo" src="{{ url('assets/media/logos/demo39.svg') }}" class="h-25px theme-light-show" />
                <img alt="Logo" src="{{ url('assets/media/logos/demo39-dark.svg') }}"
                    class="h-25px theme-dark-show" />
            </a>
            <!--end::Logo-->
        </div>
        <!--begin::Navbar-->
        <div class="app-navbar flex-grow-1 justify-content-end" id="kt_app_header_navbar">
            <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
                <!--begin::Search-->
                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-350px"
                    data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter"
                    data-kt-search-layout="menu" data-kt-search-responsive="true" data-kt-menu-trigger="auto"
                    data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                    <!--begin::Tablet and mobile search toggle-->
                    <div data-kt-search-element="toggle"
                        class="search-toggle-mobile d-flex d-lg-none align-items-center">
                        <div class="d-flex">
                            <i class="ki-outline ki-magnifier fs-1 fs-1"></i>
                        </div>
                    </div>
                    <!--end::Tablet and mobile search toggle-->
                    <!--begin::Form(use d-none d-lg-block classes for responsive search)-->
                    <form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0"
                        autocomplete="off">
                        <!--begin::Hidden input(Added to disable form autocomplete)-->
                        <input type="hidden" />
                        <!--end::Hidden input-->
                        <!--begin::Icon-->
                        <i
                            class="ki-outline ki-magnifier search-icon fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-5"></i>
                        <!--end::Icon-->
                        <!--begin::Input-->
                        <input type="text" class="search-input form-control form-control border h-lg-45px ps-13"
                            name="search" value="" placeholder="Search..." data-kt-search-element="input" />
                        <!--end::Input-->
                        <!--begin::Spinner-->
                        <span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5"
                            data-kt-search-element="spinner">
                            <span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
                        </span>
                        <!--end::Spinner-->
                        <!--begin::Reset-->
                        <span
                            class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4"
                            data-kt-search-element="clear">
                            <i class="ki-outline ki-cross fs-2 fs-lg-1 me-0"></i>
                        </span>
                        <!--end::Reset-->
                    </form>
                    <!--end::Form-->
                </div>
                @auth
                    @if (session()->has('ut') && session('ut') == 1)
                        <div class="warning-msg d-flex align-items-center">
                            <!-- Warning Icon -->
                            <div class="warning-icon me-2 ms-4">
                                <i class="fas fa-exclamation-triangle" data-bs-toggle="popover"
                                    data-bs-custom-class="d-sm-none"
                                    data-bs-template='<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body text-danger"></div></div>'
                                    data-bs-content="You have shadow logged in {{ auth()->user()->first_name }}
                                {{ auth()->user()->last_name }}'s white label
                                account."
                                    style="color: red; font-size: 18px;"></i>
                            </div>
                            <!-- Warning Message -->
                            <div class="warning-message d-sm-block d-none" style="font-size: 13px; color: red;">
                                You have shadow logged in {{ auth()->user()->first_name }}
                                {{ auth()->user()->last_name }}'s white label
                                account.
                            </div>
                            <div class="app-navbar-item ms-2">
                                <!--begin::Link-->
                                <a href="{{ route('tr.user.change', ['SA', 4]) }}"
                                    class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
                                    <i class="ki-outline ki-exit-right fs-1"></i>
                                </a>
                                <!--end::Link-->
                            </div>
                        </div>
                    @endif
                @endauth
                <!--end::Search-->
            </div>
            <!--begin::User menu-->
            <div class="app-navbar-item ms-2 ms-lg-6 me-lg-13" id="kt_header_user_menu_toggle">
                <!--begin::Menu wrapper-->
                <div class="cursor-pointer symbol symbol-circle symbol-30px symbol-lg-45px"
                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    @if (session('avatar'))
                        <img src="{{ asset('white/' . session('avatar')) }}" alt="user" />
                    @else
                        <img src="{{ asset('assets/img/profile.jpg') }}" alt="user" />
                    @endif
                </div>

                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                @if (session('avatar'))
                                    <img src="{{ asset('white/' . session('avatar')) }}" alt="user" />
                                @else
                                    <img src="{{ asset('assets/img/profile.jpg') }}" alt="user" />
                                @endif
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    {{ session('first') }} {{ session('last') }}
                                </div>
                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7"
                                    style="overflow-wrap:anywhere">{{ session('email') }}</a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ route('white-label.profile.index') }}" class="menu-link px-5">My Profile</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ route('white-label.profile.edit') }}" class="menu-link px-5">Edit Profile</a>
                    </div>
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">@lang('lang.lang')
                                @foreach ($lang as $langu)
                                    @if (Request()->lang == $langu->Short_name)
                                        <span
                                            class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">{{$langu->language_name}}
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="{{asset('logo/' . $langu->logo)}}" alt="{{$langu->language_name}}" />
                                    @endif
                                @endforeach
                            </span>
                            </span>
                        </a>
                        <!--begin::Menu sub-->
                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                            <!--begin::Menu item-->
                            @foreach ($lang as $langu)
                                <div class="menu-item px-3">
                                    <a href="{{ route('transport.dashboard', [$langu->Short_name]) }}"
                                        class="menu-link d-flex px-5 {{ Request()->lang == $langu->Short_name ? 'active' : '' }}">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="{{ asset('logo/' . $langu->logo) }}"
                                                alt="{{ $langu->language_name }}" />
                                        </span>{{ $langu->language_name }}</a>
                                </div>
                            @endforeach
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu sub-->
                    </div>
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">Mode
                                <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                    <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                                    <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                                </span></span>
                        </a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                            data-kt-menu="true" data-kt-element="theme-mode-menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                    data-kt-value="light">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-night-day fs-2"></i>
                                    </span>
                                    <span class="menu-title">Light</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                    data-kt-value="dark">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-moon fs-2"></i>
                                    </span>
                                    <span class="menu-title">Dark</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                    data-kt-value="system">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-screen fs-2"></i>
                                    </span>
                                    <span class="menu-title">System</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ url('logout') }}" class="menu-link px-5">Sign Out</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
                <!--end::Menu wrapper-->
            </div>
            <!--end::User menu-->
            <!--begin::Action-->
            <div class="app-navbar-item ms-2 ms-lg-6 me-lg-6">
                <!--begin::Link-->

                <!--end::Link-->
            </div>
            <!--end::Action-->
            <!--begin::Header menu toggle-->
            <div class="app-navbar-item ms-2 ms-lg-6 ms-n2 me-3 d-flex d-lg-none">
                <div class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-primary w-35px h-35px w-md-40px h-md-40px"
                    id="kt_app_aside_mobile_toggle">
                    <i class="ki-outline ki-burger-menu-2 fs-2"></i>
                </div>
            </div>
            <!--end::Header menu toggle-->
        </div>
        <!--end::Navbar-->
    </div>
    <!--end::Header main-->
    <!--begin::Separator-->
    <div class="app-header-separator"></div>
    <!--end::Separator-->
</div>
