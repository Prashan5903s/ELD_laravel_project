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

                <div data-kt-menu-trigger="click"
                    class="menu-item  menu-accordion <?php echo e(in_array(Route::currentRouteName(), ['white-label.index']) ? 'show' : ''); ?>">

                    <!--begin:Menu link-->

                    <span class="menu-link">

                        <span class="menu-icon">

                            <i class="ki-outline ki-home-2 fs-2"></i>

                        </span>

                        <span class="menu-title">Admin Dashboard</span>

                        <span class="menu-arrow"></span>

                    </span>

                    <!--end:Menu link-->

                    <!--begin:Menu sub-->

                    <div class="menu-sub menu-sub-accordion">

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'white-label.index' ? 'active' : ''); ?>"
                                href="<?php echo e(url('white-label')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">White Label Company</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                    </div>

                </div>

                <!--end:Menu item-->



                <!--begin:Menu item-->

                <div data-kt-menu-trigger="click"
                    class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['roles.index', 'permissions.index', 'modules.index', 'user.view']) ? 'show' : ''); ?> menu-accordion">

                    <!--begin:Menu link-->

                    <span class="menu-link">

                        <span class="menu-icon">

                            <i class="ki-outline ki-abstract-26 fs-2"></i>

                        </span>

                        <span class="menu-title">User Management</span>

                        <span class="menu-arrow"></span>

                    </span>

                    <!--end:Menu link-->

                    <!--begin:Menu sub-->

                    <div class="menu-sub menu-sub-accordion">

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'user.view' ? 'active' : ''); ?>"
                                href="<?php echo e(route('user.view')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">User list</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                        <!--begin:Menu item-->

                        <div data-kt-menu-trigger="click"
                            class="menu-item <?php echo e(Route::currentRouteName() == 'roles.index' ? 'show' : ''); ?> menu-accordion">

                            <!--begin:Menu link-->

                            <span class="menu-link">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Roles</span>

                                <span class="menu-arrow"></span>

                            </span>

                            <!--end:Menu link-->

                            <!--begin:Menu sub-->

                            <div class="menu-sub menu-sub-accordion">

                                <!--begin:Menu item-->

                                <div class="menu-item">

                                    <!--begin:Menu link-->

                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'roles.index' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('roles.index')); ?>">

                                        <span class="menu-bullet">

                                            <span class="bullet bullet-dot"></span>

                                        </span>

                                        <span class="menu-title">Roles List</span>

                                    </a>

                                    <!--end:Menu link-->

                                </div>

                                <!--end:Menu item-->

                                

                            </div>

                            <!--end:Menu sub-->

                        </div>

                        <!--end:Menu item-->

                        <!--begin:Menu item-->

                        <div data-kt-menu-trigger="click"
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['permissions.index', 'modules.index']) ? 'show' : ''); ?> menu-accordion">

                            <!--begin:Menu link-->

                            <span class="menu-link">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Permissions</span>

                                <span class="menu-arrow"></span>

                            </span>

                            <!--end:Menu link-->

                            <!--begin:Menu sub-->

                            <div class="menu-sub menu-sub-accordion">

                                <!--begin:Menu item-->

                                <div class="menu-item">

                                    <!--begin:Menu link-->

                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'permissions.index' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('permissions.index')); ?>">

                                        <span class="menu-bullet">

                                            <span class="bullet bullet-dot"></span>

                                        </span>

                                        <span class="menu-title">Permissions List</span>

                                    </a>

                                    <!--end:Menu link-->

                                </div>

                                <!--end:Menu item-->

                                <!--begin:Menu item-->

                                <div class="menu-item">

                                    <!--begin:Menu link-->

                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'modules.index' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('modules.index')); ?>">

                                        <span class="menu-bullet">

                                            <span class="bullet bullet-dot"></span>

                                        </span>

                                        <span class="menu-title">Modules</span>

                                    </a>

                                    <!--end:Menu link-->

                                </div>

                                <!--end:Menu item-->

                            </div>

                            <!--end:Menu sub-->

                        </div>

                        <!--end:Menu item-->

                    </div>

                    <!--end:Menu sub-->

                </div>

                <!--end:Menu item-->



                <!--begin:Menu item-->

                <div data-kt-menu-trigger="click"
                    class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['packages.index', 'packages.create', 'package.assign.index']) ? 'show' : ''); ?> menu-accordion">

                    <!--begin:Menu link-->

                    <span class="menu-link">

                        <span class="menu-icon">

                            <i class="ki-outline ki-abstract-35 fs-2"></i>

                        </span>

                        <span class="menu-title">Packages</span>

                        <span class="menu-arrow"></span>

                    </span>

                    <!--end:Menu link-->

                    <!--begin:Menu sub-->

                    <div class="menu-sub menu-sub-accordion">

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'packages.index' ? 'active' : ''); ?>"
                                href="<?php echo e(route('packages.index')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Packages List</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'packages.create' ? 'active' : ''); ?>"
                                href="<?php echo e(route('packages.create')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Add Package</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'package.assign.index' ? 'active' : ''); ?>"
                                href="<?php echo e(route('package.assign.index')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Package assign</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                    </div>

                    <!--end:Menu sub-->

                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['hardware.device.name', 'device.assign.index', 'device.admin.data.index']) ? 'show' : ''); ?> menu-accordion">

                    <!--begin:Menu link-->

                    <span class="menu-link">

                        <span class="menu-icon">

                            <i class="bi bi-motherboard"></i>

                        </span>

                        <span class="menu-title">Hardware</span>

                        <span class="menu-arrow"></span>

                    </span>

                    <!--end:Menu link-->

                    <!--begin:Menu sub-->

                    <div class="menu-sub menu-sub-accordion">

                        <!--begin:Menu item-->

                        <?php $__currentLoopData = $hardware; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="menu-item">

                                <!--begin:Menu link-->

                                <a class="menu-link <?php echo e(request()->route('device') == $value->hardware_name ? 'active' : ''); ?>"
                                    href="<?php echo e(route('hardware.device.name', [$value->hardware_name])); ?>">

                                    <span class="menu-bullet">

                                        <span class="bullet bullet-dot"></span>

                                    </span>

                                    <span class="menu-title"><?php echo e($value->hardware_name); ?></span>

                                </a>

                                <!--end:Menu link-->

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div data-kt-menu-trigger="click"
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['device.assign.index', 'device.admin.data.index']) ? 'show' : ''); ?> menu-accordion">

                            <!--begin:Menu link-->

                            <span class="menu-link">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Device</span>

                                <span class="menu-arrow"></span>

                            </span>

                            <!--end:Menu link-->

                            <!--begin:Menu sub-->

                            <div class="menu-sub menu-sub-accordion">

                                <!--begin:Menu item-->

                                <div class="menu-item">

                                    <!--begin:Menu link-->

                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'device.admin.data.index' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('device.admin.data.index')); ?>">

                                        <span class="menu-bullet">

                                            <span class="bullet bullet-dot"></span>

                                        </span>

                                        <span class="menu-title">Device</span>

                                    </a>

                                    <!--end:Menu link-->

                                </div>

                                <div class="menu-item">

                                    <!--begin:Menu link-->

                                    <a class="menu-link <?php echo e(Route::currentRouteName() == 'device.assign.index' ? 'active' : ''); ?>"
                                        href="<?php echo e(route('device.assign.index')); ?>">

                                        <span class="menu-bullet">

                                            <span class="bullet bullet-dot"></span>

                                        </span>

                                        <span class="menu-title">Device Assign</span>

                                    </a>

                                    <!--end:Menu link-->

                                </div>

                                <!--end:Menu item-->

                                

                            </div>

                            <!--end:Menu sub-->

                        </div>

                        <!--end:Menu item-->

                    </div>

                    <!--end:Menu sub-->

                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['language.index', 'currency.index', 'admin.software.version.index']) ? 'show' : ''); ?> menu-accordion">

                    <!--begin:Menu link-->

                    <span class="menu-link">

                        <span class="menu-icon">

                            <i class="bi bi-gear fs-2"></i>

                        </span>

                        <span class="menu-title">Settings</span>

                        <span class="menu-arrow"></span>

                    </span>

                    <!--end:Menu link-->

                    <!--begin:Menu sub-->

                    <div class="menu-sub menu-sub-accordion">

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'language.index' ? 'active' : ''); ?>"
                                href="<?php echo e(url('language')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Language</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                        <!--begin:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'currency.index' ? 'active' : ''); ?>"
                                href="<?php echo e(route('currency.index')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Currencies</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                        <!--end:Menu item-->

                        <div class="menu-item">

                            <!--begin:Menu link-->

                            <a class="menu-link <?php echo e(Route::currentRouteName() == 'admin.software.version.index' ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.software.version.index')); ?>">

                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>

                                </span>

                                <span class="menu-title">Software version</span>

                            </a>

                            <!--end:Menu link-->

                        </div>

                    </div>

                    <!--end:Menu sub-->

                </div>

                <!--end:Menu item-->

            </div>

        </div>

    </div>

    <!--end::Wrapper-->

</div>
<?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/super-admin/layout/left-slidebar.blade.php ENDPATH**/ ?>