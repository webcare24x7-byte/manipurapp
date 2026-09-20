<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Controllers;

use App\Core\Controller;
use App\Modules\MemberApp\Services\MemberAuthService;
use App\Modules\MemberApp\Services\MemberCsrfService;
use App\Modules\MemberApp\Services\MemberTaxiService;
use App\Modules\MemberApp\Services\MemberRestaurantService;
use App\Modules\MemberApp\Services\MemberFreshFoodService;
use App\Modules\MemberApp\Services\MemberNotificationService;
use App\Modules\MemberApp\Services\MemberCommercialRentalService;
use App\Modules\MemberApp\Services\MemberTourismService;
use App\Modules\MemberApp\Services\MemberAIService;
use RuntimeException;
use Throwable;

final class MemberAppController extends Controller
{
    private MemberAuthService $authService;

    private MemberCsrfService $csrf;

    private MemberTaxiService $taxiService;

    private MemberRestaurantService $restaurantService;

    private MemberFreshFoodService $freshFoodService;

    private MemberNotificationService $notificationService;

    private MemberCommercialRentalService $commercialRentalService;
    private MemberTourismService $tourismService;
    private MemberAIService $aiService;

    public function __construct()
    {
        $this->authService = new MemberAuthService();
        $this->csrf = new MemberCsrfService();
        $this->taxiService = new MemberTaxiService();
        $this->restaurantService = new MemberRestaurantService();
        $this->freshFoodService = new MemberFreshFoodService();
        $this->notificationService = new MemberNotificationService();
        $this->commercialRentalService = new MemberCommercialRentalService();
        $this->tourismService = new MemberTourismService();
        $this->aiService = new MemberAIService();
    }

    /**
     * GET /member
     */
    public function home(): void
    {
        $this->view(
            'MemberApp::Home.home',
            [
                'title'  => 'ManipurApp',
                'member' => $this->currentMember(),
            ],
            'memberapp'
        );
    }

    /** GET /member/search */
    public function search(): void
    {
        $query = trim((string)($_GET['q'] ?? ''));
        $results = [
            'restaurants' => [],
            'destinations' => [],
            'stays' => [],
            'packages' => [],
            'guides' => [],
            'experiences' => [],
            'events' => [],
            'fresh_food' => [],
            'taxi' => [],
            'rentals' => [],
        ];

        if ($query !== '') {
            $results['restaurants'] = array_slice($this->restaurantService->restaurants(null, null, $query), 0, 12);
            $results['destinations'] = array_slice($this->tourismService->destinations($query), 0, 8);
            $results['stays'] = array_slice($this->tourismService->stays(null, $query), 0, 8);
            $results['packages'] = array_slice($this->tourismService->packages($query), 0, 8);
            $results['guides'] = array_slice($this->tourismService->guides($query), 0, 8);
            $results['experiences'] = array_slice($this->tourismService->experiences(null, $query), 0, 8);
            $results['events'] = array_slice($this->tourismService->events($query), 0, 8);
            $results['fresh_food'] = array_slice($this->freshFoodService->businesses(null, null, $query), 0, 8);

            $needle = mb_strtolower($query);
            foreach ($this->taxiService->services() as $service) {
                $haystack = mb_strtolower(implode(' ', [
                    (string)($service['name'] ?? ''),
                    (string)($service['business_name'] ?? ''),
                    (string)($service['service_type'] ?? ''),
                    (string)($service['city'] ?? ''),
                    (string)($service['district'] ?? ''),
                    (string)($service['state'] ?? ''),
                ]));
                if (str_contains($haystack, $needle)) $results['taxi'][] = $service;
            }
            $results['taxi'] = array_slice($results['taxi'], 0, 8);

            foreach ($this->commercialRentalService->search([]) as $vehicle) {
                $haystack = mb_strtolower(implode(' ', [
                    (string)($vehicle['name'] ?? ''),
                    (string)($vehicle['make'] ?? ''),
                    (string)($vehicle['model'] ?? ''),
                    (string)($vehicle['category_name'] ?? ''),
                    (string)($vehicle['provider_name'] ?? ''),
                    (string)($vehicle['provider_city'] ?? ''),
                    (string)($vehicle['provider_district'] ?? ''),
                    (string)($vehicle['description'] ?? ''),
                ]));
                if (str_contains($haystack, $needle)) $results['rentals'][] = $vehicle;
            }
            $results['rentals'] = array_slice($results['rentals'], 0, 8);
        }

        $total = 0;
        foreach ($results as $group) $total += count($group);

        $this->view('MemberApp::Search.index', [
            'title' => $query !== '' ? 'Search: ' . $query : 'Search ManipurApp',
            'query' => $query,
            'results' => $results,
            'total' => $total,
            'member' => $this->currentMember(),
        ], 'memberapp');
    }

    /** GET /member/taxi */
    public function taxi(): void
    {
        $this->view('MemberApp::Taxi.index', [
            'title' => 'Taxi',
            'services' => $this->taxiService->services(),
            'member' => $this->currentMember(),
        ], 'memberapp');
    }

    /** GET /member/taxi/vehicles */
    public function taxiVehicles(): void
    {
        $serviceId = (int)($_GET['service_id'] ?? 0);
        $service = $this->taxiService->service($serviceId);
        if (!$service) { http_response_code(404); $this->view('MemberApp::Taxi.error', ['title'=>'Taxi','message'=>'Taxi service not found.'], 'memberapp'); return; }
        $vehicles = $this->taxiService->vehicles($serviceId, (int)($_GET['min_seats'] ?? 0) ?: null, !empty($_GET['ac_only']));
        $query = $_GET;
        $this->view('MemberApp::Taxi.vehicles', [
            'title' => 'Select a Taxi', 'service' => $service, 'vehicles' => $vehicles, 'query' => $query,
            'csrf' => $this->isAuthenticated() ? $this->csrf->token() : null,
        ], 'memberapp');
    }

