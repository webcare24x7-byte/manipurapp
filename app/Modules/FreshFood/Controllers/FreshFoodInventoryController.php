<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\FreshFood\Services\FreshFoodInventoryService;
use RuntimeException;
use Throwable;

final class FreshFoodInventoryController extends Controller
{
    private FreshFoodInventoryService $s;
    private Authorization $a;

    public function __construct()
    {
        $this->s = new FreshFoodInventoryService();
        $this->a = new Authorization();
    }

    private function allow(string $permission, string $redirect = '/fresh-food/inventory'): bool
    {
        try {
            $this->a->authorize($permission);
            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect($redirect);
            return false;
        }
    }

    public function index(): void
    {
        if (!$this->allow('fresh_food.inventory.view')) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $this->view('FreshFood::Inventory.index', [
            'title' => 'Fresh Food Inventory',
            'inventory' => $this->s->all($tenantId),
        ]);
    }

    public function adjust(): void
    {
        if (!$this->allow('fresh_food.inventory.adjust')) {
            return;
        }

        $tenantId = (int) Auth::tenantId();
        $productId = (int) ($_GET['product_id'] ?? 0);

        $this->view('FreshFood::Inventory.adjust', [
            'title' => 'Adjust Fresh Food Inventory',
            'products' => $this->s->products($tenantId),
            'selectedProduct' => $productId > 0 ? $this->s->findProduct($tenantId, $productId) : null,
        ]);
    }

    public function storeAdjustment(): void
    {
        if (!$this->allow('fresh_food.inventory.adjust')) {
            return;
        }

        try {
            $this->s->adjust(
                (int) Auth::tenantId(),
                $_POST,
                (int) Auth::id()
            );

            flash('success', 'Inventory updated successfully.');
            redirect('/fresh-food/inventory');
        } catch (Throwable $e) {
            $tenantId = (int) Auth::tenantId();
            $productId = (int) ($_POST['product_id'] ?? 0);

            $this->view('FreshFood::Inventory.adjust', [
                'title' => 'Adjust Fresh Food Inventory',
                'products' => $this->s->products($tenantId),
                'selectedProduct' => $productId > 0 ? $this->s->findProduct($tenantId, $productId) : null,
                'old' => $_POST,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function movements(int $productId): void
    {
        if (!$this->allow('fresh_food.inventory.movements.view')) {
            return;
        }

        $tenantId = (int) Auth::tenantId();
        $product = $this->s->findProduct($tenantId, $productId);

        if (!$product) {
            abort(404);
        }

        $this->view('FreshFood::Inventory.movements', [
            'title' => 'Inventory Movements',
            'product' => $product,
            'movements' => $this->s->movements($tenantId, $productId),
        ]);
    }
}
