 <header id="page-topbar">
     <div class="layout-width">
         <div class="navbar-header">
             <div class="d-flex">
                 <!-- LOGO -->
                 <div class="navbar-brand-box horizontal-logo">
                     <a href="index.html" class="logo logo-dark">
                         <span class="logo-sm">
                             <img src="{{ asset('assets') }}/images/logo-sm.png" alt="" height="22">
                         </span>
                         <span class="logo-lg">
                             <img src="{{ asset('assets') }}/images/logo-dark.png" alt="" height="17">
                         </span>
                     </a>

                     <a href="index.html" class="logo logo-light">
                         <span class="logo-sm">
                             <img src="{{ asset('assets') }}/images/logo-sm.png" alt="" height="22">
                         </span>
                         <span class="logo-lg">
                             <img src="{{ asset('assets') }}/images/logo-light.png" alt="" height="17">
                         </span>
                     </a>
                 </div>

                 <button type="button"
                     class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                     id="topnav-hamburger-icon">
                     <span class="hamburger-icon">
                         <span></span>
                         <span></span>
                         <span></span>
                     </span>
                 </button>

                 <!-- App Search-->
                 <form class="app-search d-none d-md-block">
                     <div class="position-relative">
                         <input type="text" class="form-control" placeholder="Search..." autocomplete="off"
                             id="search-options" value="">
                         <span class="mdi mdi-magnify search-widget-icon"></span>
                         <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                             id="search-close-options"></span>
                     </div>
                     <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                         <div data-simplebar style="max-height: 320px;">
                             <!-- item-->
                             <div class="dropdown-header">
                                 <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                             </div>

                             <div class="dropdown-item bg-transparent text-wrap">
                                 <a href="index.html" class="btn btn-soft-secondary btn-sm btn-rounded">how to
                                     setup <i class="mdi mdi-magnify ms-1"></i></a>
                                 <a href="index.html" class="btn btn-soft-secondary btn-sm btn-rounded">buttons
                                     <i class="mdi mdi-magnify ms-1"></i></a>
                             </div>
                             <!-- item-->
                             <div class="dropdown-header mt-2">
                                 <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                             </div>

                             <!-- item-->
                             <a href="javascript:void(0);" class="dropdown-item notify-item">
                                 <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                 <span>Analytics Dashboard</span>
                             </a>

                             <!-- item-->
                             <a href="javascript:void(0);" class="dropdown-item notify-item">
                                 <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                 <span>Help Center</span>
                             </a>

                             <!-- item-->
                             <a href="javascript:void(0);" class="dropdown-item notify-item">
                                 <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                 <span>My account settings</span>
                             </a>

                             <!-- item-->
                             <div class="dropdown-header mt-2">
                                 <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                             </div>

                             <div class="notification-list">
                                 <!-- item -->
                                 <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                     <div class="d-flex">
                                         <img src="{{ asset('assets') }}/images/users/avatar-2.jpg"
                                             class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                         <div class="flex-1">
                                             <h6 class="m-0">Angela Bernier</h6>
                                             <span class="fs-11 mb-0 text-muted">Manager</span>
                                         </div>
                                     </div>
                                 </a>
                                 <!-- item -->
                                 <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                     <div class="d-flex">
                                         <img src="{{ asset('assets') }}/images/users/avatar-3.jpg"
                                             class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                         <div class="flex-1">
                                             <h6 class="m-0">David Grasso</h6>
                                             <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                         </div>
                                     </div>
                                 </a>
                                 <!-- item -->
                                 <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                     <div class="d-flex">
                                         <img src="{{ asset('assets') }}/images/users/avatar-5.jpg"
                                             class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                         <div class="flex-1">
                                             <h6 class="m-0">Mike Bunch</h6>
                                             <span class="fs-11 mb-0 text-muted">React Developer</span>
                                         </div>
                                     </div>
                                 </a>
                             </div>
                         </div>

                         <div class="text-center pt-3 pb-1">
                             <a href="pages-search-results.html" class="btn btn-primary btn-sm">View All
                                 Results <i class="ri-arrow-right-line ms-1"></i></a>
                         </div>
                     </div>
                 </form>
             </div>

             <div class="d-flex align-items-center">

                 <div class="dropdown d-md-none topbar-head-dropdown header-item">
                     <button type="button"
                         class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                         id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                         aria-expanded="false">
                         <i class="bx bx-search fs-22"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                         aria-labelledby="page-header-search-dropdown">
                         <form class="p-3">
                             <div class="form-group m-0">
                                 <div class="input-group">
                                     <input type="text" class="form-control" placeholder="Search ..."
                                         aria-label="Recipient's username">
                                     <button class="btn btn-primary" type="submit"><i
                                             class="mdi mdi-magnify"></i></button>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>

                 <div class="ms-1 header-item d-none d-sm-flex">
                     <button type="button"
                         class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                         data-toggle="fullscreen">
                         <i class='bx bx-fullscreen fs-22'></i>
                     </button>
                 </div>

                 <div class="ms-1 header-item d-none d-sm-flex">
                     <button type="button"
                         class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                         <i class='bx bx-moon fs-22'></i>
                     </button>
                 </div>

                 <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                     <button type="button"
                         class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                         id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                         data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                         <i class='bx bx-bell fs-22'></i>
                         <span
                             class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                             3<span class="visually-hidden">unread messages</span>
                         </span>
                     </button>

                     <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                         aria-labelledby="page-header-notifications-dropdown">

                         <div class="dropdown-head bg-primary bg-pattern rounded-top">
                             <div class="p-3">
                                 <div class="row align-items-center">
                                     <div class="col">
                                         <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                     </div>
                                 </div>
                             </div>

                             <div class="px-2 pt-2">
                                 <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                     id="notificationItemsTab" role="tablist">
                                     <li class="nav-item waves-effect waves-light">
                                         <a class="nav-link active" data-bs-toggle="tab" href="#messages-tab"
                                             role="tab" aria-selected="true">
                                             Messages
                                         </a>
                                     </li>
                                 </ul>
                             </div>
                         </div>

                         <div class="tab-content position-relative" id="notificationItemsTabContent">
                             <div class="tab-pane fade show active py-2 ps-2" id="messages-tab" role="tabpanel">
                             </div>

                             <div class="notification-actions" id="notification-actions">
                                 <div class="d-flex text-muted justify-content-center">
                                     Select <div id="select-content" class="text-body fw-semibold px-1">0</div>
                                     Result <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                         data-bs-toggle="modal"
                                         data-bs-target="#removeNotificationModal">Remove</button>
                                 </div>
                             </div>
                         </div>

                     </div>
                 </div>

                 <div class="dropdown ms-sm-3 header-item topbar-user">
                     <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                         data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                         <span class="d-flex align-items-center">
                             <img class="rounded-circle header-profile-user"
                                 src="{{ asset('assets') }}/images/users/avatar-1.jpg" alt="Header Avatar">
                             <span class="text-start ms-xl-2">
                                 <span
                                     class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                                 {{-- <span
                                            class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">Founder</span> --}}
                             </span>
                         </span>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end">
                         <!-- item-->
                         <h6 class="dropdown-header">Welcome</h6>
                         <a class="dropdown-item" href="pages-profile.html"><i
                                 class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                 class="align-middle">Profile</span></a>
                         <a class="dropdown-item" href="auth-logout-basic.html"><i
                                 class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                 class="align-middle" data-key="t-logout">Logout</span></a>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </header>
