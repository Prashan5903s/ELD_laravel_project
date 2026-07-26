<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Wrapper-->
    <div id="kt_app_sidebar_wrapper" class="app-sidebar-wrapper">
        <div class="hover-scroll-y my-5 my-lg-2 mx-4" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper"
            data-kt-scroll-offset="5px">
            <!--begin::Sidebar menu-->
            <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
                class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary px-3 mb-5">
                <!--begin:Menu item-->
                @if (isset($permissions))


                    @if (
                        $permissions->contains(3) ||
                            $permissions->contains(6) ||
                            $permissions->contains(12) ||
                            $permissions->contains(15) ||
                            $permissions->contains(16))
                        <div data-kt-menu-trigger="click"
                            class="menu-item menu-accordion {{ in_array(Route::currentRouteName(), ['addresses.index', 'vehicles.index', 'driver.index', 'overview.enviorement.data', 'view.overview.map', 'driver.auth.index']) ? 'show' : '' }}">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="bi bi-steam fs-2"></i>
                                </span>
                                <span class="menu-title">@lang('lang.overview')</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                @if (session()->has('comp_user_change'))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::currentRouteName() == 'change.tr.user' ? 'active' : '' }}"
                                            href="{{ route('change.tr.user', [Auth::user()->master_id]) }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Switch users</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                @endif
                                @if ($permissions->contains(3) || $permissions->contains(6))
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item {{ in_array(Route::currentRouteName(), ['vehicles.index', 'addresses.index']) ? 'show' : '' }} menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.asset')</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            @if ($permissions->contains(3))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'vehicles.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'vehicles.index' ? 'active' : '' }}"
                                                        href="{{ isset(request()->lang) ? route('vehicles.index', request()->lang) : route('vehicles.index', 'en') }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.vehicles')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            @if ($permissions->contains(6))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'addresses.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'addresses.index' ? 'active' : '' }}"
                                                        href="{{ isset(request()->lang) ? route('addresses.index', request()->lang) : route('addresses.index', 'en') }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.locations')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                @if ($permissions->contains(12))
                                    <div class="menu-item">
                                        <!-- Begin: Menu link -->
                                        <a class="menu-link {{ request()->routeIs('driver.auth.index') ? 'active' : '' }}"
                                            href="{{ route('driver.auth.index', ['lang' => request()->lang]) }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">{{ __('lang.driver') }}</span>
                                        </a>
                                        <!-- End: Menu link -->
                                    </div>
                                @endif

                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                @if ($permissions->contains(15))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::currentRouteName() == 'overview.enviorement.data' ? 'active' : '' }}"
                                            href="{{ route('overview.enviorement.data', [request()->lang]) }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.enviorement')</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                @if ($permissions->contains(16))
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{ Route::currentRouteName() == 'view.overview.map' ? 'active' : '' }}"
                                            href="{{ route('view.overview.map', [request()->lang]) }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.coverage')</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                {{-- <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link" href="#">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('lang.proximity')</span>
                            </a>
                            <!--end:Menu link-->
                        </div> --}}
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                    @endif
                @endif
                <!--end:Menu item-->
                {{-- <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(Route::currentRouteName(), ['roles.index', 'permissions.index', 'modules.index']) ? 'show' : '' }} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-shield-fill-check fs-2"></i>
                        </span>
                        <span class="menu-title">@lang('lang.safety')</span>
                    </span>
                    <!--end:Menu link-->
                </div> --}}
                @if (isset($permissions) && $permissions->contains(17))
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(Route::currentRouteName(), ['driver.compliance']) ? 'show' : '' }} menu-accordion">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ in_array(Route::currentRouteName(), ['driver.compliance']) ? 'active' : '' }}"
                            href="{{ route('driver.compliance', [request()->lang]) }}">
                            {{-- <span class="menu-link"> --}}
                            <span class="menu-icon">
                                <i class="bi bi-person-badge-fill fs-2"></i>
                            </span>
                            <span class="menu-title">@lang('lang.compliance')</span>
                            {{-- </span> --}}
                        </a>
                        <!--end:Menu link-->
                    </div>
                @endif
                {{-- <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(Route::currentRouteName(), ['roles.index', 'permissions.index', 'modules.index']) ? 'show' : '' }} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-wrench fs-2"></i>
                        </span>
                        <span class="menu-title">@lang('lang.maintenance')</span>
                    </span>
                    <!--end:Menu link-->
                </div> --}}
                {{-- <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(Route::currentRouteName(), ['roles.index', 'permissions.index', 'modules.index']) ? 'show' : '' }} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-battery-full fs-2"></i>

                        </span>
                        <span class="menu-title">@lang('lang.fuel')</span>
                    </span>
                    <!--end:Menu link-->
                </div> --}}
                @if (isset($permissions) && $permissions->contains(9))
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(Route::currentRouteName(), ['document.index', 'document.create']) ? 'show' : '' }} menu-accordion">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="bi bi-sticky-fill fs-2"></i>
                            </span>
                            <span class="menu-title">@lang('lang.document')</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->

                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ Route::currentRouteName() == 'document.index' ? 'active' : '' }}"
                                    href="{{ isset(request()->lang) ? route('document.index', request()->lang) : route('document.index', 'en') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">@lang('lang.list')</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                @endif
                @if (isset($permissions) && ($permissions->contains(18) || $permissions->contains(19)))
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ in_array(Route::currentRouteName(), ['driver.report.data', 'driver.report.vechile']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="bi bi-clipboard-data fs-2"></i>
                            </span>
                            <span class="menu-title">@lang('lang.report')</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            @if (isset($permissions) && $permissions->contains(18))
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ Route::currentRouteName() == 'driver.report.data' ? 'active' : '' }}"
                                        href="{{ route('driver.report.data', [request()->lang]) }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.dlog')</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                            @endif
                            @if (isset($permissions) && $permissions->contains(19))
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{ Route::currentRouteName() == 'driver.report.vechile' ? 'active' : '' }}"
                                        href="{{ route('driver.report.vechile', [request()->lang]) }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.vechile')</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @isset($permissions)
                    <!--begin:Menu item-->
                    @if (
                        $permissions->contains(23) ||
                            $permissions->contains(26) ||
                            $permissions->contains(29) ||
                            $permissions->contains(32) ||
                            $permissions->contains(35))


                        <div data-kt-menu-trigger="click"
                            class="menu-item {{ in_array(Route::currentRouteName(), ['organization.general.index', 'setting.driver.organisation', 'setting.device.index', 'settings.organisation.userRoles.index', 'driver.activity.index']) ? 'show' : '' }} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="bi bi-gear fs-2"></i>
                                </span>
                                <span class="menu-title">@lang('lang.setting')</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                @if (
                                    $permissions->contains(23) ||
                                        $permissions->contains(26) ||
                                        $permissions->contains(29) ||
                                        $permissions->contains(32))
                                    <!--begin:Menu item-->
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item {{ in_array(Route::currentRouteName(), ['organization.general.index', 'setting.driver.organisation', 'settings.organisation.userRoles.index', 'driver.activity.index']) ? 'show' : '' }} menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.organi')</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            @if ($permissions->contains(23))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'organization.general.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'organization.general.index' ? 'active' : '' }}"
                                                        href="{{ isset(request()->lang) ? route('organization.general.index', request()->lang) : route('organization.general.index', 'en') }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">General</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            @if ($permissions->contains(26))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'settings.organisation.userRoles.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'settings.organisation.userRoles.index' ? 'active' : '' }}"
                                                        href="{{ route('settings.organisation.userRoles.index', [request()->lang]) }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.user')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            @if ($permissions->contains(29))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'setting.driver.organisation' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'setting.driver.organisation' ? 'active' : '' }}"
                                                        href="{{ route('setting.driver.organisation', [request()->lang]) }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.driver')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            {{-- <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == 'roles.index' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.tag')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div> --}}
                                            {{-- <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == 'roles.index' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.feature')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div> --}}
                                            @if ($permissions->contains(32))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'driver.activity.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'driver.activity.index' ? 'active' : '' }}"
                                                        href="{{ route('driver.activity.index', [request()->lang]) }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.dactivity')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            {{-- <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == 'roles.index' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.data')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div> --}}
                                            {{-- <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == 'roles.index' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.app')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div>
                                <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == 'roles.index' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.billing')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div> --}}
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                @endif
                                <!--end:Menu item-->
                                @if ($permissions->contains(35))
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item {{ in_array(Route::currentRouteName(), ['setting.device.index']) ? 'show' : '' }} menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.device')</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            @if ($permissions->contains(35))
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item {{ Route::currentRouteName() == 'setting.device.index' ? 'show' : '' }} menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link {{ Route::currentRouteName() == 'setting.device.index' ? 'active' : '' }}"
                                                        href="{{ route('setting.device.index', [request()->lang]) }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">@lang('lang.device')</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            @endif
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            {{-- <div data-kt-menu-trigger="click"
                                    class="menu-item {{ in_array(Route::currentRouteName(), ['permissions.index', 'modules.index']) ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.configu')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div> --}}
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                @endif
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                {{-- <div data-kt-menu-trigger="click"
                            class="menu-item {{ in_array(Route::currentRouteName(), ['']) ? 'show' : '' }} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('lang.fleet')</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == '' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.assign')</span>
                                    </span>
                                    <!--end:Menu link-->
                                </div>
                                <div data-kt-menu-trigger="click"
                                    class="menu-item {{ Route::currentRouteName() == '' ? 'show' : '' }} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">@lang('lang.app')</span>
                                    </span>
                                    <!--end:Menu link-->
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item {{ Route::currentRouteName() == '' ? 'show' : '' }} menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">@lang('lang.activity')</span>
                                        </span>
                                        <!--end:Menu link-->
                                    </div>
                                </div>
                                <!--end:Menu item-->

                            </div>
                            <!--end:Menu sub-->
                        </div> --}}
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                    @endif
                @endisset
            </div>
            <!--end::Sidebar menu-->
        </div>
    </div>
    <!--end::Wrapper-->
</div>
