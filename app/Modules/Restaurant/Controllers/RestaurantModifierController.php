<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantModifierService;
use RuntimeException;
use Throwable;

final class RestaurantModifierController extends Controller
{
    private RestaurantModifierService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantModifierService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission, string $redirectTo = '/restaurant/modifiers'): bool
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
        if (!$this->allow('restaurant.modifiers.view')) return;
        $this->view('Restaurant::RestaurantModifiers.index', [
            'title' => 'Modifier Groups',
            'groups' => $this->service->all((int) Auth::tenantId(), ($_GET['view'] ?? '') === 'trash'),
            'trash' => ($_GET['view'] ?? '') === 'trash',
        ]);
    }

    public function create(): void
    {
        if (!$this->allow('restaurant.modifiers.create')) return;
        $this->view('Restaurant::RestaurantModifiers.create', [
            'title' => 'Add Modifier Group',
            'restaurants' => $this->service->restaurants((int) Auth::tenantId()),
        ]);
    }

    public function store(): void
    {
        if (!$this->allow('restaurant.modifiers.create')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $id = $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Modifier group created.');
            redirect('/restaurant/modifiers/' . $id);
        } catch (Throwable $e) {
            $this->view('Restaurant::RestaurantModifiers.create', [
                'title' => 'Add Modifier Group',
                'restaurants' => $this->service->restaurants($tenantId),
                'error' => $e->getMessage(),
                'old' => $_POST,
            ]);
        }
    }

    public function show(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.view')) return;
        $tenantId = (int) Auth::tenantId();
        $group = $this->service->find($tenantId, $id);
        if (!$group) abort(404);
        $this->view('Restaurant::RestaurantModifiers.show', [
            'title' => $group['name'],
            'record' => $group,
            'options' => $this->service->options($tenantId, $id),
            'deleted_options' => $this->service->deletedOptions($tenantId, $id),
            'attached_items' => $this->service->attachedItems($tenantId, $id),
            'available_items' => $this->service->availableItems($tenantId, $id),
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.edit')) return;
        $tenantId = (int) Auth::tenantId();
        $group = $this->service->find($tenantId, $id);
        if (!$group) abort(404);
        $this->view('Restaurant::RestaurantModifiers.edit', [
            'title' => 'Edit Modifier Group',
            'record' => $group,
            'restaurants' => $this->service->restaurants($tenantId),
        ]);
    }

    public function update(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.edit')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Modifier group updated.');
            redirect('/restaurant/modifiers/' . $id);
        } catch (Throwable $e) {
            $group = $this->service->find($tenantId, $id) ?? $_POST;
            $this->view('Restaurant::RestaurantModifiers.edit', [
                'title' => 'Edit Modifier Group',
                'record' => $group,
                'restaurants' => $this->service->restaurants($tenantId),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function restore(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.delete', '/restaurant/modifiers?view=trash')) return;
        try { $this->service->restore((int) Auth::tenantId(), $id, (int) Auth::id()); flash('success', 'Modifier group restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/modifiers?view=trash');
    }

    public function addOption(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.edit')) return;
        try {
            $this->service->addOption((int) Auth::tenantId(), $id, $_POST, (int) Auth::id());
            flash('success', 'Modifier option added.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/modifiers/' . $id);
    }

    public function attachItem(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.edit')) return;
        try {
            $this->service->attachItem(
                (int) Auth::tenantId(),
                $id,
                (int) ($_POST['item_id'] ?? 0),
                (int) Auth::id()
            );
            flash('success', 'Menu item attached to modifier group.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/modifiers/' . $id);
    }

    public function detachItem(int $id, int $itemId): void
    {
        if (!$this->allow('restaurant.modifiers.edit')) return;
        try {
            $this->service->detachItem(
                (int) Auth::tenantId(),
                $id,
                $itemId,
                (int) Auth::id()
            );
            flash('success', 'Menu item detached from modifier group.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/modifiers/' . $id);
    }

    public function deleteOption(int $id, int $optionId): void
    {
        if (!$this->allow('restaurant.modifiers.delete')) return;
        try {
            $this->service->deleteOption((int) Auth::tenantId(), $id, $optionId, (int) Auth::id());
            flash('success', 'Modifier option deleted.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/modifiers/' . $id);
    }

    public function restoreOption(int $id, int $optionId): void
    {
        if (!$this->allow('restaurant.modifiers.delete', '/restaurant/modifiers/' . $id)) return;
        try { $this->service->restoreOption((int) Auth::tenantId(), $id, $optionId, (int) Auth::id()); flash('success', 'Modifier option restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/modifiers/' . $id);
    }

    public function destroy(int $id): void
    {
        if (!$this->allow('restaurant.modifiers.delete')) return;
        try {
            $this->service->delete((int) Auth::tenantId(), $id, (int) Auth::id());
            flash('success', 'Modifier group deleted.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/modifiers');
    }
}
