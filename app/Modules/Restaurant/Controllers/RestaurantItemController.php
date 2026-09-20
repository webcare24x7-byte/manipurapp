<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantItemService;
use RuntimeException;
use Throwable;

final class RestaurantItemController extends Controller
{
    private RestaurantItemService $service;

    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantItemService();
        $this->authorization = new Authorization();
    }

    /**
     * Authorization helper.
     */
    private function authorizeOrRedirect(
        string $permission,
        string $redirectTo = '/restaurant/items'
    ): bool {
        try {
            $this->authorization->authorize($permission);

            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());

            redirect($redirectTo);

            return false;
        }
    }

    /**
     * Items list.
     */
    public function index(): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.view'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $this->view(
            'Restaurant::RestaurantItems.index',
            [
                'title' => 'Menu Items',
                'items' => $this->service->all($tenantId, ($_GET['view'] ?? '') === 'trash'),
                'trash' => ($_GET['view'] ?? '') === 'trash',
            ]
        );
    }

    /**
     * Prepare common form data.
     *
     * This loads:
     * - Restaurants
     * - All categories
     *
     * The category dropdown is then filtered in the view
     * according to the selected restaurant.
     */
    private function form(
        string $view,
        string $title,
        array $extra = []
    ): void {
        $tenantId = (int) Auth::tenantId();

        $data = [
            'title' => $title,

            'restaurants' =>
                $this->service->restaurants(
                    $tenantId
                ),

            'all_categories' =>
                $this->service->categoriesAll(
                    $tenantId
                ),
        ];

        $this->view(
            $view,
            array_merge(
                $data,
                $extra
            )
        );
    }

    /**
     * Create form.
     */
    public function create(): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.create'
            )
        ) {
            return;
        }

        $this->form(
            'Restaurant::RestaurantItems.create',
            'Add Food Item'
        );
    }

    /**
     * Store item.
     */
    public function store(): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.create'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {
            $id = $this->service->create(
                $tenantId,
                $_POST,
                (int) Auth::id()
            );

            flash(
                'success',
                'Food item created.'
            );

            redirect('/restaurant/items/' . $id);

        } catch (Throwable $e) {

            $this->form(
                'Restaurant::RestaurantItems.create',
                'Add Food Item',
                [
                    'error' => $e->getMessage(),
                    'old' => $_POST,
                ]
            );
        }
    }

    /**
     * Show item.
     */
    public function show(int $id): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.view'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $record = $this->service->find(
            $tenantId,
            $id
        );

        if (!$record) {
            abort(404);
        }

        $this->view(
            'Restaurant::RestaurantItems.show',
            [
                'title' => $record['name'],
                'record' => $record,
            ]
        );
    }

    /**
     * Edit form.
     */
    public function edit(int $id): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.edit'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $record = $this->service->find(
            $tenantId,
            $id
        );

        if (!$record) {
            abort(404);
        }

        $restaurantId =
            (int) $record['restaurant_id'];

        $this->form(
            'Restaurant::RestaurantItems.edit',
            'Edit Food Item',
            [
                'record' => $record,

                'categories' =>
                    $this->service->categories(
                        $tenantId,
                        $restaurantId
                    ),
            ]
        );
    }

    /**
     * Update item.
     */
    public function update(int $id): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.edit'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {

            $this->service->update(
                $tenantId,
                $id,
                $_POST,
                (int) Auth::id()
            );

            flash(
                'success',
                'Food item updated.'
            );

            redirect(
                '/restaurant/items/' . $id
            );

        } catch (Throwable $e) {

            $record =
                $this->service->find(
                    $tenantId,
                    $id
                );

            if (!$record) {
                $record = $_POST;
            }

            $restaurantId =
                (int) (
                    $record['restaurant_id']
                    ?? 0
                );

            $this->form(
                'Restaurant::RestaurantItems.edit',
                'Edit Food Item',
                [
                    'record' => $record,

                    'categories' =>
                        $this->service->categories(
                            $tenantId,
                            $restaurantId
                        ),

                    'error' =>
                        $e->getMessage(),
                ]
            );
        }
    }

    /** Restore a deleted item. */
    public function restore(int $id): void
    {
        if (!$this->authorizeOrRedirect('restaurant.items.delete', '/restaurant/items?view=trash')) return;
        try { $this->service->restore((int) Auth::tenantId(), $id, (int) Auth::id()); flash('success', 'Food item restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/items?view=trash');
    }

    /**
     * Delete item.
     */
    public function destroy(int $id): void
    {
        if (
            !$this->authorizeOrRedirect(
                'restaurant.items.delete'
            )
        ) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $this->service->delete(
            $tenantId,
            $id,
            (int) Auth::id()
        );

        flash(
            'success',
            'Food item deleted.'
        );

        redirect('/restaurant/items');
    }
}
