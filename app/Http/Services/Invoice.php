<?php

namespace App\Http\Services;

use App\Models\PurchaseOrder;
use App\Models\SellingOrder;
use App\Models\Setting;
use PDF;

class Invoice
{
    public function downloadInvoice($id)
    {
        $order = PurchaseOrder::with(['supplier', 'orderItems.product', 'orderItems.weightUnit'])
            ->findOrFail($id);

        // Generate invoice number
        $invoiceNumber = 'PO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $setting = Setting::all();
        $pdf = \PDF::loadView('invoices.purchase-order', compact('order', 'invoiceNumber', 'setting'))
            ->setPaper([0, 0, 230, 800], 'portrait')
            // 80mm paper width
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        $filename = 'Receipt-' . $invoiceNumber . '.pdf';

        // Stream for modal display, download otherwise
        if (request()->query('action') === 'view') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function downloadSalesInvoice($id)
    {
        $order = SellingOrder::with(['customer', 'items.product', 'items.weightUnit', 'cashier'])
            ->findOrFail($id);

        // Generate invoice number
        $invoiceNumber = 'CN-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $setting = Setting::all();

        $pdf = PDF::loadView('invoices.sales-order', compact('order', 'invoiceNumber', 'setting'))
            ->setPaper('A4', 'portrait')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        $filename = 'Invoice-' . $invoiceNumber . '.pdf';

        // Check for a query parameter to decide between stream or download
        if (request()->query('action') === 'view') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}