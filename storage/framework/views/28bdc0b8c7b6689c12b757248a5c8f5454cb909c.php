<?php $__env->startSection('main-transport-container'); ?>

<div class="d-flex flex-column flex-root app-root" id="kt_app_root">

    <!--begin::Page-->

    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

        <!--begin::Header-->

        <?php echo $__env->make('transport.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!--end::Header-->

        <!--begin::Wrapper-->

        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            <!--begin::Sidebar-->

            <?php echo $__env->make('transport.layout.left-slidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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

                                        <?php echo app('translator')->get('lang.vList'); ?></h1>

                                    <!--end::Title-->

                                    <!--begin::Breadcrumb-->

                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item text-muted">

                                            <a href="<?php echo e(route('transport.dashboard', [request()->lang])); ?>"

                                                class="text-muted text-hover-primary"><?php echo app('translator')->get('lang.dashboard'); ?></a>

                                        </li>

                                        <!--end::Item-->

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item">

                                            <span class="bullet bg-gray-500 w-5px h-2px"></span>

                                        </li>

                                        <!--end::Item-->

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item text-muted"><?php echo app('translator')->get('lang.asset'); ?></li>

                                        <!--end::Item-->

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item">

                                            <span class="bullet bg-gray-500 w-5px h-2px"></span>

                                        </li>

                                        <!--end::Item-->

                                        <!--begin::Item-->

                                        <li class="breadcrumb-item text-muted"><?php echo app('translator')->get('lang.vehicles'); ?></li>

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

                                            <input type="text" data-kt-packages-table-filter="search"

                                                class="form-control form-control-solid w-250px ps-13"

                                                placeholder="<?php echo app('translator')->get('lang.searchV'); ?>" />

                                        </div>

                                        <!--end::Search-->

                                    </div>

                                    <!--end::Card title-->

                                    <!--begin::Card toolbar-->

                                    <?php if($permissions->contains(1)): ?>

                                    <div class="card-toolbar">

                                        <!--begin::Button-->

                                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"

                                            data-bs-target="#kt_modal_add_vehicle">

                                            <i class="ki-outline ki-plus-square fs-3"></i><?php echo app('translator')->get('lang.addV'); ?></button>

                                        

                                        <!--end::Button-->

                                    </div>

                                    <?php endif; ?>

                                    <!--end::Card toolbar-->

                                </div>

                                <!--end::Card header-->

                                <!--begin::Card body-->

                                <div class="card-body pt-0">

                                    <!--begin::Table-->

                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0"

                                        id="kt_vehicles_table">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.name'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.vin'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.make'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.model'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.year'); ?></th>

                                                <th class="min-w-125px">Fuel type</th>

                                                <th class="min-w-125px">License state</th>

                                                <th class="min-w-125px">Fuel tank primary</th>

                                                <th class="min-w-125px">Fuel tank secondary</th>

                                                <th class="min-w-125px">Throttle wifi</th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.notes'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.licensePlate'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.status'); ?></th>

                                                <th class="min-w-125px"><?php echo app('translator')->get('lang.created'); ?></th>

                                                <th class="text-end min-w-100px"><?php echo app('translator')->get('lang.actions'); ?></th>

                                            </tr>

                                        </thead>

                                        <tbody class="fw-semibold text-gray-600">

                                            <?php if(isset($vehicles) && count($vehicles) > 0): ?>

                                            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <tr>

                                                <td><?php echo e($vehicle->name); ?></td>

                                                <td><?php echo e($vehicle->vin); ?></td>

                                                

                                                <?php $__currentLoopData = $make; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <?php if($value->option_id == $vehicle->make): ?>

                                                <td data-make=<?php echo e($vehicle->make); ?>><?php echo e($value->title); ?>


                                                </td>

                                                <?php endif; ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <td><?php echo e($vehicle->model); ?></td>

                                                <td><?php echo e($vehicle->year); ?></td>

                                                <?php $__currentLoopData = $option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <?php if($values->option_id == $vehicle->fuel_type): ?>

                                                <td data-fuel=<?php echo e($vehicle->fuel_type); ?>>

                                                    <?php echo e($values->title); ?>

                                                </td>

                                                <?php endif; ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php $__currentLoopData = $state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <?php if($value->state_id == $vehicle->license_state): ?>

                                                <td data-state=<?php echo e($value->state_id); ?>>

                                                    <?php echo e($value->state_name); ?>

                                                </td>

                                                <?php endif; ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <td><?php echo e($vehicle->fuel_tank_primary); ?></td>

                                                <td><?php echo e($vehicle->fuel_tank_secondary); ?></td>

                                                <?php $__currentLoopData = $throttle_wifi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <?php if($key == $vehicle->throttle_wifi): ?>

                                                <td data-wifi=<?php echo e($key); ?>>

                                                    <?php echo e($value); ?>


                                                </td>

                                                <?php endif; ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <td><?php echo e($vehicle->notes); ?></td>

                                                <td><?php echo e($vehicle->license_plate); ?></td>

                                                <td>

                                                    <div

                                                        class="badge badge-light-<?php echo e($vehicle->status ? 'success' : 'danger'); ?>">

                                                        <?php echo e($vehicle->status ? __('lang.active') : __('lang.deActive')); ?>


                                                    </div>

                                                </td>

                                                <td><?php echo e(date('d M Y, h:i a', strtotime($vehicle->created_at))); ?>


                                                </td>

                                                <td class="text-end">

                                                    <?php if($permissions->contains(2)): ?>

                                                    <button

                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px me-3"

                                                        data-vehicles-table-filter="update_row"

                                                        data-url="<?php echo e(isset(request()->lang) ? route('vehicles.update', [request()->lang, $vehicle->id]) : route('vehicles.update', ['en', $vehicle->id])); ?>"

                                                        data-bs-toggle="modal"

                                                        data-bs-target="#kt_modal_update_vehicle">

                                                        <i class="ki-outline ki-pencil fs-3"></i>

                                                    </button>

                                                    <?php endif; ?>

                                                    

                                                    <label class="form-switch form-check-solid">

                                                        <input class="form-check-input border" type="checkbox"

                                                            data-url="<?php echo e(isset(request()->lang) ? route('vehicles.destroy', [request()->lang, $vehicle->id]) : route('vehicles.destroy', ['en', $vehicle->id])); ?>"

                                                            data-vehicles-table-filter="change_status_row"

                                                            value=""

                                                            <?php echo e($vehicle->status ? 'checked' : ''); ?> />

                                                    </label>

                                                </td>

                                            </tr>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <?php endif; ?>

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

    <!--begin::Modal - Add Vehicles-->

    <div class="modal fade" id="kt_modal_add_vehicle" tabindex="-1" aria-hidden="true">

        <!--begin::Modal dialog-->

        <div class="modal-dialog modal-dialog-centered mw-650px">

            <!--begin::Modal content-->

            <div class="modal-content">

                <!--begin::Modal header-->

                <?php if($permissions->contains(1)): ?>

                <div class="modal-header">

                    <!--begin::Modal title-->

                    <h2 class="fw-bold"><?php echo app('translator')->get('lang.addAVehicle'); ?></h2>

                    <!--end::Modal title-->

                    <!--begin::Close-->

                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-vehicle-modal-action="close"

                        data-bs-dismiss="modal">

                        <i class="ki-outline ki-cross fs-1"></i>

                    </div>

                    <!--end::Close-->

                </div>

                <?php endif; ?>

                <!--end::Modal header-->

                <!--begin::Modal body-->

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <!--begin::Form-->

                    <form id="kt_modal_add_vehicle_form" class="form" action="#" method="post">

                        <?php echo csrf_field(); ?>

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.name'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Name is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterName'); ?>"

                                name="name" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.vin'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Vin is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterVIN'); ?>"

                                name="vin" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.make'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Make is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="make"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="<?php echo app('translator')->get('lang.selectOption'); ?>">

                                <option></option>

                                <?php $__currentLoopData = $make; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->option_id); ?>"><?php echo e($value->title); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.model'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterModel'); ?>"

                                name="model" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.year'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="year"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="<?php echo app('translator')->get('lang.selectOption'); ?>">

                                <option></option>

                                <?php $__currentLoopData = range($vehicle_year, date('Y')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!-- begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Fuel type</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="fuel_type"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->option_id); ?>"><?php echo e($value->title); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">License state</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="license_state"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->state_id); ?>"><?php echo e($value->state_name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Fuel tank capacity</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="Fuel tank primary"

                                name="fuel_tank_primary" />

                            <input class="form-control form-control-solid mt-4" placeholder="Fuel tank secondary"

                                name="fuel_tank_secondary" />

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Throttle to throttle Wifi Hotspot per vehicle</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="throttle_wifi"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $throttle_wifi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span><?php echo app('translator')->get('lang.notes'); ?></span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <textarea class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterNotes'); ?>" name="notes" cols="30"

                                rows="3"></textarea>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.licensePlate'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="License Plate is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterLicense'); ?>"

                                name="license_plate" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Actions-->

                        <div class="text-center pt-15">

                            <button type="reset" class="btn btn-light me-3" data-kt-addresses-modal-action="cancel"

                                data-bs-dismiss="modal"><?php echo app('translator')->get('lang.discard'); ?></button>

                            <button type="submit" class="btn btn-primary" data-kt-addresses-modal-action="submit"

                                id="kt_modal_add_vehicle_form_submit">

                                <span class="indicator-label"><?php echo app('translator')->get('lang.submit'); ?></span>

                                <span class="indicator-progress"><?php echo app('translator')->get('lang.pleaseWait'); ?>

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

    <!--begin::Modal - Update packages-->

    <div class="modal fade" id="kt_modal_update_vehicle" tabindex="-1" aria-hidden="true">

        <!--begin::Modal dialog-->

        <div class="modal-dialog modal-dialog-centered mw-650px">

            <!--begin::Modal content-->

            <div class="modal-content">

                <!--begin::Modal header-->

                <div class="modal-header">

                    <!--begin::Modal title-->

                    <h2 class="fw-bold"><?php echo app('translator')->get('lang.updateVehicle'); ?></h2>

                    <!--end::Modal title-->

                    <!--begin::Close-->

                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"

                        data-kt-packages-modal-action="close">

                        <i class="ki-outline ki-cross fs-1"></i>

                    </div>

                    <!--end::Close-->

                </div>

                <!--end::Modal header-->

                <!--begin::Modal body-->

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <!--begin::Form-->

                    <form id="kt_modal_update_vehicle_form" class="form" action="#">

                        <?php echo csrf_field(); ?>

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.name'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Name is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterName'); ?>"

                                name="name" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.vin'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Vin is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterVIN'); ?>"

                                name="vin" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.make'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Make is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="make" data-control="select2"

                                data-placeholder="<?php echo app('translator')->get('lang.selectOption'); ?>">

                                <option></option>

                                <?php $__currentLoopData = $make; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->option_id); ?>"><?php echo e($value->title); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.model'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterModel'); ?>"

                                name="model" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.year'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="year"

                                data-dropdown-parent="#kt_modal_update_vehicle" data-control="select2"

                                data-placeholder="<?php echo app('translator')->get('lang.selectOption'); ?>">

                                <option></option>

                                <?php $__currentLoopData = range($vehicle_year, date('Y')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!-- begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Fuel type</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="fuel_type" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->option_id); ?>"><?php echo e($value->title); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">License state</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Year is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="license_state"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($value->state_id); ?>"><?php echo e($value->state_name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Fuel tank capacity</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="Fuel tank primary"

                                name="fuel_tank_primary" />

                            <input class="form-control form-control-solid mt-4" placeholder="Fuel tank secondary"

                                name="fuel_tank_secondary" />

                            <!--end::Input-->

                        </div>

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required">Throttle to throttle Wifi Hotspot per vehicle</span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="Model is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <select class="form-select form-select-solid" name="throttle_wifi"

                                data-dropdown-parent="#kt_modal_add_vehicle" data-control="select2"

                                data-placeholder="Select a fuel type">

                                <option></option>

                                <?php $__currentLoopData = $throttle_wifi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span><?php echo app('translator')->get('lang.notes'); ?></span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <textarea class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterNotes'); ?>" name="notes" cols="30"

                                rows="3"></textarea>

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Input group-->

                        <div class="fv-row mb-7">

                            <!--begin::Label-->

                            <label class="fs-6 fw-semibold form-label mb-2">

                                <span class="required"><?php echo app('translator')->get('lang.licensePlate'); ?></span>

                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover"

                                    data-bs-html="true" data-bs-content="License Plate is required.">

                                    <i class="ki-outline ki-information fs-7"></i>

                                </span>

                            </label>

                            <!--end::Label-->

                            <!--begin::Input-->

                            <input class="form-control form-control-solid" placeholder="<?php echo app('translator')->get('lang.enterLicense'); ?>"

                                name="license_plate" />

                            <!--end::Input-->

                        </div>

                        <!--end::Input group-->

                        <!--begin::Actions-->

                        <div class="text-center pt-15">

                            <button type="reset" class="btn btn-light me-3" data-kt-vehicles-modal-action="cancel"

                                data-bs-dismiss="modal"><?php echo app('translator')->get('lang.discard'); ?></button>

                            <button type="submit" class="btn btn-primary" id="kt_modal_update_vehicle_form_submit"

                                data-kt-vehicls-modal-action="submit">

                                <span class="indicator-label"><?php echo app('translator')->get('lang.submit'); ?></span>

                                <span class="indicator-progress"><?php echo app('translator')->get('lang.pleaseWait'); ?>

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

    <!--end::Modal - Update packages-->

    <!--end::Modals-->

    <?php $__env->stopSection(); ?>



    <?php $__env->startSection('footer-script'); ?>

    <script>
        t = $("#kt_vehicles_table").DataTable();





        // Add Address and Geofence start

        // Define form element

        const form = document.getElementById('kt_modal_add_vehicle_form');

        modal = new bootstrap.Modal(document.getElementById('kt_modal_add_vehicle'));



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

                    'vin': {

                        validators: {

                            notEmpty: {

                                message: 'Vin is required'

                            }

                        }

                    },



                    'make': {

                        validatores: {

                            notEmpty: {

                                message: 'Make is required'

                            }

                        }

                    },

                    'model': {

                        validators: {

                            notEmpty: {

                                message: 'Model is required'

                            }

                        }

                    },

                    'year': {

                        validators: {

                            notEmpty: {

                                message: 'Year is required'

                            }

                        }

                    },

                    'license_plate': {

                        validators: {

                            notEmpty: {

                                message: 'License is required'

                            }

                        }

                    },

                    'fuel_type': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel type is required'

                            }

                        }

                    },

                    'license_state': {

                        validators: {

                            notEmpty: {

                                message: 'License state is required'

                            }

                        }

                    },

                    'throttle_wifi': {

                        validators: {

                            notEmpty: {

                                message: 'Throttle wifi is required'

                            }

                        }

                    },

                    'fuel_tank_primary': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel tank primary is required'

                            }

                        }

                    },

                    'fuel_tank_secondary': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel tank secondary is required'

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

        const submitButton = document.getElementById('kt_modal_add_vehicle_form_submit');

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

                            url: "<?php echo e(isset(request()->lang) ? route('vehicles.store', request()->lang) : route('vehicles.store', 'en')); ?>",

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

                                            text: "Vehicle not added!",

                                            icon: "error",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                            text: "Vehicle has been successfully added!",

                                            icon: "success",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                    text: "Vehicle not added!",

                                    icon: "error",

                                    buttonsStyling: false,

                                    confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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





        $(document).on('click', '[data-vehicles-table-filter="update_row"]', function() {

            updateUrl = $(this).data('url');

            td = $(this).closest('tr').children('td');

            $('.modal-body input[name="name"]').val(td.eq(0).text());

            $('.modal-body input[name="vin"]').val(td.eq(1).text());

            $(document).ready(function() {

                var make = td.eq(2).attr('data-make');

                $('.modal-body select[name="make"]').val(make).trigger("change");

            });

            $('.modal-body input[name="model"]').val(td.eq(3).text());

            // $('.modal-body select[name="year"]').select2("val", td.eq(4).text().toString());

            $('.modal-body select[name="year"]').val(td.eq(4).text().toString()).trigger("change");

            $(document).ready(function() {

                // Assuming td is a jQuery object representing the table row or cell

                var fuelType = td.eq(5).attr('data-fuel'); // Fetch the value from the data-fuel attribute

                $('.modal-body select[name="fuel_type"]').val(fuelType).trigger("change");



                var stateId = td.eq(6).attr('data-state');

                $('.modal-body select[name="license_state"]').val(stateId).trigger("change");



                var wifi = td.eq(9).attr('data-wifi');

                $('.modal-body select[name="throttle_wifi"]').val(wifi).trigger("change");

            });

            $('.modal-body input[name="fuel_tank_primary"]').val(td.eq(7).text());

            $('.modal-body input[name="fuel_tank_secondary"]').val(td.eq(8).text());

            $('.modal-body textarea[name="notes"]').val(td.eq(10).text());

            $('.modal-body input[name="license_plate"]').val(td.eq(11).text());

        });



        // Define form element

        const updateForm = document.getElementById('kt_modal_update_vehicle_form');

        updateModal = new bootstrap.Modal(document.getElementById('kt_modal_update_vehicle'));



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

                    'vin': {

                        validators: {

                            notEmpty: {

                                message: 'Vin is required'

                            }

                        }

                    },



                    'make': {

                        validatores: {

                            notEmpty: {

                                message: 'Make is required'

                            }

                        }

                    },

                    'model': {

                        validators: {

                            notEmpty: {

                                message: 'Model is required'

                            }

                        }

                    },

                    'year': {

                        validators: {

                            notEmpty: {

                                message: 'Year is required'

                            }

                        }

                    },

                    'license_plate': {

                        validators: {

                            notEmpty: {

                                message: 'License is required'

                            }

                        }

                    },

                    'fuel_type': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel type is required'

                            }

                        }

                    },

                    'license_state': {

                        validators: {

                            notEmpty: {

                                message: 'License state is required'

                            }

                        }

                    },

                    'throttle_wifi': {

                        validators: {

                            notEmpty: {

                                message: 'Throttle wifi is required'

                            }

                        }

                    },

                    'fuel_tank_primary': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel tank primary is required'

                            }

                        }

                    },

                    'fuel_tank_secondary': {

                        validators: {

                            notEmpty: {

                                message: 'Fuel tank secondary is required'

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

        const submitUpdateButton = document.getElementById('kt_modal_update_vehicle_form_submit');

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

                                            text: "<?php echo e(__('lang.vNotUpdatedSuccess')); ?>",

                                            icon: "error",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                            text: "<?php echo app('translator')->get('lang.vUpdatedSuccess'); ?>",

                                            icon: "success",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                    text: "<?php echo e(__('lang.vNotUpdatedSuccess')); ?>",

                                    icon: "error",

                                    buttonsStyling: false,

                                    confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

        const changeStatusButtons = document.querySelectorAll('[data-vehicles-table-filter="change_status_row"]');



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

                const vehicleName = parent.querySelectorAll('td')[0].innerText;



                if (d.checked) {



                    Swal.fire({

                        text: "<?php echo e(__('lang.areSure')); ?> <?php echo e(__('lang.activate')); ?> " +

                            vehicleName + "?",

                        icon: "warning",

                        showCancelButton: true,

                        buttonsStyling: false,

                        confirmButtonText: "<?php echo e(__('lang.yes')); ?>, <?php echo e(__('lang.activate')); ?>!",

                        cancelButtonText: "<?php echo e(__('lang.no')); ?>, <?php echo e(__('lang.cancel')); ?>",

                        customClass: {

                            confirmButton: "btn fw-bold btn-danger",

                            cancelButton: "btn fw-bold btn-active-light-primary"

                        }

                    }).then(function(result) {

                        if (result.value) {

                            Swal.fire({

                                text: "Activating " + vehicleName,

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

                                                text: vehicleName +

                                                    " was not activated.",

                                                icon: "error",

                                                buttonsStyling: false,

                                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

                                                customClass: {

                                                    confirmButton: "btn fw-bold btn-primary",

                                                }

                                            }).then(function(t) {

                                                location.reload();

                                            });

                                        } else {

                                            Swal.fire({

                                                text: "You have activated " +

                                                    vehicleName + "!.",

                                                icon: "success",

                                                buttonsStyling: false,

                                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                            text: vehicleName +

                                                " was not activated.",

                                            icon: "error",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

                                            customClass: {

                                                confirmButton: "btn fw-bold btn-primary",

                                            }

                                        });

                                    },



                                });

                            });

                        } else if (result.dismiss === 'cancel') {

                            Swal.fire({

                                text: vehicleName + " was not activated.",

                                icon: "error",

                                buttonsStyling: false,

                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

                                customClass: {

                                    confirmButton: "btn fw-bold btn-primary",

                                }

                            });

                        }

                    });



                } else {



                    Swal.fire({

                        text: "<?php echo e(__('lang.areSure')); ?> <?php echo e(__('lang.deactivate')); ?> " +

                            vehicleName + "?",

                        icon: "warning",

                        showCancelButton: true,

                        buttonsStyling: false,

                        confirmButtonText: "<?php echo e(__('lang.yes')); ?>, <?php echo e(__('lang.deactivate')); ?>!",

                        cancelButtonText: "<?php echo e(__('lang.no')); ?>, <?php echo e(__('lang.cancel')); ?>",

                        customClass: {

                            confirmButton: "btn fw-bold btn-danger",

                            cancelButton: "btn fw-bold btn-active-light-primary"

                        }

                    }).then(function(result) {

                        if (result.value) {

                            Swal.fire({

                                text: "Deactivating " + vehicleName,

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

                                                text: vehicleName +

                                                    " was not deactivated.",

                                                icon: "error",

                                                buttonsStyling: false,

                                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

                                                customClass: {

                                                    confirmButton: "btn fw-bold btn-primary",

                                                }

                                            }).then(function(t) {

                                                location.reload();

                                            });

                                        } else {

                                            Swal.fire({

                                                text: "You have deactivated " +

                                                    vehicleName + "!.",

                                                icon: "success",

                                                buttonsStyling: false,

                                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

                                            text: vehicleName +

                                                " was not deactivated.",

                                            icon: "error",

                                            buttonsStyling: false,

                                            confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

                                            customClass: {

                                                confirmButton: "btn fw-bold btn-primary",

                                            }

                                        });

                                    },



                                });

                            });

                        } else if (result.dismiss === 'cancel') {

                            Swal.fire({

                                text: vehicleName + " was not deactivated.",

                                icon: "error",

                                buttonsStyling: false,

                                confirmButtonText: "<?php echo e(__('lang.okGotIt')); ?>",

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

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('transport.layout.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/transport/assets/vehicles/index.blade.php ENDPATH**/ ?>