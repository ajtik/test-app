<?php

declare(strict_types=1);

namespace Netvor\Invoice\Form\InvoiceForm;

use Netvor\Invoice\Model\Entities\Client;
use Netvor\Invoice\Model\Entities\Invoice;

interface InvoiceFormFactory
{
	public function create(Client $client, ?Invoice $invoice = null): InvoiceForm;
}
