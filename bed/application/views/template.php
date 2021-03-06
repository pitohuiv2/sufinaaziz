<!DOCTYPE html>
<html lang="en">

<head>
  <title>PCure-NEO </title>
  <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="#">
  <meta name="keywords"
    content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
  <meta name="author" content="#">
  <!-- Favicon icon -->
  <link rel="icon" href="<?php echo base_url(); ?>assets\images\favicon.png" type="image/x-icon">
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;1,200&display=swap" rel="stylesheet">
  <!-- Required Fremwork -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\bootstrap\css\bootstrap.min.css">
  <!-- themify-icons line icon -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\icon\themify-icons\themify-icons.css">
  <!-- ico font -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\icon\icofont\css\icofont.css">
  <!-- feather Awesome -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\icon\feather\css\feather.css">
  <!-- Select 2 css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components\select2\css\select2.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets\icon\font-awesome\css\font-awesome.min.css">
  <!-- Data Table Css -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets\pages\data-table\css\buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\datatables.net-responsive-bs4\css\responsive.bootstrap4.min.css">

  <!-- Syntax highlighter Prism css -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\pages\prism\prism.css">
  <!-- jpro forms css -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\pages\j-pro\css\demo.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\pages\j-pro\css\font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\pages\j-pro\css\j-pro-modern.css">
  <!-- Sweetalert Css -->
  <link href="<?php echo base_url(); ?>assets/vendors/sweetalert/sweetalert.css" rel="stylesheet" />
  <!-- Date-time picker css -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets\pages\advance-elements\css\bootstrap-datetimepicker.css">
  <!-- Date-range picker css  -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\bootstrap-daterangepicker\css\daterangepicker.css">
  <!-- Date-Dropper css -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\datedropper\css\datedropper.min.css">
  <!-- Color Picker css -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>bower_components\spectrum\css\spectrum.css">
  <!-- Mini-color css -->
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>bower_components\jquery-minicolors\css\jquery.minicolors.css">
  <!-- radial chart -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets\pages\chart\radial\css\radial.css" type="text/css"
    media="all">
  <!-- Style.css -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\pages\j-pro\css\j-forms.css">

  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\css\jquery.mCustomScrollbar.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\css\pcoded-horizontal.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets\css\bootstrapValidator.min.css" />
  <!-- ion icon css -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\icon\ion-icon\css\ionicons.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets\css\style.css">

  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.5.10/css/ripples.min.css" />
  <link rel="stylesheet"
    href="http://t00rk.github.io/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" />
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,500' rel='stylesheet' type='text/css'>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    #notifikasi {
      cursor: pointer;
      position: fixed;
      right: 0px;
      z-index: 9999;
      bottom: 0px;
      margin-bottom: 22px;
      margin-right: 15px;
      min-width: 300px;
      max-width: 800px;
    }

    .right {
      text-align: right;
    }

    fieldset {
      border: 1px solid #ddd !important;
      margin: 0;
      xmin-width: 0;
      padding: 10px;
      position: relative;
      border-radius: 4px;
      padding-left: 10px !important;
    }

    legend {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 0px;
      width: 35%;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px 5px 5px 10px;
      background-color: #ffffff;
    }
  </style>
  <!-- Required Jquery -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\jquery\js\jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\jquery-ui\js\jquery-ui.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\popper.js\js\popper.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\bootstrap\js\bootstrap.min.js"></script>
  <!-- j-pro js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>assets\pages\j-pro\js\jquery.ui.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets\pages\j-pro\js\jquery.maskedinput.min.js">
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets\pages\j-pro\js\jquery.j-pro.js"></script>

  <!-- jquery slimscroll js -->
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>

  <!-- Bootstrap date-time-picker js -->
  <script type="text/javascript"
    src="<?php echo base_url(); ?>assets\pages\advance-elements\moment-with-locales.min.js"></script>
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\bootstrap-datepicker\js\bootstrap-datepicker.min.js"></script>
  <script type="text/javascript"
    src="<?php echo base_url(); ?>assets\pages\advance-elements\bootstrap-datetimepicker.min.js"></script>


  <!-- Date-range picker js -->
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\bootstrap-daterangepicker\js\daterangepicker.js"></script>


  <!-- Date-dropper js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\datedropper\js\datedropper.min.js">
  </script>

  <!-- Color picker js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\spectrum\js\spectrum.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\jscolor\js\jscolor.js"></script>

  <!-- Mini-color js -->
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\jquery-minicolors\js\jquery.minicolors.min.js"></script>

  <!-- modernizr js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\modernizr\js\modernizr.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\modernizr\js\css-scrollbars.js">
  </script>

  <script src="<?php echo base_url(); ?>bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>bower_components\datatables.net-buttons\js\dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url(); ?>bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>bower_components\datatables.net-responsive\js\dataTables.responsive.min.js">
  </script>
  <script src="<?php echo base_url(); ?>bower_components\datatables.net-responsive-bs4\js\responsive.bootstrap4.min.js">
  </script>

  <!-- i18next.min.js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\i18next\js\i18next.min.js"></script>
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js"></script>
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js">
  </script>
  <script type="text/javascript"
    src="<?php echo base_url(); ?>bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>

  <script src="https://cdn.ckeditor.com/ckeditor5/15.0.0/classic/ckeditor.js"></script>

  <!-- SweetAlert Plugin Js -->
  <script src="<?php echo base_url(); ?>assets/vendors/sweetalert/sweetalert.min.js"></script>

  <!-- Select 2 js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\select2\js\select2.full.min.js">
  </script>

  <!-- Chart js -->
  <script type="text/javascript" src="<?php echo base_url(); ?>bower_components\chart.js\js\Chart.js"></script>
  <!-- amchart js -->
  <script src="<?php echo base_url(); ?>assets\pages\widget\amchart\amcharts.js"></script>
  <script src="<?php echo base_url(); ?>assets\pages\widget\amchart\serial.js"></script>
  <script src="<?php echo base_url(); ?>assets\pages\widget\amchart\light.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.5.10/js/ripples.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.5.10/js/material.min.js"></script>
  <script type="text/javascript" src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
  <script type="text/javascript"
    src="http://t00rk.github.io/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
  <!-- Custom js -->
  <script src="<?php echo base_url(); ?>assets\js\pcoded.min.js"></script>
  <script src="<?php echo base_url(); ?>assets\js\menu\menu-hori-fixed.js"></script>
  <script src="<?php echo base_url(); ?>assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets\js\script.js"></script>

  <script type="text/javascript"
    src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>


