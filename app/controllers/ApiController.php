<?php

declare(strict_types=1);

/**
 * Endpoint internal (JSON) yang dipakai oleh JavaScript sisi klien (fetch),
 * misalnya pencarian produk di layar kasir. Lihat docs/API.md.
 */
final class ApiController extends Controller
{
    public function product_search(): void
    {
        $this->requireLogin();
        if (!Auth::can('sales.create') && !Auth::can('purchases.manage')) {
            $this->json(['data' => []], 403);
        }

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->json(['data' => []]);
        }

        $keyword = $this->get('q');
        $products = ProductModel::search($branchId, $keyword, 20);
        $this->json(['data' => $products]);
    }

    public function customer_search(): void
    {
        $this->requireLogin();
        $keyword = $this->get('q');
        if (strlen($keyword) < 2) {
            $this->json(['data' => []]);
        }
        $customers = CustomerModel::search($keyword, $this->currentBranchId(), 15);
        $this->json(['data' => $customers]);
    }
}
