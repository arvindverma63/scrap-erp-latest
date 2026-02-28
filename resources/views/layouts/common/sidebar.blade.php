<style>
    .startbar {
        transition: all 0.3s ease;
    }

    .startbar.hide {
        transform: translateX(-100%);
        opacity: 0;
    }

    #closeSidebar {
        display: none
    }

    @media (max-width: 992px) {
        #closeSidebar {
            display: block
        }
    }
</style>
<!-- leftbar-tab-menu -->
<div class="startbar d-print-none" id="startbar"
     style="background-color: {{auth()->user()->roles()->first()?->color}};">
    <!--start brand-->
    <div class="brand">
        <a href="{{route('admin.dashboard')}}" class="logo">
            <span>
                <img src="{{ App\Models\Setting::where('key', 'admin_logo')->first()->value }}" alt="logo-small"
                     class="logo-sm">
            </span>
            <span class="">
                <img src="{{ asset('assets/images/cm-logo.png') }}" alt="logo-large" class="logo-lg logo-light">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
        <button class="btn btn-sm btn-light close-btn position-absolute top-0 end-0 m-2 border-0 shadow-none bg-white"
                id="closeSidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label mt-2">
                        <span>Main</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="iconoir-report-columns menu-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li><!--end nav-item-->
                    @can('buying_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.orders.purchase.index') }}">
                                <i class="iconoir-hand-card menu-icon"></i>
                                <span>Purchase Receipts</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('selling_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.orders.selling.index') }}">
                                <i class="iconoir-paste-clipboard menu-icon"></i>
                                <span>Selling Invoice</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @if(in_array(auth()->user()->roles->first()->id, [1, 5]))
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{url('admin/reports?web_page=RECEIPT&from_date='.now()->format('Y-m-d').'&to_date='.now()->format('Y-m-d'))}}">
                            <i class="iconoir-stats-report menu-icon"></i>
                            <span>Daily Report</span>
                        </a>
                    </li>
                    @endif

                    @canany(['suppliers_read', 'customer_read', 'wallet_approve', 'wallet_deposit',
                                'cash_count','buying_read', 'invoices_read', 'payment_read'])
                        <li class="menu-label mt-2">
                            <span>Sales</span>
                        </li>
                    @endcan

                    @can('suppliers_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.supplier.index') }}">
                                <i class="iconoir-hand-cash menu-icon"></i>
                                <span>Suppliers</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('customer_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.buyers.index') }}">
                                <i class="iconoir-community menu-icon"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                    @endcan

                    @can('wallet_approve')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.wallets.index') }}">
                                <i class="iconoir-wallet menu-icon"></i>
                                <span>Wallet</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('wallet_deposit')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.wallets.deposit') }}">
                                <i class="iconoir-money-square menu-icon"></i>
                                <span>Wallet Deposit</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('cash_count')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.wallets.cash-count') }}">
                                <i class="iconoir-bank menu-icon"></i>
                                <span>Cash Count</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('buying_read')
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarPurchase" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarPurchase">
                                <i class="iconoir-task-list menu-icon"></i>
                                <span>Receipts</span>
                            </a>
                            <div class="collapse " id="sidebarPurchase">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.po.pendingReport') }}">Pending
                                            Receipts</a>
                                    </li><!--end nav-item-->

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.po.voidedReport') }}">Voided
                                            Receipts</a>
                                    </li><!--end nav-item-->

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.po.completedReport') }}">Completed
                                            Receipts</a>
                                    </li><!--end nav-item-->
                                </ul><!--end nav-->
                            </div><!--end startbarTables-->
                        </li><!--end nav-item-->
                    @endcan

                    @can('invoices_read')
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarTransactions" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarTransactions">
                                <i class="iconoir-task-list menu-icon"></i>
                                <span>Invoices</span>
                            </a>
                            <div class="collapse " id="sidebarTransactions">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.sales.pendingReport') }}">Pending
                                            Invoices</a>
                                    </li><!--end nav-item-->

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.sales.voidedReport') }}">Voided
                                            Invoices</a>
                                    </li><!--end nav-item-->

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.sales.completedReport') }}">Completed
                                            Invoices</a>
                                    </li><!--end nav-item-->
                                </ul><!--end nav-->
                            </div><!--end startbarTables-->
                        </li><!--end nav-item-->
                    @endcan

                    @can('payment_read')
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarPayments" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarPayments">
                                <i class="iconoir-task-list menu-icon"></i>
                                <span>Payments</span>
                            </a>
                            <div class="collapse " id="sidebarPayments">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.payments.index') }}">Purchase Payments
                                        </a>
                                    </li><!--end nav-item-->
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.payments.sales.index') }}">Sales
                                            Payments
                                        </a>
                                    </li><!--end nav-item-->
                                </ul><!--end nav-->
                            </div><!--end startbarTables-->
                        </li><!--end nav-item-->
                    @endcan



                    @can('products_read')
                        <li class="menu-label mt-2">
                            <span>Products</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">
                                <i class="iconoir-box-iso menu-icon"></i>
                                <span>Products </span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan
                    {{--
                    @can('product_category_read')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.product_categories.index')}}">
                            <i class="iconoir-tags menu-icon"></i>
                            <span>Product Categories</span>
                        </a>
                    </li><!--end nav-item-->
                    @endcan --}}




                    @can('inventory_management_read')
                        <li class="menu-label mt-2">
                            <span>Inventory</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.inventories.index') }}">
                                <i class="iconoir-task-list menu-icon"></i>
                                <span>Inventory management </span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('low_stock_alert_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.stock-alert.index') }}">
                                <i class="iconoir-calendar menu-icon"></i>
                                <span>stock Alert</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    @can('audit_read')
                        <li class="menu-label mt-2">
                            <span>Audit</span>
                        </li>
                    @endcan

                    @can('audit_read')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.audit.index') }}">
                                <i class="iconoir-paste-clipboard menu-icon"></i>
                                <span>Activity Logs</span>
                            </a>
                        </li><!--end nav-item-->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.audit.ledger', ['type' => 'Purchase']) }}">
                                <i class="iconoir-stats-report menu-icon"></i>
                                <span>Ledger Reports</span>
                            </a>
                        </li><!--end nav-item-->
                    @endcan

                    <li class="menu-label mt-2">
                        <span>Notification</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                            <i class="iconoir-box-iso menu-icon"></i>
                            <span>Top-up </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.notifications.payments') }}">
                            <i class="iconoir-box-iso menu-icon"></i>
                            <span>Invoice/Receipt </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.notifications.stocks') }}">
                            <i class="iconoir-box-iso menu-icon"></i>
                            <span>Stock Notifications </span>
                        </a>
                    </li>

                    @canany(['user_setting_read', 'setting_general_read'])
                        <li class="menu-label mt-2">
                            <span>Settings</span>
                        </li>
                    @endcanany

                    @canany(['user_setting_create', 'user_setting_read', 'user_setting_update'])
                        <li class="nav-item">
                            <a class="nav-link" href="#rolesPermissions" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="rolesPermissions">
                                <i class="iconoir-task-list menu-icon"></i>
                                <span>Roles & Permission</span>
                            </a>
                            <div class="collapse " id="rolesPermissions">
                                <ul class="nav flex-column">
                                    @if(auth()->user()->roles->first()?->name === 'super-admin')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.roles.index') }}">Roles
                                            </a>
                                        </li><!--end nav-item-->

                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{ route('admin.permissions.index') }}">Permission</a>
                                        </li><!--end nav-item-->

                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.permission-groups.index') }}">Permissions
                                                Groups
                                            </a>
                                        </li><!--end nav-item-->
                                    @endif
                                    @can('user_setting_read')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.users.index') }}">Users
                                                Settings
                                            </a>
                                        </li><!--end nav-item-->
                                    @endcan
                                </ul><!--end nav-->
                            </div><!--end startbarTables-->
                        </li><!--end nav-item-->
                    @endcanany

                    @canany(['setting_general_read', 'setting_invoice_read', 'setting_notification_read', 'setting_weight_unit_read'])
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarElements" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarElements">
                                <i class="iconoir-compact-disc menu-icon"></i>
                                <span>Settings</span>
                            </a>
                            <div class="collapse " id="sidebarElements">
                                <ul class="nav flex-column">
                                    @can('setting_general_read')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.settings.general.index') }}">General</a>
                                        </li><!--end nav-item-->
                                    @endcan
                                    @can('setting_weight_unit_read')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.weight_units.index') }}">
                                                Weight Units</a>
                                        </li><!--end nav-item-->
                                    @endcan
                                    @can('setting_invoice_read')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin.settings.invoice') }}">Invoice &
                                                Receipt</a>
                                        </li><!--end nav-item-->
                                    @endcan
                                    @can('setting_notification_read')
                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{ route('admin.settings.notification_settings') }}">Notifications</a>
                                        </li><!--end nav-item-->
                                    @endcan

                                    @can('low_stock_alert_read')
                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{ route('admin.stock-settings.index') }}">Low/High Alert</a>
                                        </li><!--end nav-item-->
                                    @endcan
                                </ul><!--end nav-->
                            </div><!--end startbarElements-->
                        </li><!--end nav-item-->
                    @endcanany
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cache.clear') }}">
                            <i class="iconoir-refresh menu-icon"></i>
                            <span>clear cache</span>
                        </a>
                    </li>
                </ul><!--end navbar-nav--->
            </div>
        </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->
</div><!--end startbar-->