<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantMenuManagementService;
use RuntimeException;
use Throwable;

final class RestaurantMenuManagementController extends Controller
{
    private RestaurantMenuManagementService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantMenuManagementService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission, string $redirectTo = '/restaurant'): bool
    {
        try {
            $this->authorization->authorize($permission);
            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect($redirectTo);
            return false;
        }
    }

    public function index(): void
    {
        if (!$this->allow('restaurant.view')) {
            return;
        }

        $tenantId = (int) Auth::tenantId();
        $restaurants = $this->service->restaurants($tenantId);
        $selectedId = (int) ($_GET['restaurant_id'] ?? 0);

        if ($selectedId <= 0 && !empty($restaurants)) {
            $selectedId = (int) $restaurants[0]['id'];
        }

        $menu = null;
        if ($selectedId > 0) {
            try {
                $menu = $this->service->build($tenantId, $selectedId);
            } catch (Throwable $e) {
                flash('error', $e->getMessage());
            }
        }

        $this->view('Restaurant::RestaurantMenu.index', [
            'title' => 'Menu Management',
            'restaurants' => $restaurants,
            'selected_restaurant_id' => $selectedId,
            'menu' => $menu,
        ]);
    }

    public function itemAvailability(int $restaurantId, int $itemId): void
    {
        if (!$this->allow('restaurant.items.edit', '/restaurant/menu?restaurant_id=' . $restaurantId)) {
            return;
        }

        $available = !empty($_POST['is_available']);

        try {
            $this->service->setItemAvailability(
                (int) Auth::tenantId(),
                $restaurantId,
                $itemId,
                $available,
                (int) Auth::id()
            );
            flash('success', $available ? 'Item marked available.' : 'Item marked sold out.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/restaurant/menu?restaurant_id=' . $restaurantId);
    }

    public function variantAvailability(int $restaurantId, int $variantId): void
    {
        if (!$this->allow('restaurant.variants.edit', '/restaurant/menu?restaurant_id=' . $restaurantId)) {
            return;
        }

        $available = !empty($_POST['is_available']);

        try {
            $this->service->setVariantAvailability(
                (int) Auth::tenantId(),
                $restaurantId,
                $variantId,
                $available,
                (int) Auth::id()
            );
            flash('success', $available ? 'Variant marked available.' : 'Variant marked unavailable.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/restaurant/menu?restaurant_id=' . $restaurantId);
    }
}
