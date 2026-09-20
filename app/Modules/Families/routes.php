<?php

declare(strict_types=1);

use App\Core\Router;
use App\Modules\Families\Controllers\FamilyController;

$router->get(
    '/families',
    [FamilyController::class, 'index']
);

$router->get(
    '/families/create',
    [FamilyController::class, 'create']
);

$router->post(
    '/families',
    [FamilyController::class, 'store']
);

$router->get(
    '/families/{id}',
    [FamilyController::class, 'show']
);

$router->get(
    '/families/{id}/edit',
    [FamilyController::class, 'edit']
);

$router->post(
    '/families/{id}/update',
    [FamilyController::class, 'update']
);

$router->post(
    '/families/{id}/delete',
    [FamilyController::class, 'delete']
);

$router->post(
    '/families/{id}/members',
    [FamilyController::class, 'addMember']
);

$router->post(
    '/families/{id}/members/{memberId}/role',
    [FamilyController::class, 'changeMemberRole']
);

$router->post(
    '/families/{id}/members/{memberId}/move',
    [FamilyController::class, 'moveMember']
);

$router->post(
    '/families/{id}/members/{memberId}/remove',
    [FamilyController::class, 'removeMember']
);