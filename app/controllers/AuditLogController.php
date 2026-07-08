<?php

declare(strict_types=1);

final class AuditLogController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('audit.view');

        $filters = [
            'module'    => $this->get('module'),
            'branch_id' => $this->get('branch_id'),
            'from'      => $this->get('from'),
            'to'        => $this->get('to'),
        ];
        if (!Auth::isSuperAdmin()) {
            $filters['branch_id'] = (string) $this->currentBranchId();
        }

        $this->view('audit_log/index', [
            'title'   => 'Log Audit',
            'logs'    => AuditLogModel::search(array_filter($filters, fn ($v) => $v !== ''), 200),
            'filters' => $filters,
            'branches'=> Auth::isSuperAdmin() ? BranchModel::activeList() : [],
        ]);
    }
}
