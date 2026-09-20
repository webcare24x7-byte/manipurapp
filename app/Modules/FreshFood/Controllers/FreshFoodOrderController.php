<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\FreshFood\Services\FreshFoodOrderService;
use RuntimeException;
use Throwable;

final class FreshFoodOrderController extends Controller
{
    private FreshFoodOrderService $s;
    private Authorization $a;

    public function __construct()
    {
        $this->s = new FreshFoodOrderService();
        $this->a = new Authorization();
    }

    private function allow(string $permission, string $redirect = '/fresh-food/orders'): bool
    {
        try { $this->a->authorize($permission); return true; }
        catch (RuntimeException $e) { flash('error', $e->getMessage()); redirect($redirect); return false; }
    }

    public function index(): void
    {
        if (!$this->allow('fresh_food.orders.view', '/fresh-food')) return;
        $tenantId = (int)Auth::tenantId();
        $status = strtoupper(trim((string)($_GET['status'] ?? '')));
        $status = $status !== '' ? $status : null;
        $this->view('FreshFood::Orders.index', [
            'title' => 'Fresh Food Orders',
            'orders' => $this->s->all($tenantId, $status),
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        if (!$this->allow('fresh_food.orders.create')) return;
        $tenantId = (int)Auth::tenantId();
        $this->view('FreshFood::Orders.create', [
            'title' => 'Create Fresh Food Order',
            ...$this->s->formData($tenantId),
        ]);
    }

    public function products(int $freshFoodId): void
    {
        if (!$this->allow('fresh_food.orders.create')) return;
        header('Content-Type: application/json; charset=UTF-8');
        try {
            echo json_encode($this->s->products((int)Auth::tenantId(), $freshFoodId), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => 'Unable to load products.']);
        }
        exit;
    }

    public function coupons(int $freshFoodId): void
    {
        if (!$this->allow('fresh_food.orders.create')) return;
        header('Content-Type: application/json; charset=UTF-8');
        try {
            echo json_encode($this->s->coupons((int)Auth::tenantId(), $freshFoodId), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => 'Unable to load coupons.']);
        }
        exit;
    }

    public function couponPreview(int $freshFoodId): void
    {
        if (!$this->allow('fresh_food.orders.create')) return;
        header('Content-Type: application/json; charset=UTF-8');
        try {
            $subtotal=(float)($_GET['subtotal']??0);$code=(string)($_GET['code']??'');
            echo json_encode($this->s->previewCoupon((int)Auth::tenantId(),$freshFoodId,$code,$subtotal),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422); echo json_encode(['error'=>$e->getMessage()]);
        }
        exit;
    }

    public function store(): void
    {
        if (!$this->allow('fresh_food.orders.create')) return;
        $tenantId = (int)Auth::tenantId();
        try {
            $id = $this->s->create($tenantId, $_POST, (int)Auth::id());
            flash('success', 'Fresh Food order created.');
            redirect('/fresh-food/orders/' . $id);
        } catch (Throwable $e) {
            $freshFoodId = (int)($_POST['fresh_food_id'] ?? 0);
            $this->view('FreshFood::Orders.create', [
                'title' => 'Create Fresh Food Order',
                ...$this->s->formData($tenantId, $freshFoodId),
                'old' => $_POST,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show(int $id): void
    {
        if (!$this->allow('fresh_food.orders.view', '/fresh-food')) return;
        $tenantId = (int)Auth::tenantId();
        try { $order = $this->s->details($tenantId, $id); }
        catch (Throwable $e) { abort(404); return; }
        $this->view('FreshFood::Orders.show', [
            'title' => 'Fresh Food Order ' . $order['order_no'],
            'order' => $order,
            'deliveryUsers' => $this->s->deliveryUsers($tenantId),
        ]);
    }

    public function transition(int $id): void
    {
        $to = strtoupper(trim((string)($_POST['status'] ?? '')));
        $permission = $to === 'CANCELLED' ? 'fresh_food.orders.cancel' : 'fresh_food.orders.manage';
        if (!$this->allow($permission, '/fresh-food/orders/' . $id)) return;
        try {
            $this->s->transition((int)Auth::tenantId(), $id, $to, $_POST, (int)Auth::id());
            flash('success', 'Order status updated to ' . $to . '.');
        } catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/fresh-food/orders/' . $id);
    }
}
