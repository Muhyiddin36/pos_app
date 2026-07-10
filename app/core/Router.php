<?php
/**
 * Router sederhana berbasis whitelist controller.
 * Format URL: index.php?route=modul/aksi/param1/param2
 * atau /modul/aksi/param1 melalui .htaccess rewrite.
 */

declare(strict_types=1);

final class Router
{
    /** Peta segmen pertama URL => nama kelas controller. Whitelist eksplisit. */
    private const MAP = [
        'auth'        => AuthController::class,
        'dashboard'   => DashboardController::class,
        'branches'    => BranchController::class,
        'users'       => UserController::class,
        'roles'       => RoleController::class,
        'categories'  => CategoryController::class,
        'products'    => ProductController::class,
        'suppliers'   => SupplierController::class,
        'customers'   => CustomerController::class,
        'purchases'   => PurchaseController::class,
        'sales'       => SalesController::class,
        'pulsa'       => PulsaController::class,
        'pawn'        => PawnController::class,
        'loan'        => LoanController::class,
        'bank'        => BankController::class,
        'service'     => ServiceController::class,
        'cash'        => CashController::class,
        'reports'     => ReportController::class,
        'backup'      => BackupController::class,
        'audit-log'   => AuditLogController::class,
        'settings'    => SettingController::class,
        'api'         => ApiController::class,
    ];

    public static function dispatch(string $route): void
    {
        $route = trim($route, '/');
        $segments = $route === '' ? [] : explode('/', $route);

        $moduleKey = $segments[0] ?? 'dashboard';
        $action    = $segments[1] ?? 'index';
        $params    = array_slice($segments, 2);

        if (!isset(self::MAP[$moduleKey])) {
            self::notFound();
        }

        $controllerClass = self::MAP[$moduleKey];
        if (!class_exists($controllerClass)) {
            self::notFound();
        }

        // hanya izinkan nama aksi alfanumerik + underscore (whitelist pola)
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $action)) {
            self::notFound();
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action) || !(new ReflectionMethod($controller, $action))->isPublic()) {
            self::notFound();
        }

        // Cegah pemanggilan method milik base Controller secara langsung
        $declaringClass = (new ReflectionMethod($controller, $action))->getDeclaringClass()->getName();
        if ($declaringClass === Controller::class) {
            self::notFound();
        }

        call_user_func_array([$controller, $action], $params);
    }

    private static function notFound(): never
    {
        http_response_code(404);
        require VIEW_PATH . '/errors/404.php';
        exit;
    }
}