</head>
<!-- Menu horizontal fixed layout -->

<body>
  <!-- Pre-loader start -->
  <div class="theme-loader">
    <div class="ball-scale">
      <div class='contain'>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
        <div class="ring">
          <div class="frame"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Pre-loader end -->
  <div id="pcoded" class="pcoded">
    <div class="pcoded-container">
      <!-- Menu header start -->
      <nav class="navbar header-navbar pcoded-header">
        <div class="navbar-wrapper">
          <div class="navbar-logo" style="width:300px">
            <!-- <a class="mobile-menu" id="mobile-collapse" href="<?php echo base_url(); ?>dashboard"> -->
            <a class="mobile-menu" id="mobile-collapse">
              <i class="feather icon-menu"></i>
            </a>
            <a href="" style="text-transform:none;    display: flex;height: 100%;
    line-height: 1.5;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;">
              <img src="<?php echo base_url(); ?>assets\images\p-cure.png"
                style="object-fit: cover; height: 100%;margin-left: 20px">

              <span style="font-size: 18px; margin-left: 20px;font-weight: bold;padding-top: 10px;">

                <div class="white--text"
                  style="line-height: 1; align-self: center;color: #fff!important; caret-color: #fff!important;">
                  <div style="font-size: 1.5rem;">BED ONLINE</div>
                  <div style="font-weight: 300;font-size: 1rem;">P-CURE NEO</div>
                </div>
            </a>
            <a class="mobile-options">
              <i class="feather icon-more-horizontal"></i>
            </a>
          </div>
          <div class="navbar-container container-fluid">
            <ul class="nav-left">
              <li class="header-search">
                <div class="main-search morphsearch-search">
                  <div class="input-group">
                    <span class="input-group-addon search-close"><i class="feather icon-x"></i></span>
                    <input type="text" class="form-control">
                    <span class="input-group-addon search-btn"><i class="feather icon-search"></i></span>
                  </div>
                </div>
              </li>
              <li>
                <a href="#!" onclick="javascript:toggleFullScreen()">
                  <i class="feather icon-maximize full-screen"></i>
                </a>
              </li>
            </ul>
            <ul class="nav-right">

              <li class="user-profile header-notification">
                <div class="dropdown-primary ">
                  <div class="dropdown-toggle">
                    <i class="feather icon-clock" style="font-size: 20px;margin-right: 0.5rem;"></i>
                    <span id="timer"> </span>
                    <i class="feather icon-chevron-down"></i>
                  </div>
                  <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn"
                    data-dropdown-out="fadeOut">
                    <li>
                      <a href="<?php echo base_url(); ?>auth/logout">
                        <i class="feather icon-log-out"></i> Logout
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- Menu header end -->
      <div class="pcoded-main-container">
        <nav class="pcoded-navbar">
          <div class="pcoded-inner-navbar">
            <ul class="pcoded-item pcoded-left-item">

              <?php
    						if (!empty($menu)) {
    							echo $menu;
    						}
    					?>
            </ul>
          </div>
        </nav>
        <div class="pcoded-wrapper">
          <div class="pcoded-content">
            <div class="pcoded-inner-content">
              <!-- Main-body start -->
              <div class="main-body">
                <div id="notifikasi"><?php echo $this->session->flashdata('msg'); ?></div>
                <?php
        					if (!empty($content)) {
        						echo $content;
        					}
        					if (!empty($inside)) {
        						echo $inside;
        					}
        				?>
              </div>
              <!-- Main-body end -->
              <div id="styleSelector"> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $("#notifikasi").slideDown('slow').delay(3000).slideUp('slow');

    $(document).ready(function () {
      getdate();
    });

    function getdate() {
      var today = new Date();
      var h = today.getHours();
      var m = today.getMinutes();
      var s = today.getSeconds();
      if (h < 10) {
        h = "0" + h;
      }
      if (m < 10) {
        m = "0" + m;
      }
      if (s < 10) {
        s = "0" + s;
      }

      var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober',
        'November', 'Desember'
      ];
      var myDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum&#39;at', 'Sabtu'];
      var date = new Date();
      var day = date.getDate();
      var month = date.getMonth();
      var thisDay = date.getDay(),
        thisDay = myDays[thisDay];
      var yy = date.getYear();
      var year = (yy < 1000) ? yy + 1900 : yy;

      var tgl = ("Hari : " + thisDay + ', ' + day + ' ' + months[month] + ' ' + year);
      var jam = (h + ":" + m + ":" + s + " wib");
      $("#timer").html(tgl + ' ' + jam);
      setTimeout(function () {
        getdate()
      }, 1000);
    }
  </script>
</body>

</html>