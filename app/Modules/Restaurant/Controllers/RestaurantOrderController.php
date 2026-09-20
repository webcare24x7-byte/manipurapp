<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantOrderService;
use App\Modules\Restaurant\Models\RestaurantOrder;
use RuntimeException;
use Throwable;

final class RestaurantOrderController extends Controller
{
    private RestaurantOrderService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantOrderService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission, string $redirect = '/restaurant'): bool
    {
        try { $this->authorization->authorize($permission); return true; }
        catch (RuntimeException $e) { flash('error', $e->getMessage()); redirect($redirect); return false; }
    }

    public function index(): void
    {
        if (!$this->allow('restaurant.orders.view')) return;
        $tenantId = (int)Auth::tenantId();
        $restaurantId = (int)($_GET['restaurant_id'] ?? 0);
        $status = trim((string)($_GET['status'] ?? ''));
        $this->view('Restaurant::RestaurantOrders.index', [
            'title' => 'Restaurant Orders', 'orders' => $this->service->all($tenantId, $restaurantId > 0 ? $restaurantId : null, $status ?: null),
            'restaurants' => $this->service->restaurants($tenantId), 'selected_restaurant_id' => $restaurantId, 'selected_status' => $status,
        ]);
    }

    public function show(int $id): void
    {
        if (!$this->allow('restaurant.orders.view', '/restaurant/orders')) return;
        $order = $this->service->find((int)Auth::tenantId(), $id);
        if (!$order) abort(404);
        $this->view('Restaurant::RestaurantOrders.show', ['title' => 'Order ' . $order['order_no'], 'order' => $order]);
    }

    public function status(int $id): void
    {
        if (!$this->allow('restaurant.orders.manage', '/restaurant/orders')) return;
        try {
            $this->service->updateStatus((int)Auth::tenantId(), $id, strtoupper(trim((string)($_POST['status'] ?? ''))), trim((string)($_POST['reason'] ?? '')), (int)Auth::id());
            flash('success', 'Order status updated.');
        } catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/orders/' . $id);
    }

    public function createTest(): void
    {
        if (!$this->allow('restaurant.orders.test_create')) return;
        $tenantId = (int)Auth::tenantId();
        $restaurantId = (int)($_GET['restaurant_id'] ?? 0);
        $restaurants = $this->service->restaurants($tenantId);
        if ($restaurantId <= 0 && $restaurants) $restaurantId = (int)$restaurants[0]['id'];
        $this->renderTestOrderForm($tenantId, $restaurantId, [], null);
    }

    public function storeTest(): void
    {
        if (!$this->allow('restaurant.orders.test_create', '/restaurant/orders')) return;
        try {
            $payload = $_POST;
            // The complete cart is submitted as items[index][...]. The service re-validates every line.
            if (!is_array($payload['items'] ?? null) || !$payload['items']) {
                throw new RuntimeException('Please add at least one item to the cart.');
            }
            $id = $this->service->create((int)Auth::tenantId(), $payload, (int)Auth::id());
            flash('success', 'Test customer order created.');
            redirect('/restaurant/orders/' . $id);
        } catch (Throwable $e) {
            $tenantId = (int)Auth::tenantId();
            $rid = (int)($_POST['restaurant_id'] ?? 0);
            $this->renderTestOrderForm($tenantId, $rid, $_POST, $e->getMessage());
        }
    }

    public function couponPreview(): void
    {
        if (!$this->allow('restaurant.orders.test_create', '/restaurant/orders')) return;
        header('Content-Type: application/json; charset=utf-8');
        try {
            $tenantId = (int)Auth::tenantId();
            $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
            $subtotal = max(0, (float)($_POST['subtotal'] ?? 0));
            $code = trim((string)($_POST['coupon_code'] ?? ''));
            if ($code === '') throw new RuntimeException('Enter a coupon code.');
            echo json_encode(['success'=>true,'data'=>$this->service->couponPreview($tenantId,$restaurantId,$code,$subtotal)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    private function renderTestOrderForm(int $tenantId, int $restaurantId, array $old, ?string $error): void
    {
        $restaurants = $this->service->restaurants($tenantId);
        if ($restaurantId <= 0 && $restaurants) $restaurantId = (int)$restaurants[0]['id'];
        $this->view('Restaurant::RestaurantOrders.create_test', [
            'title'=>'Create Test Customer Order',
            'restaurants'=>$restaurants,
            'members'=>$this->service->members($tenantId),
            'restaurant_id'=>$restaurantId,
            'restaurant'=>$restaurantId>0?$this->service->restaurant($tenantId,$restaurantId):[],
            'menu'=>$restaurantId>0?$this->service->menu($tenantId,$restaurantId):[],
            'coupons'=>$restaurantId>0?$this->service->coupons($tenantId,$restaurantId):[],
            'old'=>$old,
            'error'=>$error,
        ]);
    }

}
