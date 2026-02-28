<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\MaterialsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\POController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductsCategoriesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\SupplierController;
use App\Mail\OrderMail;
use App\Models\SellingOrder;
use App\Models\Setting;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardContoller;
use App\Http\Controllers\Orders\PurchaseOrderController;
use App\Http\Controllers\Orders\SellingOrderController;
use App\Http\Controllers\Payments\PurchasePaymentController;
use App\Http\Controllers\Payments\SalesPaymentController;
use App\Http\Controllers\RolesAndPermission\PermissionController;
use App\Http\Controllers\RolesAndPermission\PermissionGroupController;
use App\Http\Controllers\RolesAndPermission\RoleController;
use App\Http\Controllers\RolesAndPermission\UserController;
use App\Http\Controllers\Settings\GeneralSettingController;
use App\Http\Controllers\Settings\InvoiceSettingController;
use App\Http\Controllers\Settings\NotificationController;
use App\Http\Controllers\Settings\ReceiptController;
use App\Http\Controllers\Settings\StockAlertSettingController;
use App\Http\Controllers\Settings\WeightUnitController;
use App\Http\Controllers\WalletController;
use App\Models\Product;
use App\Repositories\SupplierRepository;
use App\Allmails\SendInvoiceMail;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Mail;

// make sure this is installed and aliased correctly

