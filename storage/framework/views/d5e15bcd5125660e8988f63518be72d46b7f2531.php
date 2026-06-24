<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($user->user_type == 'U'): ?>
        <tr class="descendant-item child-row row-all" style="display: none; background-color:#F5F5F5;" data-user-id="<?php echo e($user->id); ?>"
            data-user-master-id="<?php echo e($user->master_id); ?>">
            <td><i class="fa fa-angle-up"></i></td>
            <td class="d-flex align-items-center"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></td>
            <td>
                <?php if($user->user_type == 'WC'): ?>
                    White label company
                <?php endif; ?>
                <?php if($user->user_type == 'EC'): ?>
                    Company
                <?php endif; ?>
                <?php if($user->user_type == 'RS'): ?>
                    Reseller
                <?php endif; ?>
                <?php if($user->user_type == 'TR'): ?>
                    Transport company
                <?php endif; ?>
                <?php if($user->user_type == 'U'): ?>
                    User/Driver
                <?php endif; ?>
            </td>
            <td><?php echo e(trim(explode(',', $user->country_code)[0])); ?> <?php echo e($user->mobile_no); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($user->created_at)->format('h:i A d-m-Y')); ?></td>
            <td>
                <!--<button class="btn btn-primary shadowLoginBtn" data-user-type="<?php echo e($user->user_type); ?>"-->
                <!--    data-user-id="<?php echo e($user->id); ?>">-->
                <!--    <i class="fa fa-sign-in"></i>-->
                <!--</button>-->
            </td>

            <div class="modal fade" id="shadowLoginModal" tabindex="-1" aria-labelledby="shadowLoginModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="shadowLoginModalLabel">
                                Shadow Login Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: red;"></i>
                            <p style="margin-top: 10px">Are you sure you
                                want to do shadow login?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary shadowLogin_btn" id="shadowLoginYes"
                                data-user-type="<?php echo e($user->user_type); ?>" data-user-id="<?php echo e($user->id); ?>">Yes,
                                Shadow
                                Login</button>
                            <button type="button" class="btn btn-secondary" id="shadowLoginCancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">
                                Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fa fa-close" style="font-size: 48px; color: red;"></i>
                            <p>Shadow login not happening</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" id="okGotIt" data-bs-dismiss="modal">Ok,
                                got it</button>
                        </div>
                    </div>
                </div>
            </div>
        </tr>
    <?php endif; ?>
    <?php if($user->user_type != 'U'): ?>
        <tr class="descendant-item child-row row-all " style="display: none; background-color:#F8F8F8;" data-user-id="<?php echo e($user->id); ?>"
            data-user-master-id="<?php echo e($user->master_id); ?>">
            <td><i class="fa fa-angle-up"></i></td>
            <td class="d-flex align-items-center"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></td>
            <td>
                <?php if($user->user_type == 'WC'): ?>
                    White label company
                <?php endif; ?>
                <?php if($user->user_type == 'EC'): ?>
                    Company
                <?php endif; ?>
                <?php if($user->user_type == 'RS'): ?>
                    Reseller
                <?php endif; ?>
                <?php if($user->user_type == 'TR'): ?>
                    Transport company
                <?php endif; ?>
                <?php if($user->user_type == 'U'): ?>
                    User/Driver
                <?php endif; ?>
            </td>
            <td><?php echo e(trim(explode(',', $user->country_code)[0])); ?> <?php echo e($user->mobile_no); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($user->created_at)->format('h:i A d-m-Y')); ?></td>
            <td>
                <button class="btn btn-primary shadowLoginBtn" data-user-type="<?php echo e($user->user_type); ?>"
                    data-user-id="<?php echo e($user->id); ?>">
                    <i class="fa fa-sign-in"></i>
                </button>
            </td>

            <div class="modal fade" id="shadowLoginModal" tabindex="-1" aria-labelledby="shadowLoginModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="shadowLoginModalLabel">
                                Shadow Login Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: red;"></i>
                            <p style="margin-top: 10px">Are you sure you
                                want to do shadow login?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary shadowLogin_btn" id="shadowLoginYes"
                                data-user-type="<?php echo e($user->user_type); ?>" data-user-id="<?php echo e($user->id); ?>">Yes,
                                Shadow
                                Login</button>
                            <button type="button" class="btn btn-secondary" id="shadowLoginCancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">
                                Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fa fa-close" style="font-size: 48px; color: red;"></i>
                            <p>Shadow login not happening</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" id="okGotIt"
                                data-bs-dismiss="modal">Ok,
                                got it</button>
                        </div>
                    </div>
                </div>
            </div>
        </tr>
    <?php endif; ?>
    <?php echo $__env->make('super-admin.layout.user_row', ['users' => $user->descendants], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/super-admin/layout/user_row.blade.php ENDPATH**/ ?>