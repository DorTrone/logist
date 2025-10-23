<nav class="navbar navbar-expand-lg navbar-dark bg-black" aria-label="Navbar">    
    <div class="container-fluid">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">@lang('app.appShortName')</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse position-relative" id="navbar">
            <ul class="navbar-nav me-auto">
                @canany(['packagesPanel', 'visitorsPanel', 'adminPanel', 'errors', 'tokens'])
                    <li class="nav-item dropdown">
                        <a class="nav-link ps-lg-3 link-primary dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-bar-chart"></i> @lang('app.panels')
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @can('packagesPanel')
                                <li><a class="dropdown-item" href="{{ route('admin.packagesPanel.index') }}">@lang('app.packagesPanel')</a></li>
                            @endcan
                            @can('visitorsPanel')
                                <li><a class="dropdown-item" href="{{ route('admin.visitorsPanel.index') }}">@lang('app.visitorsPanel')</a></li>
                            @endcan
                            @can('adminPanel')
                                <li><a class="dropdown-item" href="{{ route('admin.adminPanel.index') }}">@lang('app.adminPanel')</a></li>
                            @endcan
                            @can('errors')
                                <li><a class="dropdown-item" href="{{ route('admin.errors.index') }}">@lang('app.errors')</a></li>
                            @endcan
                            @can('tokens')
                                <li><a class="dropdown-item" href="{{ route('admin.tokens.index') }}">@lang('app.tokens')</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['packages', 'transports', 'customers', 'verifications', 'contacts'])
                    <li class="nav-item dropdown">
                        <a class="nav-link ps-lg-3 link-warning dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-box"></i> @lang('app.packages')
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @can('packages')
                                <li><a class="dropdown-item" href="{{ route('admin.packages.index') }}">@lang('app.packages')</a></li>
                            @endcan
                            @can('transports')
                                <li><a class="dropdown-item" href="{{ route('admin.transports.index') }}">@lang('app.transports')</a></li>
                            @endcan
                            @can('customers')
                                <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}">@lang('app.customers')</a></li>
                            @endcan
                            @can('verifications')
                                <li><a class="dropdown-item" href="{{ route('admin.verifications.index') }}">@lang('app.verifications')</a></li>
                            @endcan
                            @can('contacts')
                                <li><a class="dropdown-item" href="{{ route('admin.contacts.index') }}">@lang('app.contacts')</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- ✅ New Warehouses menu --}}
                <li class="nav-item dropdown">
                    <a class="nav-link ps-lg-3 link-success dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi-building"></i> Warehouses
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('admin.warehouses.index') }}">All Warehouses</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.warehouses.create') }}">Add New</a></li>
                    </ul>
                </li>

                @canany(['banners', 'notifications', 'pushNotifications'])
                    <li class="nav-item dropdown">
                        <a class="nav-link ps-lg-3 link-light dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-app-indicator"></i> @lang('app.notifications')
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @can('banners')
                                <li><a class="dropdown-item" href="{{ route('admin.banners.index') }}">@lang('app.banners')</a></li>
                            @endcan
                            @can('notifications')
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index') }}">@lang('app.notifications')</a></li>
                            @endcan
                            @can('pushNotifications')
                                <li><a class="dropdown-item" href="{{ route('admin.pushNotifications.index') }}">@lang('app.pushNotifications')</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['tasks', 'users', 'configs'])
                    <li class="nav-item dropdown">
                        <a class="nav-link ps-lg-3 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-gear"></i> @lang('app.settings')
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @can('tasks')
                                <li><a class="dropdown-item" href="{{ route('admin.tasks.index') }}">@lang('app.tasks')</a></li>
                            @endcan
                            @can('users')
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">@lang('app.users')</a></li>
                            @endcan
                            @can('configs')
                                <li><a class="dropdown-item" href="{{ route('admin.configs.edit') }}">@lang('app.configs')</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['ipAddresses', 'userAgents', 'authAttempts', 'visitors'])
                    <li class="nav-item dropdown">
                        <a class="nav-link ps-lg-3 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-geo-alt"></i> @lang('app.visitors')
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @can('ipAddresses')
                                <li><a class="dropdown-item" href="{{ route('admin.ipAddresses.index') }}">@lang('app.ipAddresses')</a></li>
                            @endcan
                            @can('userAgents')
                                <li><a class="dropdown-item" href="{{ route('admin.userAgents.index') }}">@lang('app.userAgents')</a></li>
                            @endcan
                            @can('authAttempts')
                                <li><a class="dropdown-item" href="{{ route('admin.authAttempts.index') }}">@lang('app.authAttempts')</a></li>
                            @endcan
                            @can('visitors')
                                <li><a class="dropdown-item" href="{{ route('admin.visitors.index') }}">@lang('app.visitors')</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <li class="nav-item dropdown">
                    <a class="nav-link ps-lg-3 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi-translate"></i> @lang('app.language')
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('locale', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('locale', 'tm') }}">Türkmen</a></li>
                        <li><a class="dropdown-item" href="{{ route('locale', 'ru') }}">Русский</a></li>
                        <li><a class="dropdown-item" href="{{ route('locale', 'cn') }}">Chinese</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout').submit();">
                        <i class="bi-box-arrow-right"></i> @lang('app.logout')
                    </a>
                    <form method="POST" action="{{ route('logout') }}" id="logout" class="d-none">@csrf</form>
                </li>
            </ul>
        </div>
    </div>
</nav>
