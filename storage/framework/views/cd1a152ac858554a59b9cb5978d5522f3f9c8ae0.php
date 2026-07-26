<!--end::Root-->
<!--begin::Javascript-->
<script>
    var hostUrl = "assets/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="<?php echo e(asset('assets/js/eye.js')); ?>"></script>
<script src="<?php echo e(url('assets/plugins/global/plugins.bundle.js')); ?>"></script>
<script src="<?php echo e(url('assets/js/scripts.bundle.js')); ?>"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="<?php echo e(url('assets/js/custom/authentication/sign-in/general.js')); ?>"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
<?php echo $__env->yieldContent('footer-script-link'); ?>
<?php echo $__env->yieldContent('footer-script'); ?>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>

<?php if(Session::has('success')): ?>
    <script>
        toastr.success("<?php echo e(Session::get('success')); ?>");
    </script>
<?php endif; ?>
<?php if(Session::has('error')): ?>
    <script>
        toastr.error("<?php echo e(Session::get('error')); ?>");
    </script>
<?php endif; ?>
<?php if(Session::has('status')): ?>
    <script>
        toastr.success("<?php echo e(Session::get('status')); ?>");
    </script>
<?php endif; ?>
<?php if(Session::has('email')): ?>
    <script>
        toastr.error("<?php echo e(Session::get('email')); ?>");
    </script>
<?php endif; ?>
<?php if(Session::has('password_confirmation')): ?>
    <script>
        toastr.error("<?php echo e(Session::get('password_confirmation')); ?>");
    </script>
<?php endif; ?>
<?php if(Session::has('password')): ?>
    <script>
        toastr.error("<?php echo e(Session::get('password')); ?>");
    </script>
<?php endif; ?>
</body>
<!--end::Body-->

</html>
<?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/login/layouts/footer.blade.php ENDPATH**/ ?>