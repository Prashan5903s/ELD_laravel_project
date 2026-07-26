<script>
    var hostUrl = "{{ url('assets/') }}";
</script>

<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ url('assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ url('assets/js/custom/apps/ecommerce/catalog/products.js') }}"></script>
<script src="{{ url('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ url('assets/js/custom/widgets.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/users-search.js') }}"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->

</html>
