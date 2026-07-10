<?php

declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('dashboard.view');

        $branchId = $this->currentBranchId();
        $canSeeProfit = Auth::can('reports.profit');

        $data = [
            'title'       => 'Dashboard',
            'salesToday'  => SaleModel::todaySummary($branchId),
            'pulsaToday'  => PulsaTransactionModel::todaySummary($branchId),
            'bankToday'   => BankTransactionModel::todaySummary($branchId),
            'pawnActive'  => PawnModel::countByStatus($branchId, 'active'),
            'pawnOutstanding' => PawnModel::totalOutstanding($branchId),
            'loanActive'  => LoanModel::countByStatus($branchId, 'active'),
            'loanOutstanding' => LoanModel::totalOutstanding($branchId),
            'serviceOpen' => ServiceOrderModel::countByStatus($branchId, 'received')
                + ServiceOrderModel::countByStatus($branchId, 'in_progress')
                + ServiceOrderModel::countByStatus($branchId, 'waiting_parts'),
            'serviceReady' => ServiceOrderModel::countByStatus($branchId, 'completed'),
            'lowStock'    => ProductModel::lowStock($branchId),
            'canSeeProfit'=> $canSeeProfit,
            'branches'    => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
        ];

        $this->view('dashboard/index', $data);
    }
}
