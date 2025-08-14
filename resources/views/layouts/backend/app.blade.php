@php
    $saasApps = App\Models\SaasApp::all();
    $users = App\Models\Access\User::all();
    $settings = Auth::check()
        ? \App\Models\Setting::firstOrCreate(['user_id' => Auth::id()])
        : null;
    $unreadCount = user()->unreadNotifications->count();
    $upcomingReminders = user()->upcomingReminders()->limit(3)->get();
    $remindersCount = $upcomingReminders->count();
    $totalAlerts = $unreadCount + $remindersCount;
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ticketing System') }} - @yield('title')</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('asset/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('asset/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
    </style>
    @stack('styles')
</head>
<body id="page-top">

<div id="wrapper">
    <ul id="sidebar" class="navbar-nav bg-white text-dark sidebar-light accordion border-end sidebar">
        <a  href="{{ route('home') }}" class="sidebar-brand d-flex align-items-center justify-content-center">
            <div class="sidebar-brand-text mx-3">Ticketing System</div>
        </a>

        <hr class="sidebar-divider my-0" />
        <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if(access()->allow('view_tickets'))
            <div class="sidebar-heading">
                Tickets
            </div>
            <li class="nav-item {{ request()->routeIs('backend.ticket.*') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseTicket" aria-expanded="true" aria-controls="collapseTicket">
                    <i class="fas fa-fw fa-ticket-alt"></i>
                    <span>Tickets management</span>
                </a>
                <div id="collapseTicket" class="collapse" aria-labelledby="headingTwo" data-parent="#sidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('backend.ticket.index') }}">{{__('All Tickets')}}</a>
                        <h6 class="collapse-header">Create New Ticket:</h6>
                        @foreach($saasApps as $app)
                            <a class="collapse-item" href="{{ route('backend.ticket.create', ['saas_app_id' => $app->id]) }}">
                                {{ $app->abbreviation }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </li>
        @endif

        @if(access()->allow('view_reports'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('label.reports')}}
            </div>
            <li class="nav-item {{ request()->routeIs(['backend.report.*']) }}">
                <a class="nav-link collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseReports" aria-expanded="{{ request()->routeIs(['backend.report.*']) ? 'true' : 'false' }}">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>{{__('label.reports_management')}}</span>
                </a>
                <div id="collapseReports" class="collapse {{ request()->routeIs(['backend.report.*']) ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#sidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">{{__('label.tickets')}}:</h6>
                        <a class="collapse-item {{ request()->routeIs('backend.report.*') ? 'active' : '' }}" href="{{ route('backend.report.index') }}">{{__('label.ticket')}}</a>
                    </div>
                </div>
            </li>
        @endif

        @if(access()->allow('manage_topics') || access()->allow('manage_subtopics') || access()->allow('manage_tertiary_topics'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('label.general')}}
            </div>
            <li class="nav-item {{ request()->routeIs(['backend.topic.*', 'backend.subtopic.*', 'backend.tertiary.*']) ? 'active' : '' }}">
                <a class="nav-link collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseTopic" aria-expanded="{{ request()->routeIs(['backend.topic.*', 'backend.subtopic.*', 'backend.tertiary.*']) ? 'true' : 'false' }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>{{__('label.general_settings')}}</span>
                </a>
                <div id="collapseTopic" class="collapse {{ request()->routeIs(['backend.topic.*', 'backend.subtopic.*', 'backend.tertiary.*']) ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#sidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if(access()->allow('manage_topics'))
                            <h6 class="collapse-header">{{__('label.main')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.topic.*') ? 'active' : '' }}" href="{{ route('backend.topic.index') }}">{{__('label.topics')}}</a>
                        @endif
                        @if(access()->allow('manage_subtopics'))
                            <h6 class="collapse-header">{{__('label.sub')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.subtopic.*') ? 'active' : '' }}" href="{{ route('backend.subtopic.index') }}">{{__('label.sub_topics')}}</a>
                        @endif
                        @if(access()->allow('manage_tertiary_topics'))
                            <h6 class="collapse-header">{{__('label.tertiary')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.tertiary.*') ? 'active' : '' }}" href="{{ route('backend.tertiary.index') }}">{{__('label.tertiary_topics')}}</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        @if(access()->allow('manage_operators') || access()->allow('manage_saas_apps') || access()->allow('manage_sender_ids'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('label.other')}}
            </div>
            <li class="nav-item {{
                request()->routeIs('backend.operator.*') ||
                request()->routeIs('backend.saas_app.*') ||
                request()->routeIs('backend.sender_id.*') ? 'active' : ''
            }}">
                <a class="nav-link collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseOther" aria-expanded="true" aria-controls="collapseOther">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>{{__('label.other_settings')}}</span>
                </a>
                <div id="collapseOther" class="collapse {{
                    request()->routeIs('backend.operator.*') ||
                    request()->routeIs('backend.saas_app.*') ||
                    request()->routeIs('backend.sender_id.*') ? 'show' : ''
                }}" aria-labelledby="headingTwo" data-parent="#sidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if(access()->allow('manage_operators'))
                            <h6 class="collapse-header">{{__('label.mno')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.operator.*') ? 'active' : '' }}" href="{{ route('backend.operator.index') }}">{{__('MNOs')}}</a>
                        @endif
                        @if(access()->allow('manage_saas_apps'))
                            <h6 class="collapse-header">{{__('label.service')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.saas_app.*') ? 'active' : '' }}" href="{{ route('backend.saas_app.index') }}">{{__('label.saas_app')}}</a>
                        @endif
                        @if(access()->allow('manage_sender_ids'))
                            <h6 class="collapse-header">{{__('label.sender')}}:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.sender_id.*') ? 'active' : '' }}" href="{{ route('backend.sender_id.index') }}">{{__('label.senders')}}</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        @if(access()->allow('view_staff') || access()->allow('view_clients') || access()->allow('manage_roles_permissions'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Administration
            </div>

            <li class="nav-item {{
                request()->routeIs('backend.user.*') ||
                request()->routeIs('backend.client.*') ||
                request()->routeIs('backend.role.*') ||
                request()->routeIs('backend.permission.*') ? 'active' : ''
            }}">
                <a class="nav-link collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseAdministration" aria-expanded="{{
                    request()->routeIs('backend.user.*') ||
                    request()->routeIs('backend.client.*') ||
                    request()->routeIs('backend.role.*') ||
                    request()->routeIs('backend.permission.*') ? 'true' : 'false'
                }}" aria-controls="collapseAdministration">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>User management</span>
                </a>
                <div id="collapseAdministration" class="collapse {{
                        request()->routeIs('backend.user.*') ||
                        request()->routeIs('backend.client.*') ||
                        request()->routeIs('backend.role.*') ||
                        request()->routeIs('backend.permission.*') ? 'show' : ''
                    }}" aria-labelledby="headingTwo" data-parent="#sidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">User:</h6>
                        @if(access()->allow('view_clients'))
                            <a class="collapse-item {{ request()->routeIs('backend.client.*') ? 'active' : '' }}" href="{{ route('backend.client.index') }}">
                                {{__('label.client')}}
                            </a>
                        @endif
                        @if(access()->allow('view_staff'))
                            <a class="collapse-item {{ request()->routeIs('backend.user.*') ? 'active' : '' }}" href="{{ route('backend.user.index') }}">
                                {{__('label.staff')}}
                            </a>
                        @endif
                        @if(access()->allow('manage_roles_permissions'))
                            <h6 class="collapse-header">Authorization:</h6>
                            <a class="collapse-item {{ request()->routeIs('backend.role.*') || request()->routeIs('backend.permission.*') ? 'active' : '' }}" href="{{ route('backend.role.index') }}">
                                {{__('role')}}
                            </a>
                            <a class="collapse-item {{ request()->routeIs('backend.permission.*') || request()->routeIs('backend.permission.*') ? 'active' : '' }}" href="{{ route('backend.permission.index') }}">
                                {{__('permission')}}
                            </a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav id="topbar" class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search tickets..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            @if($totalAlerts > 0)
                                <span class="badge badge-danger badge-counter">
                                    {{ $totalAlerts > 9 ? '9+' : $totalAlerts }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">
                                Alerts Center
                            </h6>

                            {{-- Unread Notifications --}}
                            @forelse(user()->unreadNotifications->take(3) as $notification)
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('backend.notifications.show', $notification->id) }}">
                                    <div class="mr-3">
                                        <i class="fas fa-dot-circle text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                        <span class="font-weight-bold">{{ $notification->data['message'] ?? 'New notification' }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="dropdown-item text-center small text-gray-500">
                                    No new notifications
                                </div>
                            @endforelse

                            {{-- Upcoming Sticker Reminders --}}
                            @foreach($upcomingReminders as $reminder)
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('backend.stickers.show', $reminder->uid) }}">
                                    <div class="mr-3">
                                        <i class="fas fa-sticky-note text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">
                                            {{ \Carbon\Carbon::parse($reminder->remind_at)->format('d M Y h:i A') }}
                                        </div>
                                        <span class="font-weight-bold">{!! Purifier::clean($reminder->note) !!}</span>
                                    </div>
                                </a>
                            @endforeach

                            <a class="dropdown-item text-center small text-gray-500" href="{{ route('backend.notifications.index') }}">
                                Show All Alerts
                            </a>
                        </div>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ userFullName() }}</span>
                            @if(auth()->user()->profile_photo_path)
                                <img class="img-profile rounded-circle" src="{{ auth()->user()->profile_photo_url }}" width="32" height="32" alt="Profile">
                            @else
                                <div class="user-avatar">{{ initials() }}</div>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('backend.profile.show') }}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{__('label.profile')}}
                            </a>
                            <a class="dropdown-item" href="{{ route('backend.stickers.index') }}">
                                <i class="fas fa-book-open fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{__('label.yellow_sticker')}}
                            </a>
                            <a class="dropdown-item" href="{{ route('backend.setting.index') }}">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{__('label.settings')}}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{__('label.logout')}}
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <main class="main-content">
                <div class="container-fluid">
                    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render() }}
                    @include('layouts.partials.alerts')
                    @yield('content')
                </div>
            </main>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; {{ config('app.name') }} {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</div>

<script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>

<!-- 2. Bootstrap and dependencies -->
<script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('asset/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- 3. Core plugins -->
<script src="{{ asset('asset/js/sb-admin-2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 4. Select2 (must be after jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- 5. DataTables and other plugins -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<!-- Scroll to top button -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@include('layouts.partials.modal')
@include('layouts.partials.customizer')

@stack('plugins')
@stack('scripts')
</body>
</html>
