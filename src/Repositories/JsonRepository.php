<?php


namespace PatrickRose\Invoices\Repositories;


use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\JsonTrait;

class JsonRepository implements InvoiceRepositoryInterface
{

    use JsonTrait;

    /**
     * @var Invoice[]
     */
    private $invoices = [];

    public function __construct($filename)
    {
        $invoices = $this->readAndLockFile($filename);

        foreach ($invoices as $invoice) {
            $this->invoices[] = new Invoice(
                $invoice['reference'],
                $invoice['payee'],
                $invoice['date'],
                $invoice['fees'],
                $invoice['expenses']
            );
        }
    }

    /**
     * Add an invoice to the repository
     *
     * @param Invoice $invoice The invoice the add
     * @return bool True if add was successful
     */
    public function add(Invoice $invoice): bool
    {
        $this->invoices[] = $invoice;

        return true;
    }

    /**
     * Get all invoices from this repository
     *
     * @return Invoice[]
     */
    public function getAll(): array
    {
        return $this->invoices;
    }

    public function __destruct()
    {
        $toWrite = [];

        foreach ($this->invoices as $invoice) {
            $toWrite[] = $invoice->toArray();
        }

        $this->writeAndUnlockFile($toWrite);
    }

    /**
     * Instantiate this repository based on the given
     *
     * @param array $config
     * @return InvoiceRepositoryInterface
     */
    public static function instantiate(array $config): InvoiceRepositoryInterface
    {
        return new JsonRepository($config['filename']);
    }
}
