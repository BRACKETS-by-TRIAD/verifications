<?php

namespace Brackets\Verifications\Http\Controllers;

use Brackets\Verifications\Verification;
use Illuminate\Support\Facades\View;

// TODO remove this class later
class ExampleController
{
    public function indexInvoices()
    {
        $invoices = Invoice::paginate();

        return View::make('invoices.indexInvoices')->withInvoices($invoices); // v indexInvoices smerujeme akciu na postDownloadInvoice
    }

    // 1.

    // POST
    public function postDownloadInvoice(Invoice $invoice)
    {
        return (new Verification)->verify('download-invoice', '/invoices', function () use ($invoice) {
            return $invoice->download();
        });
    }

    // 2.

    // GET
    public function autoDownloadInvoice(Invoice $invoice)
    {
        return (new Verification)->verify('download-invoice', '/invoices/autoDownloadInvoice/'.$invoice->id, function () use ($invoice) {
            return View::make('invoices.autoDownload')->withInvoice($invoice); // in invoices.autoDownload blade JS calls post ajax postDownloadInvoice
        });
    }

    // POST
    public function postDownloadInvoice2(Invoice $invoice)
    {
        return (new Verification)->verify('download-invoice', '/invoices/autoDownloadInvoice/'.$invoice->id, function () use ($invoice) {
            return $invoice->download();
        });
    }





    // 3.

    // GET
    public function showDownloadInvoice(Invoice $invoice)
    {
        return (new Verification)->verify('download-invoice', '/invoices/showDownloadInvoice/'.$invoice->id, function () use ($invoice) {
            return View::make('invoices.showDownloadInvoice')->withInvoice($invoice); // in invoices.autoDownload blade there is a button which downloads an invoice
        });
    }

    // POST
    public function postDownloadInvoice3(Invoice $invoice)
    {
        return (new Verification)->verify('download-invoice', '/invoices/showDownloadInvoice/'.$invoice->id, function () use ($invoice) {
            return $invoice->download();
        });
    }



    // 4.

    // POST s middlewarom "verifications.verify:download-invoice"
    public function postDownloadInvoice4(Invoice $invoice)
    {
        return $invoice->download();
    }
}
