<!DOCTYPE html>

<html lang="en">


<head>
    <base href="<?php echo e(url('/')); ?>" />
    <title>Admin panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?php echo e(url('/')); ?>" />
    <meta property="og:site_name" content="Admin panel" />
    <link rel="canonical" href="<?php echo e(url('/')); ?>" />
    <link rel="shortcut icon" href="<?php echo e(url('assets/media/logos/favicon.ico')); ?>" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="<?php echo e(url('assets/plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="<?php echo e(url('assets/plugins/global/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(url('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(url('assets/css/paginate.css')); ?>" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"
    data-kt-app-aside-enabled="true" data-kt-app-aside-fixed="true" data-kt-app-aside-push-toolbar="true"
    data-kt-app-aside-push-footer="true" class="app-default">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script><?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/super-admin/layout/header.blade.php ENDPATH**/ ?>