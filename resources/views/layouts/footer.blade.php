<!--begin::Javascript-->
<script>
    var hostUrl = "{{asset('assets/')}}";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{asset('assets/js/custom/apps/ecommerce/catalog/products.js')}}"></script>
<script src="{{asset('assets/js/widgets.bundle.js')}}"></script>
<script src="{{asset('assets/js/custom/widgets.js')}}"></script>
<script src="{{asset('assets/js/custom/apps/chat/chat.js')}}"></script>
<script src="{{asset('assets/js/custom/utilities/modals/upgrade-plan.js')}}"></script>
<script src="{{asset('assets/js/custom/utilities/modals/create-campaign.js')}}"></script>
<script src="{{asset('assets/js/custom/utilities/modals/users-search.js')}}"></script>
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

@if(Session::has('success'))
    <script>
        toastr.success("{{ Session::get('success') }}");
    </script>
@endif
@if(Session::has('error'))
    <script>
        toastr.error("{{ Session::get('error') }}");
    </script>
@endif
<!--end::Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->

</html>