Route::get('/', function () {
    if (!empty(auth()->user()->id)) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/test-mail', function () {
    try {
        $pdf = Pdf::loadView('emails.purchase-order-attachment', ['data' => '123']);
        // 2. Save PDF into public folder
        $fileName = 'order_' . time() . '.pdf';
        $filePath = public_path('pdf/' . $fileName);
        // Create directory if not exists
        if (!file_exists(public_path('pdf'))) {
            mkdir(public_path('pdf'), 0777, true);
        }
        file_put_contents($filePath, $pdf->output());
        $data = ['name' => 'testing 123'];
        // 3. Send email with attachment
        Mail::to('souravgupta5656@gmail.com')
            ->send(new OrderMail($data, $filePath));

        return 'mail send';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});
Route::get('/hello',function (){
    return 'hello';
});
Route::get('/check-db-host', function () {
    
    $host = config('database.connections.mysql.host');
    $resolvedIp = gethostbyname($host);

    try {
        $pdo = DB::connection()->getPdo();
        $connectionStatus = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    } catch (\Exception $e) {
        $connectionStatus = 'Not Connected';
    }

    return response()->json([
        'db_host'        => $host,
        'resolved_ip'    => $resolvedIp,
        'connection_status' => $connectionStatus,
    ]);
});


Route::get('/send-test-mail', function () {
    try {
        $to = 'abd@gmail.com';
        $order = SellingOrder::with(['customer', 'items.product', 'items.weightUnit'])
            ->find(45);
        $setting = Setting::all();
        $pdfContent = SnappyPdf::loadView('emails.invoicemailattachment', compact('order', 'setting'))
            ->setPaper('a4')
            ->setOption('orientation', 'Portrait')
            ->output(); // output returns raw PDF content
        Mail::to('souravgupta5656@gmail.com')->send(new SendInvoiceMail($order, $setting, $pdfContent));
        return "Test email sent to " . $to;
    } catch (\Exception $e) {
        return $e->getMessage() . '_' . $e->getFile();
    }
});

Route::get('send-msg', function () {
    $to = '+917061774399';
    //    $url = url('supplier/receipt/61');
    $url = 'https://lab5.website.work/scraperp/public/supplier/receipt/61';

    $message = 'Testing twillo msg from invoidea ScrapErp Testing ' . $url;
    //    $message = 'Testing Msg from Laravel: https://lab5.invoidea.work/scraperp/public/supplier/receipt/61';
    try {
        $twilio = new TwilioService();
        $twilio->sendSms($to, $message);
        return 'send msg';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::get('/cc', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    return redirect()->route('admin.dashboard')->with('info', 'Cache clear!');

    return 'Cache cleared successfully!';
})->middleware('auth')->name('cache.clear');

Route::get('queue-work', function () {
    Artisan::call('queue:work --once');

    return 'running!';
});

Route::get('supplier/receipt/{id}', [PurchaseOrderController::class, 'downloadInvoice']);
Route::get('customer/invoice/{id}', [SellingOrderController::class, 'downloadSalesInvoice']);
Route::prefix('admin')->as('admin.')->middleware(['auth'])->group(function () {

    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');


    Route::get('/dashboard', [DashboardContoller::class, 'index'])->name('dashboard');
    Route::get('/inventory-report-data', [DashboardContoller::class, 'inventoryReportData'])->name('inventory.reportData');
    Route::get('/reports', [DashboardContoller::class, 'reports'])->name('reports');
    Route::get('/reports-receipt-download', [DashboardContoller::class, 'receiptDownload'])->name('reports.receipt-download');
    Route::get('/reports-invoice-download', [DashboardContoller::class, 'invoiceDownload'])->name('reports.invoice-download');

    Route::get('test-dashboard', function () {
//        $notification = ['message' => 'ok 1', 'alert-type' => 'success'];
//        return redirect()->route('admin.dashboard')->with($notification);
        return redirect()->route('admin.dashboard')->with('success', 'Customer deleted successfully.');
    });

    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');


    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::get('/supplier/show/{id}', [SupplierController::class, 'show'])->name('supplier.show');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    Route::post('/storeOnCreateReceipt', [SupplierController::class, 'storeOnCreateReceipt'])->name('supplier.storeOnCreateReceipt');

    Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers.index');
    Route::get('/buyers/create', [BuyerController::class, 'create'])->name('buyers.create');
    Route::post('/buyers', [BuyerController::class, 'store'])->name('buyers.store');
    Route::get('/buyers/{id}', [BuyerController::class, 'show'])->name('buyers.show');
    Route::get('/buyers/{id}/edit', [BuyerController::class, 'edit'])->name('buyers.edit');
    Route::put('/buyers/{id}', [BuyerController::class, 'update'])->name('buyers.update');
    Route::get('/customer/{id}', [BuyerController::class, 'getCustomer'])->name('buyer.get');
    Route::delete('/buyers/{id}', [BuyerController::class, 'destroy'])->name('buyers.destroy');
    Route::post('/onInvoiceCreate', [BuyerController::class, 'onInvoiceCreate'])->name('buyers.onInvoiceCreate');


    // Route::get('/purchase-order', [POController::class, 'index'])->name('PO.index');
    // Route::get('/purchase-order/create', [POController::class, 'create'])->name('PO.create');

    Route::get('/SalesInvoice', [SalesInvoiceController::class, 'index'])->name('sales_invoices.index');

    Route::get('/payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('/payments/sales', [PaymentsController::class, 'salesIndex'])->name('payments.sales.index');

    Route::get('/products/categories', [ProductsCategoriesController::class, 'index'])->name('product_categories.index');
    Route::post('/products/categories', [ProductsCategoriesController::class, 'store'])->name('product_categories.store');
    Route::get('/products/categories/{product_category}/edit', [ProductsCategoriesController::class, 'edit'])->name('product_categories.edit');
    Route::put('/products/categories/{product_category}', [ProductsCategoriesController::class, 'update'])->name('product_categories.update');
    Route::delete('/products/categories/{product_category}', [ProductsCategoriesController::class, 'destroy'])->name('product_categories.destroy');

    Route::get('/materials', [MaterialsController::class, 'index'])->name('materials.index');

    Route::resource('inventories', InventoriesController::class);
    Route::patch('/inventories/update-status/{id}', [PurchaseOrderController::class, 'updateStatusPurchase'])->name('order.updateStatus');
    Route::get('/inventories/update-status-voided/{id}', [PurchaseOrderController::class, 'updateStatusPurchaseVoided'])->name('order.voided');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/stock-alerts', [StockAlertController::class, 'index'])->name('stock-alert.index');

    Route::get('/audits', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/audits/ledger', [AuditController::class, 'ledger'])->name('audit.ledger');

    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->names('orders.purchase');

    Route::get('/admin/orders/purchase/{id}/invoice', [PurchaseOrderController::class, 'downloadInvoice'])
        ->name('orders.purchase.invoice');
    Route::get('/admin/orders/sales/{id}/invoice', [SellingOrderController::class, 'downloadSalesInvoice'])
        ->name('orders.sales.invoice');


    Route::get('/receipt/view/{id}', [PurchaseOrderController::class, 'receiptPage'])->name('receiptPage');
    Route::get('/invoice/view/{id}', [SellingOrderController::class, 'invoicePage'])->name('invoicePage');

    Route::patch('/invoice/updateStatus/{id}', [SellingOrderController::class, 'updateStatus'])->name('invoice.updateStatus');

    Route::get('/selling', [SellingOrderController::class, 'index'])->name('orders.selling.index');
    Route::get('/selling/create', [SellingOrderController::class, 'create'])->name('orders.selling.create');
    Route::post('/selling', [SellingOrderController::class, 'store'])->name('orders.selling.store');
    Route::put('/selling', [SellingOrderController::class, 'update'])->name('orders.selling.update');
    Route::delete('/selling', [SellingOrderController::class, 'destory'])->name('orders.selling.delete');
    Route::get('/selling-orders/{id}/complete', [SellingOrderController::class, 'updateStatusSales'])
        ->name('selling-orders.complete');
    Route::get('/selling-orders/{id}/voided', [SellingOrderController::class, 'updateStatusVoided'])
        ->name('selling-orders.voided');

    Route::get('/sales/pending', [SalesInvoiceController::class, 'pendingReport'])->name('sales.pendingReport');
    Route::get('/sales/completed', [SalesInvoiceController::class, 'completedReport'])->name('sales.completedReport');
    Route::get('/sales/voided', [SalesInvoiceController::class, 'voidedReport'])->name('sales.voidedReport');

    Route::get('/purchase-orders-reports/pending', [POController::class, 'pendingReport'])->name('po.pendingReport');
    Route::get('/purchase-orders-reports/completed', [POController::class, 'completedReport'])->name('po.completedReport');
    Route::get('/purchase-orders-reports/voided', [POController::class, 'voidedReport'])->name('po.voidedReport');

    Route::get('/weight-units', [WeightUnitController::class, 'index'])->name('weight_units.index');
    Route::post('/weight-units', [WeightUnitController::class, 'store'])->name('weight_units.store');
    Route::get('/weight-units/{id}/edit', [WeightUnitController::class, 'edit'])->name('weight_units.edit');
    Route::put('/weight-units/{id}', [WeightUnitController::class, 'update'])->name('weight_units.update');
    Route::delete('/weight-units/{id}', [WeightUnitController::class, 'destroy'])->name('weight_units.destroy');


    Route::get('/notification-settings', [SettingsController::class, 'notification_settings'])->name('settings.notification_settings');
    Route::get('/user-settings', [SettingsController::class, 'user_settings'])->name('settings.user_settings');

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/stocks', [NotificationController::class, 'stocks'])->name('notifications.stocks');
        Route::get('/payments', [NotificationController::class, 'payments'])->name('notifications.payments');
        Route::post('/', [NotificationController::class, 'store'])->name('notifications.store');
        Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::get('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
    Route::get('check/notifications', [NotificationController::class, 'checkNotification'])->name('check.notifications');
    Route::get('updateStatus/notifications', [NotificationController::class, 'updateStatusNotification'])->name('updateStatus.notifications');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/invoice', [InvoiceSettingController::class, 'index'])->name('invoice');
        Route::put('/invoice', [InvoiceSettingController::class, 'update'])->name('invoice.update');
        Route::get('/general-settings', [GeneralSettingController::class, 'index'])->name('general.index');
        Route::put('/currency-symbol', [GeneralSettingController::class, 'update'])->name('general.update');
    });


    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::resource('users', UserController::class);

    Route::resource('permissions', PermissionController::class);

    Route::resource('permission-groups', PermissionGroupController::class);

    Route::prefix('purchase-payments')->name('purchase-payments.')->group(function () {
        Route::get('/', [PurchasePaymentController::class, 'index'])->name('index');
        Route::post('/', [PurchasePaymentController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchasePaymentController::class, 'show'])->name('show');
        Route::put('/{id}', [PurchasePaymentController::class, 'update'])->name('update');
        Route::delete('/{id}', [PurchasePaymentController::class, 'destroy'])->name('destroy');
    });

    Route::name('sales-payments.')->group(function () {
        Route::get('/sales-payments', [SalesPaymentController::class, 'index'])->name('index');
        Route::post('/sales-payments', [SalesPaymentController::class, 'store'])->name('store');
        Route::get('/sales-payments/{id}', [SalesPaymentController::class, 'show'])->name('show');
        Route::put('/sales-payments/{id}', [SalesPaymentController::class, 'update'])->name('update');
        Route::delete('/sales-payments/{id}', [SalesPaymentController::class, 'destroy'])->name('destroy');
    });
    Route::get('/wallets', [WalletController::class, 'walletsPage'])->name('wallets.index');
    Route::post('/wallets/{id}/approve', [WalletController::class, 'approveTopup'])->name('wallets.approve');
    Route::post('/wallets/{id}/reject', [WalletController::class, 'rejectTopup'])->name('wallets.reject');
    Route::post('/wallets/request-topup', [WalletController::class, 'requestTopup'])->name('wallets.requestTopup');
    Route::get('wallets/deposit', [WalletController::class, 'deposit'])->name('wallets.deposit');
    Route::post('wallets/deposit', [WalletController::class, 'createDeposit'])->name('wallets.create-deposit');
    Route::get('wallets/cash-count', [WalletController::class, 'cashCount'])->name('wallets.cash-count');
    Route::post('wallets/create-cash-count', [WalletController::class, 'createCashCount'])->name('wallets.create-cash-count');
    Route::get('wallets/cash-count-download/{id}', [WalletController::class, 'downloadCashCount'])->name('wallets.cash-count-download');

//    Route::post('wallets/update-deposit', [WalletController::class, 'updateDeposit'])->name('wallets.update-deposit');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::get('product/get/{id}', [ProductController::class, 'getProduct'])
        ->name('product.get');

    Route::get('/import/supplier', function () {
        return view('layouts.suppliers.import');
    })->name('suppliers.import');

    Route::post('/import/supplier/create', [SupplierController::class, 'importSuppliers'])->name('supplier.import.csv');
    Route::get('/import/customer', function () {
        return view('layouts.buyers.import');
    })->name('customers.import');

    Route::post('/customer/import/csv', [BuyerController::class, 'importCsv'])->name('customer.import.csv');


    Route::get('/product/import', [ProductController::class, 'import'])->name('product.import.index');
    Route::post('import/product/csv', [ProductController::class, 'importCsv'])->name('product.import.csv');

    Route::post('suppliers/{id}/status', [SupplierController::class, 'updateStatus'])->name('suppliers.updateStatus');
    Route::post('customers/{id}/status', [BuyerController::class, 'updateStatus'])->name('customers.updateStatus');

    Route::get('/stock-alert-settings', [StockAlertSettingController::class, 'index'])->name('stock-settings.index');
    Route::put('/settings/stock-alert', [StockAlertSettingController::class, 'update'])->name('stock-alert.update');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/admin/dashboard'); // already logged in
    }
    return view('auth.login');
})->name('login');


Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');


Route::get('/send-test', function () {
    Mail::raw('Mailtrap test email content.', function ($message) {
        $message->to('arvindverma630635@gmail.com')
            ->subject('Testing Mailtrap');
    });

    return 'Email sent!';
});
