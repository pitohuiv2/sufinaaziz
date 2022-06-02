<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v3.0.0-alpha.1
* @link https://coreui.io
* Copyright (c) 2019 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->

<html lang="en">
<head>
<!--    <base href="./">-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="UI Template By EpicTeam@Transmedic">

    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>EIS - Login</title>
<!--    <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">-->
<!--    <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">-->
<!--    <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">-->
<!--    <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">-->
<!--    <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">-->
<!--    <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">-->
<!--    <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">-->
<!--    <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">-->
<!--    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">-->
<!--    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">-->
<!--    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">-->
<!--    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">-->
<!--    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">-->
<!--    <link rel="manifest" href="assets/favicon/manifest.json">-->
<!--    <meta name="msapplication-TileColor" content="#ffffff">-->
<!--    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">-->
    <meta name="theme-color" content="#ffffff">
    <!-- Icons-->
    <script src="{{ asset('js/jquery/jquery-3.5.1.min.js') }}"></script>
    <link href="{{ asset('coreui/css/free.min.css') }}" rel="stylesheet"> <!-- icons -->
<!--    <link href="{{ asset('coreui/css/flag-icon.min.css') }}" rel="stylesheet">-->
    <!-- Main styles for this application-->
    <link href="{{ asset('coreui/css/style.css') }}" rel="stylesheet">

    <!-- Global site tag (gtag.js) - Google Analytics-->
<!--    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>-->
    @toastr_css
    @toastr_js
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        // Shared ID
        gtag('config', 'UA-118965717-3');
        // Bootstrap ID
        gtag('config', 'UA-118965717-5');
    </script>

<!--    <link href="{{ asset('coreui/css/coreui-chartjs.css') }}" rel="stylesheet">-->

</head>
<style>
    .bg-primary {
        background-color: #00bcd4 !important;
    }
    .btn-info.disabled, .btn-info:disabled {
        color: #fff;
        background-color: #8C489F;
        border-color: #8C489F;
    }
    .bg-primary {
        background-color: #8C489F !important;
    }
    .btn-info {
        color: #fff;
        background-color: #8C489F;
        border-color: #8C489F;
    }
</style>
<body class="c-app flex-row align-items-center" style="
    background: url({!! asset('images/cover1.jpg') !!}) top/cover no-repeat fixed;
    display: flex;
    flex-direction: row;
    padding: 0;
    height: 100vh;">
<div id="toast"></div>
@toastr_render
@yield('content')

<!-- CoreUI and necessary plugins-->
<script src="{{ asset('coreui/js/coreui.bundle.min.js') }}"></script>
<!-- <script src="https://www.google.com/recaptcha/api.js" async="" defer=""></script> -->
@yield('javascript')

</body>
</html>
