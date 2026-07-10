<?php

declare(strict_types=1);

final class ReportController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('reports.view');
        $this->view('reports/index', ['title' => 'Laporan']);
    }

    public function sales(): void
    {
        $this->requirePermission('reports.view');
        [$from, $to] = $this->dateRange();
        $branchId = $this->currentBranchId();

        $sales = SaleModel::listForBranch($branchId, $from, $to);
        $sales = array_values(array_filter($sales, fn ($s) => $s['status'] === 'completed'));

        $summary = [
            'trx_count' => count($sales),
            'omzet'     => array_sum(array_column($sales, 'total_amount')),
        ];

        if ($this->get('export') === 'csv') {
            $this->exportCsv($sales, [
                'invoice_no' => 'No. Invoice', 'sale_date' => 'Tanggal', 'customer_name' => 'Pelanggan',
                'total_amount' => 'Total', 'payment_method' => 'Metode Bayar',
            ], 'laporan_penjualan_' . date('Ymd') . '.csv');
        }

        $this->view('reports/sales', [
            'title' => 'Laporan Penjualan', 'sales' => $sales, 'summary' => $summary, 'from' => $from, 'to' => $to,
        ]);
    }

    public function profit(): void
    {
        $this->requirePermission('reports.profit');
        [$from, $to] = $this->dateRange();
        $branchId = $this->currentBranchId();

        $sql = "SELECT DATE(s.sale_date) AS d, SUM(si.subtotal) AS omzet,
                       SUM(si.subtotal - si.cost_price * si.qty) AS laba
                FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id
                WHERE s.status = 'completed' AND DATE(s.sale_date) BETWEEN :from AND :to";
        $params = ['from' => $from, 'to' => $to];
        if ($branchId !== null) {
            $sql .= ' AND s.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $sql .= ' GROUP BY DATE(s.sale_date) ORDER BY d';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $salesProfit = $stmt->fetchAll();

        $pulsaSql = "SELECT DATE(created_at) AS d, SUM(sale_price) AS omzet, SUM(profit_amount) AS laba
                     FROM pulsa_transactions WHERE status = 'success' AND DATE(created_at) BETWEEN :from AND :to";
        $pulsaParams = ['from' => $from, 'to' => $to];
        if ($branchId !== null) {
            $pulsaSql .= ' AND branch_id = :branch_id';
            $pulsaParams['branch_id'] = $branchId;
        }
        $pulsaSql .= ' GROUP BY DATE(created_at) ORDER BY d';
        $pulsaStmt = Database::connection()->prepare($pulsaSql);
        $pulsaStmt->execute($pulsaParams);
        $pulsaProfit = $pulsaStmt->fetchAll();

        $bankSql = "SELECT DATE(created_at) AS d, SUM(amount) AS omzet, SUM(profit_amount) AS laba
                    FROM bank_transactions WHERE status = 'success' AND DATE(created_at) BETWEEN :from AND :to";
        $bankParams = ['from' => $from, 'to' => $to];
        if ($branchId !== null) {
            $bankSql .= ' AND branch_id = :branch_id';
            $bankParams['branch_id'] = $branchId;
        }
        $bankSql .= ' GROUP BY DATE(created_at) ORDER BY d';
        $bankStmt = Database::connection()->prepare($bankSql);
        $bankStmt->execute($bankParams);
        $bankProfit = $bankStmt->fetchAll();

        $totalSalesProfit = array_sum(array_column($salesProfit, 'laba'));
        $totalPulsaProfit = array_sum(array_column($pulsaProfit, 'laba'));
        $totalBankProfit = array_sum(array_column($bankProfit, 'laba'));

        $this->view('reports/profit', [
            'title' => 'Laporan Laba Rugi', 'salesProfit' => $salesProfit, 'pulsaProfit' => $pulsaProfit, 'bankProfit' => $bankProfit,
            'totalSalesProfit' => $totalSalesProfit, 'totalPulsaProfit' => $totalPulsaProfit, 'totalBankProfit' => $totalBankProfit,
            'from' => $from, 'to' => $to,
        ]);
    }

    public function stock(): void
    {
        $this->requirePermission('reports.view');
        $branchId = $this->currentBranchId();
        $products = $branchId !== null ? ProductModel::listForBranch($branchId) : [];

        if ($this->get('export') === 'csv') {
            $this->exportCsv($products, [
                'sku' => 'SKU', 'name' => 'Nama Produk', 'category_name' => 'Kategori',
                'stock_qty' => 'Stok', 'min_stock' => 'Stok Minimum', 'sale_price' => 'Harga Jual',
            ], 'laporan_stok_' . date('Ymd') . '.csv');
        }

        $this->view('reports/stock', [
            'title' => 'Laporan Stok', 'products' => $products,
            'branches' => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
        ]);
    }

    public function pawn(): void
    {
        $this->requirePermission('reports.view');
        $status = $this->get('status');
        $this->view('reports/pawn', [
            'title' => 'Laporan Gadai',
            'pawns' => PawnModel::listForBranch($this->currentBranchId(), $status),
            'status' => $status,
            'outstanding' => PawnModel::totalOutstanding($this->currentBranchId()),
        ]);
    }

    public function loan(): void
    {
        $this->requirePermission('reports.view');
        $status = $this->get('status');
        $this->view('reports/loan', [
            'title' => 'Laporan Pinjaman',
            'loans' => LoanModel::listForBranch($this->currentBranchId(), $status),
            'status' => $status,
            'outstanding' => LoanModel::totalOutstanding($this->currentBranchId()),
        ]);
    }

    public function pulsa(): void
    {
        $this->requirePermission('reports.view');
        [$from, $to] = $this->dateRange();
        $transactions = PulsaTransactionModel::listForBranch($this->currentBranchId(), $from, $to);

        $this->view('reports/pulsa', [
            'title' => 'Laporan Pulsa, Data & Top Up Saldo', 'transactions' => $transactions, 'from' => $from, 'to' => $to,
            'summary' => [
                'count' => count($transactions),
                'omzet' => array_sum(array_column($transactions, 'sale_price')),
                'profit' => array_sum(array_column($transactions, 'profit_amount')),
            ],
        ]);
    }

    public function bank(): void
    {
        $this->requirePermission('reports.view');
        [$from, $to] = $this->dateRange();
        $transactions = BankTransactionModel::listForBranch($this->currentBranchId(), $from, $to);
        $transactions = array_values(array_filter($transactions, fn ($t) => $t['status'] !== 'void'));

        $this->view('reports/bank', [
            'title' => 'Laporan Transfer / Setor Tunai', 'transactions' => $transactions, 'from' => $from, 'to' => $to,
            'summary' => [
                'count'  => count($transactions),
                'amount' => array_sum(array_column($transactions, 'amount')),
                'profit' => array_sum(array_column($transactions, 'profit_amount')),
            ],
        ]);
    }

    public function service(): void
    {
        $this->requirePermission('reports.view');
        $status = $this->get('status');
        $orders = ServiceOrderModel::listForBranch($this->currentBranchId(), $status);

        $this->view('reports/service', [
            'title'  => 'Laporan Servis HP', 'orders' => $orders, 'status' => $status,
            'summary' => [
                'count'      => count($orders),
                'final_cost' => array_sum(array_column($orders, 'final_cost')),
                'paid'       => array_sum(array_column($orders, 'paid_amount')),
            ],
        ]);
    }

    /** @return array{0:string,1:string} */
    private function dateRange(): array
    {
        $from = $this->get('from') ?: date('Y-m-01');
        $to = $this->get('to') ?: date('Y-m-d');
        return [$from, $to];
    }
}
