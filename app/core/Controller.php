<?php
/**
 * Base Controller. Semua controller aplikasi mewarisi kelas ini untuk
 * mendapatkan akses ke rendering view, CSRF, auth guard, dan helper input.
 */

declare(strict_types=1);

abstract class Controller
{
    public function __construct()
    {
        Auth::checkSessionTimeout();
    }

    /** Render view dengan layout utama. */
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data, EXTR_SKIP);
        $csrfField = Csrf::field();
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new RuntimeException("View tidak ditemukan: {$view}");
        }

        if ($layout === '') {
            require $viewFile;
            return;
        }

        $content = function () use ($viewFile, $data): void {
            extract($data, EXTR_SKIP);
            require $viewFile;
        };
        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    /** Ambil & bersihkan input dari POST. */
    protected function post(string $key, string $default = ''): string
    {
        return Security::clean($_POST[$key] ?? $default);
    }

    protected function postRaw(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, string $default = ''): string
    {
        return Security::clean($_GET[$key] ?? $default);
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            $this->view('errors/419', [], 'guest');
            exit;
        }
    }

    /** Pastikan user login, jika tidak redirect ke login. */
    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            redirect('auth/login');
        }
    }

    /** Pastikan user memiliki permission tertentu, jika tidak tampilkan 403. */
    protected function requirePermission(string $code): void
    {
        $this->requireLogin();
        if (!Auth::can($code)) {
            http_response_code(403);
            $this->view('errors/403', [], 'app');
            exit;
        }
    }

    /** Set flash message lalu redirect. */
    protected function withSuccess(string $message, string $path): never
    {
        Flash::set('success', $message);
        redirect($path);
    }

    protected function withError(string $message, string $path): never
    {
        Flash::set('error', $message);
        redirect($path);
    }

    /**
     * Batasi query ke cabang milik user (null jika super admin = semua cabang).
     */
    protected function currentBranchId(): ?int
    {
        return Auth::user()['branch_id'] ?? null;
    }

    /**
     * Kirim data sebagai file CSV untuk diunduh (dipakai fitur ekspor laporan).
     * @param array<int,array<string,mixed>> $rows
     * @param array<string,string> $columns kolom_data => label_header
     */
    protected function exportCsv(array $rows, array $columns, string $filename): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . Security::safeFilename($filename) . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, array_values($columns));
        foreach ($rows as $row) {
            $line = [];
            foreach (array_keys($columns) as $key) {
                $line[] = $row[$key] ?? '';
            }
            fputcsv($out, $line);
        }
        fclose($out);
        exit;
    }
}
