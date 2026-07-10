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
                <?php if(isset($permissions)): ?>


                    <?php if(
                        $permissions->contains(3) ||
                            $permissions->contains(6) ||
                            $permissions->contains(12) ||
                            $permissions->contains(15) ||
                            $permissions->contains(16)): ?>
                        <div data-kt-menu-trigger="click"
                            class="menu-item menu-accordion <?php echo e(in_array(Route::currentRouteName(), ['addresses.index', 'vehicles.index', 'driver.index', 'overview.enviorement.data', 'view.overview.map', 'driver.auth.index']) ? 'show' : ''); ?>">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="bi bi-steam fs-2"></i>
                                </span>
                                <span class="menu-title"><?php echo app('translator')->get('lang.overview'); ?></span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <?php if(session()->has('comp_user_change')): ?>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link <?php echo e(Route::currentRouteName() == 'change.tr.user' ? 'active' : ''); ?>"
                                            href="<?php echo e(route('change.tr.user', [Auth::user()->master_id])); ?>">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Switch users</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                <?php endif; ?>
                                <?php if($permissions->contains(3) || $permissions->contains(6)): ?>
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['vehicles.index', 'addresses.index']) ? 'show' : ''); ?> menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo app('translator')->get('lang.asset'); ?></span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            <?php if($permissions->contains(3)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'vehicles.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'vehicles.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(isset(request()->lang) ? route('vehicles.index', request()->lang) : route('vehicles.index', 'en')); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.vehicles'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            <?php if($permissions->contains(6)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'addresses.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'addresses.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(isset(request()->lang) ? route('addresses.index', request()->lang) : route('addresses.index', 'en')); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.locations'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                <?php endif; ?>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <?php if($permissions->contains(12)): ?>
                                    <div class="menu-item">
                                        <!-- Begin: Menu link -->
                                        <a class="menu-link <?php echo e(request()->routeIs('driver.auth.index') ? 'active' : ''); ?>"
                                            href="<?php echo e(route('driver.auth.index', ['lang' => request()->lang])); ?>">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo e(__('lang.driver')); ?></span>
                                        </a>
                                        <!-- End: Menu link -->
                                    </div>
                                <?php endif; ?>

                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <?php if($permissions->contains(15)): ?>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link <?php echo e(Route::currentRouteName() == 'overview.enviorement.data' ? 'active' : ''); ?>"
                                            href="<?php echo e(route('overview.enviorement.data', [request()->lang])); ?>">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo app('translator')->get('lang.enviorement'); ?></span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                <?php endif; ?>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <?php if($permissions->contains(16)): ?>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link <?php echo e(Route::currentRouteName() == 'view.overview.map' ? 'active' : ''); ?>"
                                            href="<?php echo e(route('view.overview.map', [request()->lang])); ?>">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo app('translator')->get('lang.coverage'); ?></span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                <?php endif; ?>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <!--end:Menu item-->
                
                <?php if(isset($permissions) && $permissions->contains(17)): ?>
                    <div data-kt-menu-trigger="click"
                        class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['driver.compliance']) ? 'show' : ''); ?> menu-accordion">
                        <!--begin:Menu link-->
                        <a class="menu-link <?php echo e(in_array(Route::currentRouteName(), ['driver.compliance']) ? 'active' : ''); ?>"
                            href="<?php echo e(route('driver.compliance', [request()->lang])); ?>">
                            
                            <span class="menu-icon">
                                <i class="bi bi-person-badge-fill fs-2"></i>
                            </span>
                            <span class="menu-title"><?php echo app('translator')->get('lang.compliance'); ?></span>
                            
                        </a>
                        <!--end:Menu link-->
                    </div>
                <?php endif; ?>
                
                
                <?php if(isset($permissions) && $permissions->contains(9)): ?>
                    <div data-kt-menu-trigger="click"
                        class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['document.index', 'document.create']) ? 'show' : ''); ?> menu-accordion">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="bi bi-sticky-fill fs-2"></i>
                            </span>
                            <span class="menu-title"><?php echo app('translator')->get('lang.document'); ?></span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->

                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo e(Route::currentRouteName() == 'document.index' ? 'active' : ''); ?>"
                                    href="<?php echo e(isset(request()->lang) ? route('document.index', request()->lang) : route('document.index', 'en')); ?>">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"><?php echo app('translator')->get('lang.list'); ?></span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                <?php endif; ?>
                <?php if(isset($permissions) && ($permissions->contains(18) || $permissions->contains(19))): ?>
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion <?php echo e(in_array(Route::currentRouteName(), ['driver.report.data', 'driver.report.vechile']) ? 'show' : ''); ?>">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="bi bi-clipboard-data fs-2"></i>
                            </span>
                            <span class="menu-title"><?php echo app('translator')->get('lang.report'); ?></span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <?php if(isset($permissions) && $permissions->contains(18)): ?>
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'driver.report.data' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('driver.report.data', [request()->lang])); ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title"><?php echo app('translator')->get('lang.dlog'); ?></span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                            <?php endif; ?>
                            <?php if(isset($permissions) && $permissions->contains(19)): ?>
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'driver.report.vechile' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('driver.report.vechile', [request()->lang])); ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title"><?php echo app('translator')->get('lang.vechile'); ?></span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(isset($permissions)): ?>
                    <!--begin:Menu item-->
                    <?php if(
                        $permissions->contains(23) ||
                            $permissions->contains(26) ||
                            $permissions->contains(29) ||
                            $permissions->contains(32) ||
                            $permissions->contains(35)): ?>


                        <div data-kt-menu-trigger="click"
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['organization.general.index', 'setting.driver.organisation', 'setting.device.index', 'settings.organisation.userRoles.index', 'driver.activity.index']) ? 'show' : ''); ?> menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="bi bi-gear fs-2"></i>
                                </span>
                                <span class="menu-title"><?php echo app('translator')->get('lang.setting'); ?></span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <?php if(
                                    $permissions->contains(23) ||
                                        $permissions->contains(26) ||
                                        $permissions->contains(29) ||
                                        $permissions->contains(32)): ?>
                                    <!--begin:Menu item-->
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['organization.general.index', 'setting.driver.organisation', 'settings.organisation.userRoles.index', 'driver.activity.index']) ? 'show' : ''); ?> menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo app('translator')->get('lang.organi'); ?></span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            <?php if($permissions->contains(23)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'organization.general.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'organization.general.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(isset(request()->lang) ? route('organization.general.index', request()->lang) : route('organization.general.index', 'en')); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">General</span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            <?php if($permissions->contains(26)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'settings.organisation.userRoles.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'settings.organisation.userRoles.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(route('settings.organisation.userRoles.index', [request()->lang])); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.user'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            <?php if($permissions->contains(29)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'setting.driver.organisation' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'setting.driver.organisation' ? 'active' : ''); ?>"
                                                        href="<?php echo e(route('setting.driver.organisation', [request()->lang])); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.driver'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if($permissions->contains(32)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'driver.activity.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'driver.activity.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(route('driver.activity.index', [request()->lang])); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.dactivity'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                <?php endif; ?>
                                <!--end:Menu item-->
                                <?php if($permissions->contains(35)): ?>
                                    <div data-kt-menu-trigger="click"
                                        class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['setting.device.index']) ? 'show' : ''); ?> menu-accordion">
                                        <!--begin:Menu link-->
                                        <span class="menu-link">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title"><?php echo app('translator')->get('lang.device'); ?></span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div class="menu-sub menu-sub-accordion">
                                            <!--begin:Menu item-->
                                            <?php if($permissions->contains(35)): ?>
                                                <div data-kt-menu-trigger="click"
                                                    class="menu-item <?php echo e(Route::currentRouteName() == 'setting.device.index' ? 'show' : ''); ?> menu-accordion">
                                                    <!--begin:Menu link-->
                                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'setting.device.index' ? 'active' : ''); ?>"
                                                        href="<?php echo e(route('setting.device.index', [request()->lang])); ?>">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title"><?php echo app('translator')->get('lang.device'); ?></span>
                                                    </a>
                                                    <!--end:Menu link-->
                                                </div>
                                            <?php endif; ?>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                <?php endif; ?>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                
                                <!--end:Menu item-->
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!--end::Sidebar menu-->
        </div>
    </div>
    <!--end::Wrapper-->
</div>
<?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/transport/layout/left-slidebar.blade.php ENDPATH**/ ?>