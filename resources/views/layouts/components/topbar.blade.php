 <header id="page-topbar">
     <div class="layout-width">
         <div class="navbar-header">
             <div class="d-flex">
                 <!-- LOGO -->
                 <div class="navbar-brand-box horizontal-logo">
                     <a href="index.html" class="logo logo-dark">
                         <span class="logo-sm">
                             <img src="https://app.clttoolbox.com.au/images/logos/logo_color.png" alt=""
                                 height="22">
                         </span>
                         <span class="logo-lg">
                             <img src="https://app.clttoolbox.com.au/images/logos/logo_color.png" alt=""
                                 height="17">
                         </span>
                     </a>

                     <a href="index.html" class="logo logo-light">
                         <span class="logo-sm">
                             <img src="https://app.clttoolbox.com.au/images/logos/logo_color.png" alt=""
                                 height="22">
                         </span>
                         <span class="logo-lg">
                             <img src="https://app.clttoolbox.com.au/images/logos/logo_color.png" alt=""
                                 height="17">
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
                         id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                         aria-haspopup="true" aria-expanded="false">
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
                                 src="{{ asset('assets') }}/images/users/user-dummy-img.jpg" alt="Header Avatar">
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
                         <a class="dropdown-item logout-link" href="javascript:void(0);"><i
                                 class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                 class="align-middle" data-key="t-logout">Keluar</span></a>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </header>
