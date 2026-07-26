<!--end::Root-->
<!--begin::Javascript-->
<script>
    var hostUrl = "assets/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/js/eye.js') }}"></script>
<script src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ url('assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ url('assets/js/custom/authentication/sign-in/general.js') }}"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
@yield('footer-script-link')
@yield('footer-script')
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

@if (Session::has('success'))
    <script>
        toastr.success("{{ Session::get('success') }}");
    </script>
@endif
@if (Session::has('error'))
    <script>
        toastr.error("{{ Session::get('error') }}");
    </script>
@endif
@if (Session::has('status'))
    <script>
        toastr.success("{{ Session::get('status') }}");
    </script>
@endif
@if (Session::has('email'))
    <script>
        toastr.error("{{ Session::get('email') }}");
    </script>
@endif
@if (Session::has('password_confirmation'))
    <script>
        toastr.error("{{ Session::get('password_confirmation') }}");
    </script>
@endif
@if (Session::has('password'))
    <script>
        toastr.error("{{ Session::get('password') }}");
    </script>
@endif
</body>
<!--end::Body-->

</html>