    /** POST /member/taxi/book */
    public function taxiBook(): void
    {
        if (!$this->isAuthenticated()) {
            $return = '/member/taxi';
            if (!empty($_POST['service_id'])) {
                $keep = [
                    'service_id','trip_type','pickup_address','pickup_lat','pickup_lng',
                    'destination_address','destination_lat','destination_lng',
                    'pickup_date','pickup_time','return_date','return_time',
                    'trip_purpose','notes'
                ];
                $params = [];
                foreach ($keep as $key) {
                    if (isset($_POST[$key]) && $_POST[$key] !== '') $params[$key] = (string)$_POST[$key];
                }
                $return = '/member/taxi/vehicles?' . http_build_query($params);
            }
            $this->redirect('/member/login?return_to=' . rawurlencode($return));
        }
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $auth = $_SESSION['memberapp_auth'];
            $id = $this->taxiService->createBooking((int)$auth['member_id'], (int)$auth['user_id'], $_POST);
            $this->redirect('/member/bookings/' . $id);
        } catch (Throwable $e) {
            $serviceId = (int)($_POST['service_id'] ?? 0);
            $service = $this->taxiService->service($serviceId);
            $vehicles = $service ? $this->taxiService->vehicles($serviceId) : [];
            $this->view('MemberApp::Taxi.vehicles', ['title'=>'Select a Taxi','service'=>$service,'vehicles'=>$vehicles,'query'=>$_POST,'error'=>$e->getMessage(),'csrf'=>$this->csrf->token()], 'memberapp');
        }
    }

    /** GET /member/taxi/bookings/{id}/process-route */
    public function taxiProcessRoute(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Login required.']); return; }
        try {
            $auth = $_SESSION['memberapp_auth'];
            // The route calculation may call Gemini and take several seconds.
            // Release the PHP session lock so cancellation/status requests from
            // the same member can proceed while the route is being processed.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            $result = $this->taxiService->processRoute((int)$auth['member_id'], $id);
            echo json_encode(['success'=>true,'data'=>$result], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422); echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** GET /member/taxi/bookings/{id}/status */
    public function taxiBookingStatus(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Login required.']);
            return;
        }

        $auth = $_SESSION['memberapp_auth'];
        $booking = $this->taxiService->booking((int)$auth['member_id'], $id);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $booking], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /** GET /member/notifications */
    public function notifications(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/member/login?return_to=' . rawurlencode('/member/notifications'));
        }
        $auth = $_SESSION['memberapp_auth'];
        $memberId = (int)$auth['member_id'];
        $this->notificationService->markAllRead($memberId);
        $this->view('MemberApp::Notifications.index', [
            'title' => 'Notifications',
            'notifications' => $this->notificationService->all($memberId),
        ], 'memberapp');
    }

    /** GET /member/notifications/status */
    public function notificationStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success'=>false,'message'=>'Login required.']);
            return;
        }
        $auth = $_SESSION['memberapp_auth'];
        $memberId = (int)$auth['member_id'];
        $this->notificationService->sync($memberId);
        echo json_encode([
            'success'=>true,
            'unread_count'=>$this->notificationService->unreadCount($memberId),
            'notifications'=>$this->notificationService->all($memberId, 20),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /** POST /member/notifications/read */
    public function notificationsRead(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success'=>false,'message'=>'Login required.']);
            return;
        }
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired. Please try again.');
            }
            $auth = $_SESSION['memberapp_auth'];
            $this->notificationService->markAllRead((int)$auth['member_id']);
            echo json_encode(['success'=>true,'unread_count'=>0]);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    /** GET /member/fresh-food */
    public function freshFood(): void
    {
        $latitude = $this->coordinate($_GET['lat'] ?? null, -90, 90);
        $longitude = $this->coordinate($_GET['lng'] ?? null, -180, 180);
        $search = trim((string) ($_GET['q'] ?? ''));

        $businesses = $this->freshFoodService->businesses($latitude, $longitude, $search);

        $this->view('MemberApp::FreshFood.index', [
            'title' => 'Fresh Food & Grocery',
            'businesses' => $businesses,
            'featuredProducts' => $this->freshFoodService->featuredProducts($businesses, 8),
            'search' => $search,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'member' => $this->currentMember(),
        ], 'memberapp');
    }

    /** GET /member/fresh-food/{id} */
    public function freshFoodBusiness(int $id): void
    {
        $business = $this->freshFoodService->business($id);
        if (!$business) {
            http_response_code(404);
            $this->view('MemberApp::FreshFood.error', ['title' => 'Fresh Food', 'message' => 'Fresh Food business not found or unavailable.'], 'memberapp');
            return;
        }

        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $this->view('MemberApp::FreshFood.show', [
            'title' => $business['business_name'],
            'business' => $business,
            'products' => $this->freshFoodService->products($id, $categoryId > 0 ? $categoryId : null),
            'selectedCategoryId' => $categoryId,
        ], 'memberapp');
    }

    /** GET /member/fresh-food/{freshFoodId}/products/{productId} */
    public function freshFoodProduct(int $freshFoodId, int $productId): void
    {
        $business = $this->freshFoodService->business($freshFoodId);
        $product = $this->freshFoodService->product($freshFoodId, $productId);
        if (!$business || !$product) {
            http_response_code(404);
            $this->view('MemberApp::FreshFood.error', ['title' => 'Fresh Product', 'message' => 'The selected product is no longer available.'], 'memberapp');
            return;
        }

        $this->view('MemberApp::FreshFood.item', [
            'title' => $product['name'],
            'business' => $business,
            'product' => $product,
            'csrf' => $this->csrf->token(),
        ], 'memberapp');
    }

    /** GET /member/fresh-food/cart */
    public function freshFoodCart(): void
    {
        $cart = $this->freshFoodCartSession();
        if (empty($cart['items']) && $this->isAuthenticated()) {
            $saved = $this->freshFoodService->loadPersistentCart((int) $_SESSION['memberapp_auth']['member_id']);
            if ($saved) {
                $cart = $saved;
                $_SESSION['memberapp_fresh_food_cart'] = $cart;
            }
        }

        try {
            $preview = $this->freshFoodService->cartPreview($cart);
        } catch (Throwable $e) {
            if (!empty($cart['coupon_code'])) {
                $cart['coupon_code'] = null;
                $this->freshFoodSaveCart($cart);
                $preview = $this->freshFoodService->cartPreview($cart);
            } else {
                throw $e;
            }
        }
        $business = $preview['business'] ?? null;
        $this->view('MemberApp::FreshFood.cart', [
            'title' => 'Fresh Food Cart',
            'business' => $business,
            'cart' => $cart,
            'preview' => $preview,
            'csrf' => $this->csrf->token(),
        ], 'memberapp');
    }

    /** POST /member/fresh-food/cart/add */
    public function freshFoodCartAdd(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $freshFoodId = (int) ($_POST['fresh_food_id'] ?? 0);
            $productId = (int) ($_POST['product_id'] ?? 0);
            $product = $this->freshFoodService->product($freshFoodId, $productId);
            if (!$product || empty($product['is_available'])) throw new RuntimeException('This Fresh Food product is no longer available.');

            $quantity = (float) ($_POST['quantity'] ?? $product['min_order_quantity'] ?? 1);
            $min = (float) $product['min_order_quantity'];
            $inc = (float) $product['increment_quantity'];
            if ($quantity < $min - 0.000001) throw new RuntimeException('Minimum order is ' . $min . ' ' . $product['unit'] . '.');
            $steps = ($quantity - $min) / $inc;
            if (abs($steps - round($steps)) > 0.0001) throw new RuntimeException('Quantity must be ordered in increments of ' . $inc . ' ' . $product['unit'] . '.');
            if ((float) $product['current_quantity'] + 0.000001 < $quantity) throw new RuntimeException('Only ' . $product['current_quantity'] . ' ' . $product['unit'] . ' is currently available.');

            $cart = $this->freshFoodCartSession();
            if (!empty($cart['fresh_food_id']) && (int) $cart['fresh_food_id'] !== $freshFoodId) {
                echo json_encode(['success'=>false,'code'=>'DIFFERENT_FRESH_FOOD','message'=>'Your Fresh Food cart contains items from another store. Clear it before starting a new store cart.'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                return;
            }
            $cart['fresh_food_id'] = $freshFoodId;
            $key = (string) $productId;
            $found = false;
            foreach ($cart['items'] as &$line) {
                if ((string) ($line['key'] ?? '') === $key) {
                    $newQuantity = round((float) $line['quantity'] + $quantity, 3);
                    if ((float) $product['current_quantity'] + 0.000001 < $newQuantity) {
                        throw new RuntimeException('You cannot add more than the available stock for this product.');
                    }
                    $line['quantity'] = $newQuantity;
                    $found = true;
                    break;
                }
            }
            unset($line);
            if (!$found) $cart['items'][] = ['key'=>$key,'product_id'=>$productId,'quantity'=>round($quantity,3)];
            $cart['coupon_code'] = $cart['coupon_code'] ?? null;
            $this->freshFoodSaveCart($cart);
            $preview = $this->freshFoodService->cartPreview($cart);
            echo json_encode(['success'=>true,'cart_count'=>$this->freshFoodCartCount($cart),'total'=>$preview['total'],'redirect'=>rtrim((string)config('app.base_path'),'/').'/member/fresh-food/cart'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** POST /member/fresh-food/cart/update */
    public function freshFoodCartUpdate(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $key = trim((string) ($_POST['key'] ?? ''));
            $quantity = (float) ($_POST['quantity'] ?? 0);
            $cart = $this->freshFoodCartSession();
            foreach ($cart['items'] as $index => $line) {
                if ((string) ($line['key'] ?? '') !== $key) continue;
                if ($quantity <= 0) unset($cart['items'][$index]);
                else {
                    $product = $this->freshFoodService->product((int)$cart['fresh_food_id'], (int)$line['product_id']);
                    if (!$product) throw new RuntimeException('This product is no longer available.');
                    $min = (float)$product['min_order_quantity']; $inc = (float)$product['increment_quantity'];
                    if ($quantity < $min - 0.000001) { unset($cart['items'][$index]); break; }
                    $steps = ($quantity - $min) / $inc;
                    if (abs($steps-round($steps)) > 0.0001) throw new RuntimeException('Quantity must be ordered in increments of '.$inc.' '.$product['unit'].'.');
                    if ((float)$product['current_quantity'] + 0.000001 < $quantity) throw new RuntimeException('Insufficient stock for '.$product['name'].'.');
                    $cart['items'][$index]['quantity'] = round($quantity,3);
                }
                break;
            }
            $cart['items'] = array_values($cart['items']);
            if (!$cart['items']) { $cart['fresh_food_id'] = null; $cart['coupon_code'] = null; }
            $this->freshFoodSaveCart($cart);
            $preview = $this->freshFoodService->cartPreview($cart);
            echo json_encode(['success'=>true,'cart_count'=>$this->freshFoodCartCount($cart),'total'=>$preview['total']], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) { http_response_code(422); echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
    }

    /** POST /member/fresh-food/cart/clear */
    public function freshFoodCartClear(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            unset($_SESSION['memberapp_fresh_food_cart']);
            if ($this->isAuthenticated()) $this->freshFoodService->deletePersistentCart((int)$_SESSION['memberapp_auth']['member_id']);
            echo json_encode(['success'=>true]);
        } catch (Throwable $e) { http_response_code(422); echo json_encode(['success'=>false,'message'=>$e->getMessage()]); }
    }

    /** POST /member/fresh-food/coupon */
    public function freshFoodApplyCoupon(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $cart = $this->freshFoodCartSession();
            if (empty($cart['items'])) throw new RuntimeException('Your Fresh Food cart is empty.');
            $code = strtoupper(trim((string)($_POST['code'] ?? '')));
            if ($code === '') throw new RuntimeException('Enter a coupon code.');
            [$cart, $preview] = $this->freshFoodService->applyCoupon($cart, $code);
            $this->freshFoodSaveCart($cart);
            echo json_encode(['success'=>true,'discount'=>$preview['coupon_discount'],'total'=>$preview['total']], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) { http_response_code(422); echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
    }

    /** POST /member/fresh-food/coupon/remove */
    public function freshFoodRemoveCoupon(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $cart = $this->freshFoodCartSession(); $cart['coupon_code'] = null; $this->freshFoodSaveCart($cart);
            echo json_encode(['success'=>true]);
        } catch (Throwable $e) { http_response_code(422); echo json_encode(['success'=>false,'message'=>$e->getMessage()]); }
    }

    /** GET /member/fresh-food/checkout */
    public function freshFoodCheckout(): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/fresh-food/checkout'));
        $cart = $this->freshFoodCartSession();
        if (empty($cart['items']) && !empty($_SESSION['memberapp_auth']['member_id'])) {
            $saved = $this->freshFoodService->loadPersistentCart((int)$_SESSION['memberapp_auth']['member_id']);
            if ($saved) { $cart = $saved; $_SESSION['memberapp_fresh_food_cart'] = $cart; }
        }
        try { $preview = $this->freshFoodService->cartPreview($cart); }
        catch (Throwable $e) { $preview = ['lines'=>[],'total'=>0,'subtotal'=>0,'gross_subtotal'=>0,'item_discount'=>0,'coupon_discount'=>0,'delivery_fee'=>0,'business'=>null]; }
        $auth = $_SESSION['memberapp_auth'];
        $this->view('MemberApp::FreshFood.checkout', [
            'title'=>'Fresh Food Checkout','cart'=>$cart,'preview'=>$preview,
            'profile'=>$this->freshFoodService->memberProfile((int)$auth['member_id']),
            'csrf'=>$this->csrf->token(),'error'=>null,'old'=>[]
        ], 'memberapp');
    }

    /** POST /member/fresh-food/orders */
    public function freshFoodPlaceOrder(): void
    {
        if (!$this->isAuthenticated()) { $this->redirect('/member/login?return_to=' . rawurlencode('/member/fresh-food/checkout')); }
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $auth = $_SESSION['memberapp_auth'];
            $cart = $this->freshFoodCartSession();
            $id = $this->freshFoodService->createOrder((int)$auth['member_id'], (int)$auth['user_id'], [
                'cart'=>$cart,
                'order_type'=>strtoupper((string)($_POST['order_type'] ?? 'DELIVERY')),
                'customer_name'=>trim((string)($_POST['customer_name'] ?? '')),
                'customer_phone'=>trim((string)($_POST['customer_phone'] ?? '')),
                'customer_email'=>trim((string)($_POST['customer_email'] ?? '')),
                'delivery_address'=>trim((string)($_POST['delivery_address'] ?? '')),
                'delivery_city'=>trim((string)($_POST['delivery_city'] ?? '')),
                'delivery_district'=>trim((string)($_POST['delivery_district'] ?? '')),
                'delivery_state'=>trim((string)($_POST['delivery_state'] ?? 'Manipur')),
                'delivery_postal_code'=>trim((string)($_POST['delivery_postal_code'] ?? '')),
                'customer_latitude'=>$this->coordinate($_POST['customer_latitude'] ?? null,-90,90),
                'customer_longitude'=>$this->coordinate($_POST['customer_longitude'] ?? null,-180,180),
                'customer_note'=>trim((string)($_POST['customer_note'] ?? '')),
            ]);
            unset($_SESSION['memberapp_fresh_food_cart']);
            $this->redirect('/member/fresh-food/orders/' . $id);
        } catch (Throwable $e) {
            $cart = $this->freshFoodCartSession();
            try { $preview = $this->freshFoodService->cartPreview($cart); } catch (Throwable) { $preview = ['lines'=>[],'total'=>0,'subtotal'=>0,'gross_subtotal'=>0,'item_discount'=>0,'coupon_discount'=>0,'delivery_fee'=>0,'business'=>null]; }
            $auth = $_SESSION['memberapp_auth'];
            $this->view('MemberApp::FreshFood.checkout', [
                'title'=>'Fresh Food Checkout','cart'=>$cart,'preview'=>$preview,
                'profile'=>$this->freshFoodService->memberProfile((int)$auth['member_id']),
                'csrf'=>$this->csrf->token(),'error'=>$e->getMessage(),'old'=>$_POST
            ], 'memberapp');
        }
    }

    /** GET /member/fresh-food/orders */
    public function freshFoodOrders(): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/fresh-food/orders'));
        $auth = $_SESSION['memberapp_auth'];
        $this->view('MemberApp::FreshFood.order', ['title'=>'Fresh Food Orders','orders'=>$this->freshFoodService->memberOrders((int)$auth['member_id'])], 'memberapp');
    }

    /** GET /member/fresh-food/orders/{id} */
    public function freshFoodOrder(int $id): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/fresh-food/orders/' . $id));
        $auth = $_SESSION['memberapp_auth'];
        $order = $this->freshFoodService->order((int)$auth['member_id'], $id);
        if (!$order) { http_response_code(404); $this->view('MemberApp::FreshFood.error', ['title'=>'Fresh Food Order','message'=>'Order not found.'], 'memberapp'); return; }
        $this->view('MemberApp::FreshFood.order', ['title'=>'Order '.$order['order_no'],'order'=>$order,'csrf'=>$this->csrf->token()], 'memberapp');
    }

    /** POST /member/fresh-food/orders/{id}/process-route */
    public function freshFoodProcessRoute(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Login required.']);
            return;
        }

        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired. Please refresh the order page.');
            }

            $auth = $_SESSION['memberapp_auth'];
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Gemini can take several seconds. Do not hold the PHP session
                // lock while the external route request is running.
                session_write_close();
            }

            $result = $this->freshFoodService->processRoute((int)$auth['member_id'], $id);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /** GET /member/fresh-food/orders/{id}/status */
    public function freshFoodOrderStatus(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Login required.']); return; }
        $auth = $_SESSION['memberapp_auth'];
        $order = $this->freshFoodService->order((int)$auth['member_id'], $id);
        if (!$order) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Order not found.']); return; }
        echo json_encode(['success'=>true,'data'=>[
            'id'=>(int)$order['id'],
            'status'=>(string)$order['status'],
            'route_status'=>(string)($order['route_status'] ?? ''),
            'route_error'=>$order['route_error'] ?? null,
            'distance_km'=>$order['distance_km'] ?? null,
            'eta_minutes'=>$order['eta_minutes'] ?? null,
            'delivery_fee'=>$order['delivery_fee'] ?? null,
            'total'=>$order['total'] ?? null,
            'status_history'=>$order['status_history']??[],
            'updated_at'=>$order['updated_at']??null
        ]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    private function freshFoodCartSession(): array
    {
        $cart = $_SESSION['memberapp_fresh_food_cart'] ?? null;
        if (!is_array($cart)) return ['fresh_food_id'=>null,'items'=>[],'coupon_code'=>null];
        return ['fresh_food_id'=>!empty($cart['fresh_food_id'])?(int)$cart['fresh_food_id']:null,'items'=>is_array($cart['items']??null)?array_values($cart['items']):[],'coupon_code'=>!empty($cart['coupon_code'])?(string)$cart['coupon_code']:null];
    }

    private function freshFoodSaveCart(array $cart): void
    {
        $cart = $this->freshFoodCartSessionFrom($cart);
        $_SESSION['memberapp_fresh_food_cart'] = $cart;
        if ($this->isAuthenticated() && !empty($cart['fresh_food_id'])) $this->freshFoodService->saveCart((int)$_SESSION['memberapp_auth']['member_id'], $cart);
    }

    private function freshFoodCartSessionFrom(array $cart): array
    {
        return ['fresh_food_id'=>!empty($cart['fresh_food_id'])?(int)$cart['fresh_food_id']:null,'items'=>is_array($cart['items']??null)?array_values($cart['items']):[],'coupon_code'=>!empty($cart['coupon_code'])?(string)$cart['coupon_code']:null];
    }

    private function freshFoodCartCount(array $cart): int
    {
        return count(array_filter((array) ($cart['items'] ?? []), static fn(array $line): bool => (float) ($line['quantity'] ?? 0) > 0));
    }

    /** GET /member/restaurants */
    public function restaurants(): void
    {
        $latitude = $this->coordinate($_GET['lat'] ?? null, -90, 90);
        $longitude = $this->coordinate($_GET['lng'] ?? null, -180, 180);
        $search = trim((string) ($_GET['q'] ?? ''));
        $cuisine = trim((string) ($_GET['cuisine'] ?? ''));

        try {
            $restaurants = $this->restaurantService->restaurants($latitude, $longitude, $search, $cuisine);
            $this->view('MemberApp::Restaurant.index', [
                'title' => 'Food & Restaurants',
                'restaurants' => $restaurants,
                'featuredItems' => $this->restaurantService->featuredItems($restaurants, 8),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'search' => $search,
                'cuisine' => $cuisine,
                'member' => $this->currentMember(),
            ], 'memberapp');
        } catch (Throwable $e) {
            http_response_code(500);
            $this->view('MemberApp::Restaurant.error', [
                'title' => 'Food & Restaurants',
                'message' => $e->getMessage(),
            ], 'memberapp');
        }
    }

    /** GET /member/restaurants/{id} */
    public function restaurant(int $id): void
    {
        $record = $this->restaurantService->restaurant($id);
        if (!$record) {
            http_response_code(404);
            $this->view('MemberApp::Restaurant.error', [
                'title' => 'Restaurant',
                'message' => 'Restaurant not found or is no longer available.',
            ], 'memberapp');
            return;
        }

        $this->view('MemberApp::Restaurant.show', [
            'title' => $record['business_name'],
            'restaurant' => $record,
        ], 'memberapp');
    }

    /** GET /member/restaurants/{restaurantId}/items/{itemId} */
    public function restaurantItem(int $restaurantId, int $itemId): void
    {
        $restaurant = $this->restaurantService->restaurant($restaurantId);
        $item = $this->restaurantService->item($restaurantId, $itemId);
        if (!$restaurant || !$item) {
            http_response_code(404);
            $this->view('MemberApp::Restaurant.error', [
                'title' => 'Food Item',
                'message' => 'The selected food item is no longer available.',
            ], 'memberapp');
            return;
        }

        $this->view('MemberApp::Restaurant.item', [
            'title' => $item['name'],
            'restaurant' => $restaurant,
            'item' => $item,
            'csrf' => $this->csrf->token(),
        ], 'memberapp');
    }

    /** GET /member/restaurant/cart */
    public function restaurantCart(): void
    {
        $cart = $this->restaurantCartSession();
        $restaurantId = (int) ($cart['restaurant_id'] ?? 0);
        $restaurant = $restaurantId > 0 ? $this->restaurantService->restaurant($restaurantId) : null;
        $preview = $restaurant && !empty($cart['items'])
            ? $this->restaurantService->cartPreview($restaurantId, $cart['items'])
            : ['gross_subtotal'=>0,'item_discount'=>0,'subtotal'=>0,'delivery_fee'=>0,'total'=>0,'minimum_order_amount'=>0,'free_delivery_above'=>null,'lines'=>[]];
        $coupon = null;
        $couponDiscount = 0.0;
        if ($restaurant && !empty($cart['coupon_code']) && (float)$preview['subtotal'] > 0) {
            try {
                $coupon = $this->restaurantService->couponPreview($restaurantId, (string)$cart['coupon_code'], (float)$preview['subtotal']);
                $couponDiscount = (float)$coupon['discount'];
            } catch (Throwable) {
                unset($cart['coupon_code']);
                $_SESSION['memberapp_restaurant_cart'] = $cart;
            }
        }
        $preview['coupon_discount'] = $couponDiscount;
        $preview['total'] = max(0, round((float)$preview['subtotal'] + (float)$preview['delivery_fee'] - $couponDiscount, 2));

        $this->view('MemberApp::Restaurant.cart', [
            'title' => 'Your Cart',
            'restaurant' => $restaurant,
            'cart' => $cart,
            'preview' => $preview,
            'coupon' => $coupon,
            'csrf' => $this->csrf->token(),
        ], 'memberapp');
    }

    /** POST /member/restaurant/cart/add */
    public function restaurantCartAdd(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired. Please try again.');
            }

            $restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
            $itemId = (int) ($_POST['item_id'] ?? 0);
            $item = $this->restaurantService->item($restaurantId, $itemId);
            if (!$item) {
                throw new RuntimeException('This food item is no longer available.');
            }
            if (empty($item['is_available'])) {
                throw new RuntimeException('This food item is currently sold out.');
            }

            $variantId = (int) ($_POST['variant_id'] ?? 0);
            $modifierIds = $this->intList($_POST['modifier_option_ids'] ?? []);
            $quantity = max(1, min(50, (int) ($_POST['quantity'] ?? 1)));

            // Validate variant/modifier structure before storing the cart.
            $validVariant = $variantId === 0;
            foreach ($item['variants'] as $variant) {
                if ((int) $variant['id'] === $variantId && !empty($variant['is_available'])) {
                    $validVariant = true;
                    break;
                }
            }
            if (!$validVariant) {
                throw new RuntimeException('The selected variant is unavailable.');
            }

            $groups = $item['modifier_groups'];
            $validIds = [];
            foreach ($groups as $group) {
                $selected = [];
                foreach ($group['options'] as $option) {
                    if (in_array((int) $option['id'], $modifierIds, true) && !empty($option['is_available'])) {
                        $selected[] = (int) $option['id'];
                        $validIds[] = (int) $option['id'];
                    }
                }
                $count = count($selected);
                $min = (int) ($group['min_selections'] ?? 0);
                $max = $group['max_selections'] === null ? null : (int) $group['max_selections'];
                if ($count < $min || ($max !== null && $count > $max) || (!empty($group['is_required']) && $count < max(1, $min))) {
                    throw new RuntimeException('Please complete the required options for ' . $group['name'] . '.');
                }
            }
            if (array_diff($modifierIds, $validIds)) {
                throw new RuntimeException('One or more selected options are unavailable.');
            }

            $cart = $this->restaurantCartSession();
            if (!empty($cart['restaurant_id']) && (int) $cart['restaurant_id'] !== $restaurantId) {
                echo json_encode([
                    'success' => false,
                    'code' => 'DIFFERENT_RESTAURANT',
                    'message' => 'Your cart contains items from another restaurant.',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }

            $cart['restaurant_id'] = $restaurantId;
            $key = $this->cartLineKey($itemId, $variantId, $modifierIds);
            $found = false;
            foreach ($cart['items'] as &$line) {
                if ((string) ($line['key'] ?? '') === $key) {
                    $line['quantity'] = min(50, (int) $line['quantity'] + $quantity);
                    $found = true;
                    break;
                }
            }
            unset($line);
            if (!$found) {
                $cart['items'][] = [
                    'key' => $key,
                    'item_id' => $itemId,
                    'variant_id' => $variantId > 0 ? $variantId : null,
                    'modifier_option_ids' => $modifierIds,
                    'quantity' => $quantity,
                ];
            }
            $_SESSION['memberapp_restaurant_cart'] = $cart;

            $preview = $this->restaurantService->cartPreview($restaurantId, $cart['items']);
            echo json_encode(['success'=>true,'cart_count'=>$this->cartCount($cart),'total'=>$preview['total'],'redirect'=>rtrim((string)config('app.base_path'),'/').'/member/restaurant/cart'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** POST /member/restaurant/cart/update */
    public function restaurantCartUpdate(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $key = trim((string) ($_POST['key'] ?? ''));
            $quantity = (int) ($_POST['quantity'] ?? 0);
            $cart = $this->restaurantCartSession();
            foreach ($cart['items'] as $index => $line) {
                if ((string) ($line['key'] ?? '') !== $key) continue;
                if ($quantity <= 0) unset($cart['items'][$index]);
                else $cart['items'][$index]['quantity'] = min(50, $quantity);
                break;
            }
            $cart['items'] = array_values($cart['items']);
            if (!$cart['items']) $cart['restaurant_id'] = null;
            $_SESSION['memberapp_restaurant_cart'] = $cart;
            $preview = $cart['restaurant_id'] ? $this->restaurantService->cartPreview((int)$cart['restaurant_id'], $cart['items']) : ['total'=>0];
            echo json_encode(['success'=>true,'cart_count'=>$this->cartCount($cart),'total'=>$preview['total']], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** POST /member/restaurant/cart/clear */
    public function restaurantCartClear(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            unset($_SESSION['memberapp_restaurant_cart']);
            echo json_encode(['success'=>true], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** POST /member/restaurant/coupon */
    public function restaurantApplyCoupon(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $cart = $this->restaurantCartSession();
            if (empty($cart['restaurant_id']) || empty($cart['items'])) throw new RuntimeException('Your cart is empty.');
            $restaurantId = (int)$cart['restaurant_id'];
            $preview = $this->restaurantService->cartPreview($restaurantId, $cart['items']);
            $code = trim((string)($_POST['code'] ?? ''));
            if ($code === '') throw new RuntimeException('Enter a coupon code.');
            $coupon = $this->restaurantService->couponPreview($restaurantId, $code, (float)$preview['subtotal']);
            $cart['coupon_code'] = (string)$coupon['code'];
            $_SESSION['memberapp_restaurant_cart'] = $cart;
            $total = max(0, round((float)$preview['subtotal'] + (float)$preview['delivery_fee'] - (float)$coupon['discount'], 2));
            echo json_encode(['success'=>true,'data'=>$coupon,'total'=>$total], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** POST /member/restaurant/coupon/remove */
    public function restaurantRemoveCoupon(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $cart = $this->restaurantCartSession();
            unset($cart['coupon_code']);
            $_SESSION['memberapp_restaurant_cart'] = $cart;
            echo json_encode(['success'=>true], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** GET /member/restaurant/checkout */
    public function restaurantCheckout(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/member/login?return_to=' . rawurlencode('/member/restaurant/checkout'));
        }
        $cart = $this->restaurantCartSession();
        if (empty($cart['restaurant_id']) || empty($cart['items'])) {
            $this->redirect('/member/restaurant/cart');
        }
        $restaurantId = (int) $cart['restaurant_id'];
        $restaurant = $this->restaurantService->restaurant($restaurantId);
        if (!$restaurant) {
            unset($_SESSION['memberapp_restaurant_cart']);
            $this->redirect('/member/restaurant/cart');
        }
        $profile = $this->restaurantService->memberProfile((int) $_SESSION['memberapp_auth']['member_id']);
        $preview = $this->restaurantService->cartPreview($restaurantId, $cart['items']);
        $coupon = null;
        if (!empty($cart['coupon_code'])) {
            try { $coupon = $this->restaurantService->couponPreview($restaurantId, (string)$cart['coupon_code'], (float)$preview['subtotal']); } catch (Throwable) { unset($cart['coupon_code']); $_SESSION['memberapp_restaurant_cart']=$cart; }
        }
        $preview['coupon_discount'] = (float)($coupon['discount'] ?? 0);
        $preview['total'] = max(0, round((float)$preview['subtotal'] + (float)$preview['delivery_fee'] - (float)$preview['coupon_discount'], 2));
        $this->view('MemberApp::Restaurant.checkout', [
            'title'=>'Checkout', 'restaurant'=>$restaurant, 'cart'=>$cart, 'preview'=>$preview,
            'coupon'=>$coupon, 'profile'=>$profile, 'csrf'=>$this->csrf->token(),
        ], 'memberapp');
    }

    /** POST /member/restaurant/orders */
    public function restaurantPlaceOrder(): void
    {
        if (!$this->isAuthenticated()) {
            $return = '/member/restaurant/checkout';
            $this->redirect('/member/login?return_to=' . rawurlencode($return));
        }
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $cart = $this->restaurantCartSession();
            if (empty($cart['restaurant_id']) || empty($cart['items'])) throw new RuntimeException('Your cart is empty.');

            $auth = $_SESSION['memberapp_auth'];
            $orderId = $this->restaurantService->createOrder((int)$auth['member_id'], (int)$auth['user_id'], [
                'restaurant_id' => (int)$cart['restaurant_id'],
                'order_type' => strtoupper(trim((string)($_POST['order_type'] ?? 'DELIVERY'))),
                'items' => $cart['items'],
                'delivery_address' => trim((string)($_POST['delivery_address'] ?? '')),
                'delivery_city' => trim((string)($_POST['delivery_city'] ?? '')),
                'delivery_district' => trim((string)($_POST['delivery_district'] ?? '')),
                'delivery_state' => trim((string)($_POST['delivery_state'] ?? '')),
                'delivery_postal_code' => trim((string)($_POST['delivery_postal_code'] ?? '')),
                'customer_note' => trim((string)($_POST['customer_note'] ?? '')),
                'coupon_code' => trim((string)($_POST['coupon_code'] ?? '')),
            ]);
            unset($_SESSION['memberapp_restaurant_cart']);
            $this->redirect('/member/restaurant/orders/' . $orderId);
        } catch (Throwable $e) {
            $cart = $this->restaurantCartSession();
            $restaurant = !empty($cart['restaurant_id']) ? $this->restaurantService->restaurant((int)$cart['restaurant_id']) : null;
            $profile = $this->restaurantService->memberProfile((int)$_SESSION['memberapp_auth']['member_id']);
            $preview = $restaurant ? $this->restaurantService->cartPreview((int)$cart['restaurant_id'], $cart['items']) : ['total'=>0,'subtotal'=>0,'item_discount'=>0,'delivery_fee'=>0,'minimum_order_amount'=>0,'free_delivery_above'=>null,'lines'=>[]];
            $coupon = null;
            if ($restaurant && !empty($cart['coupon_code'])) { try { $coupon = $this->restaurantService->couponPreview((int)$cart['restaurant_id'], (string)$cart['coupon_code'], (float)$preview['subtotal']); } catch (Throwable) {} }
            $preview['coupon_discount'] = (float)($coupon['discount'] ?? 0);
            $preview['total'] = max(0, round((float)$preview['subtotal'] + (float)$preview['delivery_fee'] - (float)$preview['coupon_discount'], 2));
            $this->view('MemberApp::Restaurant.checkout', ['title'=>'Checkout','restaurant'=>$restaurant,'cart'=>$cart,'preview'=>$preview,'coupon'=>$coupon,'profile'=>$profile,'csrf'=>$this->csrf->token(),'error'=>$e->getMessage(),'old'=>$_POST], 'memberapp');
        }
    }

    /** POST /member/restaurant/coupon */
    public function restaurantCouponPreview(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) throw new RuntimeException('Your form session has expired. Please try again.');
            $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
            $subtotal = max(0, (float)($_POST['subtotal'] ?? 0));
            $result = $this->restaurantService->couponPreview($restaurantId, (string)($_POST['code'] ?? ''), $subtotal);
            echo json_encode(['success'=>true,'data'=>$result], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    /** GET /member/restaurant/orders */
    public function restaurantOrders(): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/restaurant/orders'));
        $auth = $_SESSION['memberapp_auth'];
        $this->view('MemberApp::Restaurant.order', [
            'title'=>'Food Orders',
            'orders'=>$this->restaurantService->memberOrders((int)$auth['member_id']),
        ], 'memberapp');
    }

    /** GET /member/restaurant/orders/{id} */
    public function restaurantOrder(int $id): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/restaurant/orders/' . $id));
        $auth = $_SESSION['memberapp_auth'];
        $order = $this->restaurantService->order((int)$auth['member_id'], $id);
        if (!$order) {
            http_response_code(404);
            $this->view('MemberApp::Restaurant.error', ['title'=>'Food Order','message'=>'Order not found.'], 'memberapp');
            return;
        }
        $this->view('MemberApp::Restaurant.order', [
            'title'=>'Order ' . $order['order_no'],
            'order'=>$order,
        ], 'memberapp');
    }

    /** GET /member/restaurant/orders/{id}/status */
    public function restaurantOrderStatus(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success'=>false,'message'=>'Login required.']);
            return;
        }
        $auth = $_SESSION['memberapp_auth'];
        $order = $this->restaurantService->order((int)$auth['member_id'], $id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['success'=>false,'message'=>'Order not found.']);
            return;
        }
        echo json_encode(['success'=>true,'data'=>[
            'id'=>(int)$order['id'],
            'status'=>(string)$order['status'],
            'status_history'=>$order['status_history'] ?? [],
            'updated_at'=>$order['updated_at'] ?? null,
        ]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    private function restaurantCartSession(): array
    {
        $cart = $_SESSION['memberapp_restaurant_cart'] ?? null;
        if (!is_array($cart)) return ['restaurant_id'=>null,'items'=>[]];
        $items = is_array($cart['items'] ?? null) ? array_values($cart['items']) : [];
        return ['restaurant_id'=>!empty($cart['restaurant_id']) ? (int)$cart['restaurant_id'] : null, 'items'=>$items];
    }

    private function cartCount(array $cart): int
    {
        return array_sum(array_map(static fn(array $line): int => max(0, (int)($line['quantity'] ?? 0)), $cart['items'] ?? []));
    }

    private function cartLineKey(int $itemId, int $variantId, array $modifierIds): string
    {
        sort($modifierIds);
        return $itemId . ':' . $variantId . ':' . implode(',', $modifierIds);
    }

    private function intList(mixed $value): array
    {
        if (!is_array($value)) $value = [$value];
        $result = [];
        foreach ($value as $v) {
            if ((string)$v === '') continue;
            $id = (int)$v;
            if ($id > 0) $result[] = $id;
        }
        return array_values(array_unique($result));
    }

    /** GET /member/tourism */
    public function tourism(): void
    {
        $q=trim((string)($_GET['q']??''));
        $district=trim((string)($_GET['district']??''));
        $this->view('MemberApp::Tourism.index',[
            'title'=>'Explore Manipur',
            'destinations'=>$this->tourismService->destinations($q,$district),
            'stays'=>$this->tourismService->stays(null,$q,$district),
            'packages'=>$this->tourismService->packages($q,$district),
            'experiences'=>$this->tourismService->experiences(null,$q,$district),
            'events'=>$this->tourismService->events($q,$district),
            'guides'=>$this->tourismService->guides($q,$district),
            'districts'=>$this->tourismService->districts(),
            'search'=>$q,
            'district'=>$district,
        ],'memberapp');
    }

    public function tourismDestinations(): void
    {
        $this->view('MemberApp::Tourism.destinations',[
            'title'=>'Destinations',
            'destinations'=>$this->tourismService->destinations(trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),
            'search'=>trim((string)($_GET['q']??'')),
            'district'=>trim((string)($_GET['district']??'')),
        ],'memberapp');
    }

    public function tourismDestination(int $id): void
    {
        $item=$this->tourismService->destination($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Destination','message'=>'Destination not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.destination',[
            'title'=>$item['name'],'destination'=>$item,
            'stays'=>$this->tourismService->stays($id),
            'experiences'=>$this->tourismService->experiences($id),
            'reviews'=>$this->tourismService->reviews('DESTINATION',$id),
            'reviewType'=>'DESTINATION',
        ],'memberapp');
    }

    public function tourismStays(): void
    {
        $destinationId=(int)($_GET['destination_id']??0);
        $this->view('MemberApp::Tourism.stays',[
            'title'=>'Places to Stay','stays'=>$this->tourismService->stays($destinationId?:null,trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),
            'destinationId'=>$destinationId,'search'=>trim((string)($_GET['q']??'')),'district'=>trim((string)($_GET['district']??'')),'districts'=>$this->tourismService->districts(),
        ],'memberapp');
    }

    public function tourismStay(int $id): void
    {
        $item=$this->tourismService->stay($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Stay','message'=>'Stay not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.stay',['title'=>$item['name'],'stay'=>$item],'memberapp');
    }

    public function tourismPackages(): void
    {
        $this->view('MemberApp::Tourism.packages',['title'=>'Tour Packages','packages'=>$this->tourismService->packages(trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),'district'=>trim((string)($_GET['district']??'')),'districts'=>$this->tourismService->districts()],'memberapp');
    }

    public function tourismPackage(int $id): void
    {
        $item=$this->tourismService->package($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Tour Package','message'=>'Tour package not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.package',['title'=>$item['title'],'package'=>$item,'reviews'=>$this->tourismService->reviews('PACKAGE',$id),'reviewType'=>'PACKAGE'],'memberapp');
    }

    public function tourismGuides(): void
    {
        $this->view('MemberApp::Tourism.guides',['title'=>'Local Guides','guides'=>$this->tourismService->guides(trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),'district'=>trim((string)($_GET['district']??'')),'districts'=>$this->tourismService->districts()],'memberapp');
    }

    public function tourismGuide(int $id): void
    {
        $item=$this->tourismService->guide($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Guide','message'=>'Guide not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.guide',['title'=>$item['full_name'],'guide'=>$item,'reviews'=>$this->tourismService->reviews('GUIDE',$id),'reviewType'=>'GUIDE'],'memberapp');
    }

    public function tourismExperiences(): void
    {
        $this->view('MemberApp::Tourism.experiences',['title'=>'Experiences','experiences'=>$this->tourismService->experiences(null,trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),'district'=>trim((string)($_GET['district']??'')),'districts'=>$this->tourismService->districts()],'memberapp');
    }

    public function tourismExperience(int $id): void
    {
        $item=$this->tourismService->experience($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Experience','message'=>'Experience not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.experience',['title'=>$item['title'],'experience'=>$item,'reviews'=>$this->tourismService->reviews('EXPERIENCE',$id),'reviewType'=>'EXPERIENCE'],'memberapp');
    }

    public function tourismEvents(): void
    {
        $this->view('MemberApp::Tourism.events',['title'=>'Events & Festivals','events'=>$this->tourismService->events(trim((string)($_GET['q']??'')),trim((string)($_GET['district']??''))),'district'=>trim((string)($_GET['district']??'')),'districts'=>$this->tourismService->districts()],'memberapp');
    }

    public function tourismEvent(int $id): void
    {
        $item=$this->tourismService->event($id);
        if(!$item){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Event','message'=>'Event not found.'],'memberapp');return;}
        $this->view('MemberApp::Tourism.event',['title'=>$item['title'],'event'=>$item,'reviews'=>$this->tourismService->reviews('EVENT',$id),'reviewType'=>'EVENT'],'memberapp');
    }

    public function tourismReview(): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode((string)($_POST['return_to']??'/member/tourism')));return;}
        try{
            if(!$this->csrf->validate($_POST['_token']??null))throw new RuntimeException('Your form session has expired. Please refresh the page.');
            $auth=$_SESSION['memberapp_auth'];
            $type=strtoupper(trim((string)($_POST['reviewable_type']??'')));
            $id=(int)($_POST['reviewable_id']??0);
            $this->tourismService->review((int)$auth['user_id'],$type,$id,(int)($_POST['rating']??0),(string)($_POST['title']??''),(string)($_POST['review']??''));
            $return=$this->safeReturnUrl($_POST['return_to']??null)??'/member/tourism';
            $this->redirect($this->appendQuery($return, ['review'=>'submitted']));
        }catch(Throwable $e){
            $return=$this->safeReturnUrl($_POST['return_to']??null)??'/member/tourism';
            $this->redirect($this->appendQuery($return, ['review_error'=>$e->getMessage()]));
        }
    }

    public function tourismTrips(): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode('/member/tourism/trips'));return;}
        $auth=$_SESSION['memberapp_auth'];
        $district=trim((string)($_GET['district']??''));
        $this->view('MemberApp::Tourism.trips',['title'=>'My Trip Plans','trips'=>$this->tourismService->tripPlans((int)$auth['user_id']),'tripSources'=>$this->tourismService->tripPlannerSources($district),'district'=>$district,'districts'=>$this->tourismService->districts(),'csrf'=>$this->csrf->token()],'memberapp');
    }

    public function tourismTrip(int $id): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode('/member/tourism/trips/'.$id));return;}
        $auth=$_SESSION['memberapp_auth'];$trip=$this->tourismService->trip((int)$auth['user_id'],$id);
        if(!$trip){http_response_code(404);$this->view('MemberApp::Tourism.error',['title'=>'Trip Plan','message'=>'Trip plan not found.'],'memberapp');return;}
        $district=trim((string)($_GET['district']??''));
        $this->view('MemberApp::Tourism.trip',['title'=>$trip['title'],'trip'=>$trip,'tripSources'=>$this->tourismService->tripPlannerSources($district),'district'=>$district,'districts'=>$this->tourismService->districts(),'csrf'=>$this->csrf->token()],'memberapp');
    }

    public function tourismTripCreate(): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode('/member/tourism/trips'));return;}
        try{
            if(!$this->csrf->validate($_POST['_token']??null))throw new RuntimeException('Your form session has expired. Please refresh the page.');
            $auth=$_SESSION['memberapp_auth'];
            $id=$this->tourismService->createTrip((int)$auth['user_id'],(string)($_POST['title']??''),(string)($_POST['description']??''),$_POST['start_date']??null,$_POST['end_date']??null);
            $district=trim((string)($_POST['district']??''));
            $this->redirect('/member/tourism/trips/'.$id.($district!==''?'?district='.rawurlencode($district):''));
        }catch(Throwable $e){
            $this->view('MemberApp::Tourism.trips',['title'=>'My Trip Plans','trips'=>[],'csrf'=>$this->csrf->token(),'error'=>$e->getMessage()],'memberapp');
        }
    }

    public function tourismTripItem(int $id): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode('/member/tourism/trips/'.$id));return;}
        try{
            if(!$this->csrf->validate($_POST['_token']??null))throw new RuntimeException('Your form session has expired. Please refresh the page.');
            $auth=$_SESSION['memberapp_auth'];
            $this->tourismService->addTripItem((int)$auth['user_id'],$id,(int)($_POST['day_number']??1),strtoupper((string)($_POST['item_type']??'')),(int)($_POST['item_id']??0),(string)($_POST['title']??''),(string)($_POST['notes']??''));
            $district=trim((string)($_POST['district']??''));
            $this->redirect('/member/tourism/trips/'.$id.($district!==''?'?district='.rawurlencode($district):''));
        }catch(Throwable $e){$district=trim((string)($_POST['district']??''));
            $suffix=$district!==''?'&district='.rawurlencode($district):'';
            $this->redirect('/member/tourism/trips/'.$id.'?error='.rawurlencode($e->getMessage()).$suffix);}
    }

    public function tourismTripItemRemove(int $id): void
    {
        if(!$this->isAuthenticated()){$this->redirect('/member/login?return_to='.rawurlencode('/member/tourism/trips/'.$id));return;}
        try{
            if(!$this->csrf->validate($_POST['_token']??null))throw new RuntimeException('Your form session has expired. Please refresh the page.');
            $auth=$_SESSION['memberapp_auth'];
            $this->tourismService->removeTripItem((int)$auth['user_id'],$id,(int)($_POST['item_id']??0));
            $this->redirect('/member/tourism/trips/'.$id);
        }catch(Throwable $e){$this->redirect('/member/tourism/trips/'.$id.'?error='.rawurlencode($e->getMessage()));}
    }


    private function coordinate(mixed $value, float $min, float $max): ?float
    {
        if ($value === null || $value === '' || !is_numeric($value)) return null;
        $number = (float)$value;
        return ($number >= $min && $number <= $max) ? $number : null;
    }

    /** GET /member/commercial-rental */
    public function commercialRental(): void
    {
        $query = $_GET;
        $this->view('MemberApp::CommercialRental.index', [
            'title' => 'Commercial Vehicle Rental',
            'categories' => $this->commercialRentalService->categories(),
            'vehicles' => $this->commercialRentalService->search($query),
            'query' => $query,
        ], 'memberapp');
    }

    /** GET /member/commercial-rental/book */
    public function commercialRentalBook(): void
    {
        $vehicleId = (int)($_GET['vehicle_id'] ?? 0);
        $vehicle = $this->commercialRentalService->vehicle($vehicleId);

        if (!$vehicle) {
            http_response_code(404);
            $this->view('MemberApp::CommercialRental.error', [
                'title' => 'Commercial Rental',
                'message' => 'The selected vehicle is no longer available.',
            ], 'memberapp');
            return;
        }

        $member = $this->isAuthenticated()
            ? $this->commercialRentalService->member(
                (int)$_SESSION['memberapp_auth']['member_id']
            )
            : null;

        $this->view('MemberApp::CommercialRental.book', [
            'title' => 'Request Commercial Vehicle',
            'vehicle' => $vehicle,
            'member' => $member,
            'query' => $_GET,
            'csrf' => $this->isAuthenticated() ? $this->csrf->token() : null,
        ], 'memberapp');
    }

    /** POST /member/commercial-rental/book */
    public function commercialRentalStore(): void
    {
        if (!$this->isAuthenticated()) {
            $keep = [
                'vehicle_id', 'pickup_address', 'pickup_lat', 'pickup_lng',
                'destination_address', 'destination_lat', 'destination_lng',
                'start_at', 'end_at', 'duration_text', 'operator_required',
                'purpose', 'customer_name', 'customer_phone', 'customer_notes',
            ];
            $params = [];
            foreach ($keep as $key) {
                if (isset($_POST[$key]) && $_POST[$key] !== '') {
                    $params[$key] = (string)$_POST[$key];
                }
            }

            $return = '/member/commercial-rental/book?' . http_build_query($params);
            $this->redirect('/member/login?return_to=' . rawurlencode($return));
        }

        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired. Please try again.');
            }

            $auth = $_SESSION['memberapp_auth'];

            $id = $this->commercialRentalService->createBooking(
                (int)$auth['member_id'],
                (int)$auth['user_id'],
                $_POST
            );

            $this->redirect('/member/commercial-rental/bookings/' . $id);
        } catch (Throwable $e) {
            $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
            $vehicle = $this->commercialRentalService->vehicle($vehicleId);

            $this->view('MemberApp::CommercialRental.book', [
                'title' => 'Request Commercial Vehicle',
                'vehicle' => $vehicle ?? [],
                'member' => $this->commercialRentalService->member(
                    (int)$_SESSION['memberapp_auth']['member_id']
                ),
                'query' => $_POST,
                'error' => $e->getMessage(),
                'csrf' => $this->csrf->token(),
            ], 'memberapp');
        }
    }

    /** GET /member/commercial-rental/bookings/{id} */
    public function commercialRentalBooking(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/member/login?return_to=' . rawurlencode('/member/commercial-rental/bookings/' . $id));
        }

        $auth = $_SESSION['memberapp_auth'];
        $booking = $this->commercialRentalService->booking(
            (int)$auth['member_id'],
            $id
        );

        if (!$booking) {
            http_response_code(404);
            $this->view('MemberApp::CommercialRental.error', [
                'title' => 'Rental Request',
                'message' => 'Rental request not found.',
            ], 'memberapp');
            return;
        }

        $this->view('MemberApp::CommercialRental.confirmation', [
            'title' => 'Rental Request ' . $booking['request_no'],
            'booking' => $booking,
        ], 'memberapp');
    }

    /** POST /member/ai/ask */
    public function memberAIAsk(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Please login to ask the AI assistant.',
                'login_required' => true,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired. Please refresh the page and try again.');
            }

            $auth = $_SESSION['memberapp_auth'];
            $scope = strtoupper(trim((string)($_POST['scope'] ?? 'TOURISM')));
            $question = trim((string)($_POST['question'] ?? ''));

            $result = $this->aiService->ask(
                (int)($auth['tenant_id'] ?? MemberAuthService::TENANT_ID),
                (int)$auth['user_id'],
                $question,
                $scope
            );

            echo json_encode([
                'success' => true,
                'data' => $result,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /** GET /member/profile */
    public function profile(): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/profile'));
        $auth = $_SESSION['memberapp_auth'];
        $this->view('MemberApp::Profile.index', ['title'=>'Profile','profile'=>$this->taxiService->profile((int)$auth['member_id'])], 'memberapp');
    }

    /** GET /member/bookings */
    public function bookings(): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/bookings'));
        $auth = $_SESSION['memberapp_auth'];
        $taxiBookings = $this->taxiService->bookings((int)$auth['member_id']);
        $restaurantOrders = $this->restaurantService->memberOrders((int)$auth['member_id']);
        $freshFoodOrders = $this->freshFoodService->memberOrders((int)$auth['member_id']);
        $commercialRentalBookings = $this->commercialRentalService->bookings((int)$auth['member_id']);
        $this->view('MemberApp::Bookings.index', [
            'title'=>'Bookings',
            'bookings'=>$taxiBookings,
            'restaurantOrders'=>$restaurantOrders,
            'freshFoodOrders'=>$freshFoodOrders,
            'commercialRentalBookings'=>$commercialRentalBookings,
        ], 'memberapp');
    }

    /** GET /member/bookings/{id} */
    public function booking(int $id): void
    {
        if (!$this->isAuthenticated()) $this->redirect('/member/login?return_to=' . rawurlencode('/member/bookings/' . $id));
        $auth = $_SESSION['memberapp_auth'];
        $record = $this->taxiService->booking((int)$auth['member_id'], $id);
        if (!$record) { http_response_code(404); $this->view('MemberApp::Bookings.error', ['title'=>'Booking','message'=>'Booking not found.'], 'memberapp'); return; }
        $this->view('MemberApp::Bookings.show', ['title'=>'Booking ' . $record['booking_no'], 'booking'=>$record, 'csrf'=>$this->csrf->token()], 'memberapp');
    }

    /** POST /member/bookings/{id}/cancel */
    public function cancelBooking(int $id): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Login required.']);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->csrf->validate($_POST['_token'] ?? null)) {
                throw new RuntimeException('Your form session has expired.');
            }

            $auth = $_SESSION['memberapp_auth'];
            $cancelled = $this->taxiService->cancel(
                (int)$auth['member_id'],
                (int)$auth['user_id'],
                $id
            );

            if (!$cancelled) {
                throw new RuntimeException('This booking can no longer be cancelled. Please refresh the booking status.');
            }

            
            $updated = $this->taxiService->booking((int)$auth['member_id'], $id);
            echo json_encode(['success' => true, 'status' => 'Cancelled', 'data' => $updated], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /member/register
     */
    public function register(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/member');
        }

        $this->view(
            'MemberApp::Auth.register',
            [
                'title' => 'Create Your Account',
                'error' => null,
                'old'   => [],
                'csrf'  => $this->csrf->token(),
            ],
            'memberapp'
        );
    }

    /**
     * POST /member/register
     */
    public function storeRegistration(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/member');
        }

        $old = $_POST;

        try {
            /*
             * Validate CSRF token.
             */
            if (
                !$this->csrf->validate(
                    $_POST['_token'] ?? null
                )
            ) {
                throw new RuntimeException(
                    'Your form session has expired. Please try again.'
                );
            }

            /*
             * Register Member + User account.
             */
            $auth = $this->authService->register(
                $_POST
            );

            /*
             * Authenticate the newly created Member.
             */
            $this->startMemberSession($auth);

            $this->redirect('/member');

        } catch (Throwable $e) {

            $this->view(
                'MemberApp::Auth.register',
                [
                    'title' => 'Create Your Account',
                    'error' => $e->getMessage(),
                    'old'   => $old,
                    'csrf'  => $this->csrf->token(),
                ],
                'memberapp'
            );
        }
    }

    /**
     * GET /member/login
     */
    public function login(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/member');
        }

        $this->view(
            'MemberApp::Auth.login',
            [
                'title' => 'Welcome Back',
                'error' => null,
                'old'   => [],
                'csrf'  => $this->csrf->token(),
            ],
            'memberapp'
        );
    }

    /**
     * POST /member/login
     */
    public function authenticate(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/member');
        }

        $old = [
            'email' => $_POST['email'] ?? '',
        ];

        try {
            /*
             * Validate CSRF token.
             */
            if (
                !$this->csrf->validate(
                    $_POST['_token'] ?? null
                )
            ) {
                throw new RuntimeException(
                    'Your form session has expired. Please try again.'
                );
            }

            /*
             * Authenticate Member account.
             */
            $auth = $this->authService->login(
                (string) ($_POST['email'] ?? ''),
                (string) ($_POST['password'] ?? '')
            );

            /*
             * Create isolated MemberApp session.
             */
            $this->startMemberSession($auth);

            /*
             * Return user to the intended page when
             * applicable.
             */
            $redirect = $this->safeReturnUrl(
                $_POST['return_to'] ?? null
            );

            $this->redirect(
                $redirect ?? '/member'
            );

        } catch (Throwable $e) {

            $this->view(
                'MemberApp::Auth.login',
                [
                    'title' => 'Welcome Back',
                    'error' => $e->getMessage(),
                    'old'   => $old,
                    'csrf'  => $this->csrf->token(),
                ],
                'memberapp'
            );
        }
    }

    /**
     * GET /member/logout
     */
    public function logout(): void
    {
        unset(
            $_SESSION['memberapp_auth']
        );

        /*
         * Prevent session fixation.
         */
        session_regenerate_id(true);

        $this->redirect('/member');
    }

    /**
     * GET /member/session
     *
     * Return current MemberApp authentication state.
     */
    public function sessionStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        header(
            'Cache-Control: no-store, no-cache, must-revalidate, max-age=0'
        );

        echo json_encode(
            [
                'authenticated' =>
                    $this->isAuthenticated(),

                'account' =>
                    $this->isAuthenticated()
                        ? $_SESSION['memberapp_auth']
                        : null,
            ],
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Start isolated MemberApp authentication session.
     */
    private function startMemberSession(
        array $auth
    ): void {
        /*
         * Prevent session fixation.
         */
        session_regenerate_id(true);

        $_SESSION['memberapp_auth'] = [
            'user_id' => (int) $auth['user_id'],

            'member_id' => (int) $auth['member_id'],

            'tenant_id' =>
                MemberAuthService::TENANT_ID,

            'role' => 'member',

            'name' =>
                (string) $auth['name'],

            'email' =>
                (string) $auth['email'],
        ];
    }

    /**
     * Determine whether a valid MemberApp session exists.
     */
    private function isAuthenticated(): bool
    {
        $auth =
            $_SESSION['memberapp_auth'] ?? null;

        return is_array($auth)
            && (int) (
                $auth['tenant_id'] ?? 0
            ) === MemberAuthService::TENANT_ID
            && (string) (
                $auth['role'] ?? ''
            ) === 'member'
            && (int) (
                $auth['user_id'] ?? 0
            ) > 0
            && (int) (
                $auth['member_id'] ?? 0
            ) > 0;
    }

    /**
     * Return the currently authenticated Member.
     */
    private function currentMember(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        $auth =
            $_SESSION['memberapp_auth'];

        return [
            'user_id' =>
                (int) $auth['user_id'],

            'member_id' =>
                (int) $auth['member_id'],

            'tenant_id' =>
                MemberAuthService::TENANT_ID,

            'name' =>
                (string) $auth['name'],

            'email' =>
                (string) $auth['email'],
        ];
    }

    /** Append query parameters without producing malformed URLs. */
    private function appendQuery(string $url, array $params): string
    {
        $fragment = '';
        if (str_contains($url, '#')) {
            [$url, $fragmentPart] = explode('#', $url, 2);
            $fragment = '#' . $fragmentPart;
        }

        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs[rawurlencode((string)$key)] = rawurlencode((string)$value);
        }
        $query = [];
        foreach ($pairs as $key => $value) {
            $query[] = $key . '=' . $value;
        }
        if (!$query) return $url . $fragment;

        return $url . (str_contains($url, '?') ? '&' : '?') . implode('&', $query) . $fragment;
    }

    /**
     * Prevent open redirects.
     *
     * Only local application paths are accepted.
     */
    private function safeReturnUrl(
        mixed $value
    ): ?string {
        if (!is_string($value) || $value === '') {
            return null;
        }

        $value = trim($value);
        if (!str_starts_with($value, '/') || str_starts_with($value, '//')) {
            return null;
        }

        // Views expose URLs with the configured public/base path, while
        // Controller::redirect() expects an application-relative route.
        // Strip the base path here so it can never become /base/base/... .
        $basePath = rtrim((string)config('app.base_path'), '/');
        if ($basePath !== '' && ($value === $basePath || str_starts_with($value, $basePath . '/'))) {
            $value = substr($value, strlen($basePath));
            if ($value === '' || $value[0] !== '/') {
                $value = '/' . ltrim($value, '/');
            }
        }

        // Reject absolute URLs and protocol-relative paths after normalization.
        if ($value === '' || !str_starts_with($value, '/') || str_starts_with($value, '//')) {
            return null;
        }

        return $value;
    }
}