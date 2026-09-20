<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Core\Database;

final class MemberNotificationService
{
    public const TENANT_ID = 1;

    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Materialize status-history events as member notifications. This is idempotent
     * through the unique source_event key, so polling never creates duplicates.
     */
    public function sync(int $memberId): void
    {
        $this->syncRestaurant($memberId);
        $this->syncFreshFood($memberId);
        $this->syncTaxi($memberId);
        $this->syncCommercialRental($memberId);
    }

    private function syncRestaurant(int $memberId): void
    {
        $rows = $this->db->fetchAll(
            "SELECT h.id AS event_id, h.order_id, h.to_status, h.note, h.created_at,
                    o.order_no, b.name AS restaurant_name
             FROM restaurant_order_status_history h
             INNER JOIN restaurant_orders o
               ON o.id = h.order_id AND o.tenant_id = h.tenant_id AND o.member_id = ?
             INNER JOIN restaurant_profiles r
               ON r.id = o.restaurant_id AND r.tenant_id = o.tenant_id
             INNER JOIN businesses b
               ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE h.tenant_id = ?
             ORDER BY h.id ASC",
            [$memberId, self::TENANT_ID]
        );

        foreach ($rows as $row) {
            $status = strtoupper((string)($row['to_status'] ?? ''));
            $this->insertNotification([
                'restaurant',
                'order_status',
                (int)$row['order_id'],
                (int)$row['event_id'],
                $this->restaurantTitle($status, (string)$row['restaurant_name']),
                $this->restaurantMessage($status, (string)$row['order_no'], (string)$row['restaurant_name'], (string)($row['note'] ?? '')),
                '/member/restaurant/orders/' . (int)$row['order_id'],
                (string)$row['created_at'],
                $memberId,
            ]);
        }
    }

    private function syncFreshFood(int $memberId): void
    {
        $rows = $this->db->fetchAll(
            "SELECT h.id AS event_id, h.order_id, h.to_status, h.note, h.created_at,
                    o.order_no, b.name AS fresh_food_name
             FROM fresh_food_order_status_history h
             INNER JOIN fresh_food_orders o
               ON o.id = h.order_id AND o.tenant_id = h.tenant_id AND o.member_id = ?
             INNER JOIN fresh_food_profiles f
               ON f.id = o.fresh_food_id AND f.tenant_id = o.tenant_id
             INNER JOIN businesses b
               ON b.id = f.business_id AND b.tenant_id = f.tenant_id
             WHERE h.tenant_id = ?
             ORDER BY h.id ASC",
            [$memberId, self::TENANT_ID]
        );

        foreach ($rows as $row) {
            $status = strtoupper((string) ($row['to_status'] ?? ''));
            $this->insertNotification([
                'fresh_food',
                'order_status',
                (int) $row['order_id'],
                (int) $row['event_id'],
                $this->freshFoodTitle($status, (string) $row['fresh_food_name']),
                $this->freshFoodMessage($status, (string) $row['order_no'], (string) $row['fresh_food_name'], (string) ($row['note'] ?? '')),
                '/member/fresh-food/orders/' . (int) $row['order_id'],
                (string) $row['created_at'],
                $memberId,
            ]);
        }
    }

    private function syncTaxi(int $memberId): void
    {
        $rows = $this->db->fetchAll(
            "SELECT h.id AS event_id, h.booking_id, h.new_status, h.notes, h.created_at,
                    tb.booking_no, COALESCE(ts.name, 'Taxi') AS service_name
             FROM taxi_booking_status_history h
             INNER JOIN taxi_bookings tb
               ON tb.id = h.booking_id AND tb.tenant_id = h.tenant_id AND tb.member_id = ?
             LEFT JOIN taxi_services ts
               ON ts.id = tb.service_id AND ts.tenant_id = tb.tenant_id
             WHERE h.tenant_id = ?
             ORDER BY h.id ASC",
            [$memberId, self::TENANT_ID]
        );

        foreach ($rows as $row) {
            $status = (string)($row['new_status'] ?? '');
            $this->insertNotification([
                'taxi',
                'booking_status',
                (int)$row['booking_id'],
                (int)$row['event_id'],
                $this->taxiTitle($status, (string)$row['service_name']),
                $this->taxiMessage($status, (string)$row['booking_no'], (string)($row['notes'] ?? '')),
                '/member/bookings/' . (int)$row['booking_id'],
                (string)$row['created_at'],
                $memberId,
            ]);
        }
    }

    private function syncCommercialRental(int $memberId): void
    {
        $rows = $this->db->fetchAll(
            "SELECT h.id AS event_id, h.request_id, h.new_status, h.notes, h.created_at,
                    r.request_no, r.customer_name, v.name AS vehicle_name,
                    c.name AS category_name, b.name AS provider_name
             FROM commercial_rental_request_status_history h
             INNER JOIN commercial_rental_requests r
               ON r.id = h.request_id AND r.tenant_id = h.tenant_id AND r.member_id = ?
             LEFT JOIN commercial_rental_vehicles v
               ON v.id = r.vehicle_id AND v.tenant_id = r.tenant_id
             LEFT JOIN commercial_rental_categories c
               ON c.id = v.category_id AND c.tenant_id = v.tenant_id
             LEFT JOIN commercial_rental_providers p
               ON p.id = r.provider_id AND p.tenant_id = r.tenant_id
             LEFT JOIN businesses b
               ON b.id = p.business_id AND b.tenant_id = p.tenant_id
             WHERE h.tenant_id = ?
             ORDER BY h.id ASC",
            [$memberId, self::TENANT_ID]
        );

        foreach ($rows as $row) {
            $status = strtoupper((string)($row['new_status'] ?? ''));
            $requestNo = (string)($row['request_no'] ?? '');
            $vehicle = (string)($row['vehicle_name'] ?? 'Commercial vehicle');
            $provider = (string)($row['provider_name'] ?? 'Rental provider');
            $this->insertNotification([
                'commercial_rental',
                'request_status',
                (int)$row['request_id'],
                (int)$row['event_id'],
                $this->commercialRentalTitle($status, $vehicle),
                $this->commercialRentalMessage($status, $requestNo, $vehicle, $provider, (string)($row['notes'] ?? '')),
                '/member/commercial-rental/bookings/' . (int)$row['request_id'],
                (string)$row['created_at'],
                $memberId,
            ]);
        }
    }

    private function insertNotification(array $n): void
    {
        [$source, $type, $referenceId, $eventId, $title, $message, $url, $createdAt, $memberId] = $n;
        $this->db->execute(
            "INSERT IGNORE INTO member_notifications
                (uuid, tenant_id, member_id, source, notification_type, reference_id,
                 source_event_id, title, message, action_url, created_at)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [self::TENANT_ID, $memberId, $source, $type, $referenceId, $eventId, $title, $message, $url, $createdAt]
        );
    }

    public function unreadCount(int $memberId): int
    {
        $this->sync($memberId);
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS c FROM member_notifications
             WHERE tenant_id = ? AND member_id = ? AND read_at IS NULL",
            [self::TENANT_ID, $memberId]
        );
        return (int)($row['c'] ?? 0);
    }

    public function all(int $memberId, int $limit = 100): array
    {
        $this->sync($memberId);
        $limit = max(1, min(200, $limit));
        return $this->db->fetchAll(
            "SELECT id, source, notification_type, reference_id, title, message,
                    action_url, created_at, read_at
             FROM member_notifications
             WHERE tenant_id = ? AND member_id = ?
             ORDER BY created_at DESC, id DESC
             LIMIT {$limit}",
            [self::TENANT_ID, $memberId]
        );
    }

    public function markAllRead(int $memberId): void
    {
        $this->sync($memberId);
        $this->db->execute(
            "UPDATE member_notifications SET read_at = NOW()
             WHERE tenant_id = ? AND member_id = ? AND read_at IS NULL",
            [self::TENANT_ID, $memberId]
        );
    }

    private function restaurantTitle(string $status, string $restaurant): string
    {
        return match ($status) {
            'PENDING' => $restaurant . ' received your order',
            'ACCEPTED' => $restaurant . ' accepted your order',
            'PREPARING' => $restaurant . ' is preparing your order',
            'READY' => 'Your order is ready',
            'ASSIGNED' => 'Delivery assigned',
            'OUT_FOR_DELIVERY' => 'Your order is on the way',
            'DELIVERED' => 'Your order was delivered',
            'COMPLETED' => 'Restaurant order completed',
            'REJECTED' => 'Restaurant order rejected',
            'CANCELLED' => 'Restaurant order cancelled',
            default => 'Restaurant order updated',
        };
    }

    private function restaurantMessage(string $status, string $orderNo, string $restaurant, string $note): string
    {
        $message = match ($status) {
            'PENDING' => "Order {$orderNo} has been placed with {$restaurant}.",
            'ACCEPTED' => "{$restaurant} accepted order {$orderNo}.",
            'PREPARING' => "{$restaurant} is now preparing order {$orderNo}.",
            'READY' => "Order {$orderNo} is ready" . ($note !== '' ? ". {$note}" : '.'),
            'ASSIGNED' => "A delivery person has been assigned to order {$orderNo}.",
            'OUT_FOR_DELIVERY' => "Order {$orderNo} is out for delivery.",
            'DELIVERED' => "Order {$orderNo} has been delivered.",
            'COMPLETED' => "Order {$orderNo} has been completed.",
            'REJECTED' => "{$restaurant} rejected order {$orderNo}." . ($note !== '' ? " {$note}" : ''),
            'CANCELLED' => "Order {$orderNo} was cancelled." . ($note !== '' ? " {$note}" : ''),
            default => "The status of order {$orderNo} has changed to " . str_replace('_', ' ', strtolower($status)) . '.',
        };
        return $message;
    }

    private function freshFoodTitle(string $status, string $business): string
    {
        return match ($status) {
            'PENDING' => $business . ' received your order',
            'ACCEPTED' => $business . ' accepted your order',
            'PACKING' => $business . ' is packing your order',
            'READY' => 'Your Fresh Food order is ready',
            'ASSIGNED' => 'Fresh Food delivery assigned',
            'OUT_FOR_DELIVERY' => 'Your Fresh Food order is on the way',
            'DELIVERED' => 'Your Fresh Food order was delivered',
            'COMPLETED' => 'Fresh Food order completed',
            'REJECTED' => 'Fresh Food order rejected',
            'CANCELLED' => 'Fresh Food order cancelled',
            default => 'Fresh Food order updated',
        };
    }

    private function freshFoodMessage(string $status, string $orderNo, string $business, string $note): string
    {
        $message = match ($status) {
            'PENDING' => "Order {$orderNo} has been placed with {$business}.",
            'ACCEPTED' => "{$business} accepted order {$orderNo}.",
            'PACKING' => "{$business} is packing order {$orderNo}.",
            'READY' => "Fresh Food order {$orderNo} is ready.",
            'ASSIGNED' => "A delivery person has been assigned to order {$orderNo}.",
            'OUT_FOR_DELIVERY' => "Fresh Food order {$orderNo} is out for delivery.",
            'DELIVERED' => "Fresh Food order {$orderNo} has been delivered.",
            'COMPLETED' => "Fresh Food order {$orderNo} has been completed.",
            'REJECTED' => "{$business} rejected order {$orderNo}.",
            'CANCELLED' => "Fresh Food order {$orderNo} was cancelled.",
            default => "Fresh Food order {$orderNo} status changed to " . str_replace('_', ' ', strtolower($status)) . '.',
        };
        return $note !== '' ? $message . ' ' . $note : $message;
    }

    private function commercialRentalTitle(string $status, string $vehicle): string
    {
        return match ($status) {
            'CONTACTING' => 'Rental provider is contacting you',
            'QUOTED' => 'Rental quote received',
            'CONFIRMED' => 'Commercial rental confirmed',
            'IN_PROGRESS' => 'Commercial rental is in progress',
            'COMPLETED' => 'Commercial rental completed',
            'CANCELLED' => 'Commercial rental cancelled',
            'REJECTED' => 'Commercial rental request rejected',
            default => $vehicle . ' rental request updated',
        };
    }

    private function commercialRentalMessage(string $status, string $requestNo, string $vehicle, string $provider, string $notes): string
    {
        $message = match ($status) {
            'REQUESTED' => "Rental request {$requestNo} has been received for {$vehicle}.",
            'CONTACTING' => "{$provider} is contacting you about rental request {$requestNo}.",
            'QUOTED' => "A quote is available for rental request {$requestNo}.",
            'CONFIRMED' => "Rental request {$requestNo} for {$vehicle} has been confirmed.",
            'IN_PROGRESS' => "Your commercial rental for request {$requestNo} is now in progress.",
            'COMPLETED' => "Commercial rental request {$requestNo} has been completed.",
            'CANCELLED' => "Commercial rental request {$requestNo} was cancelled.",
            'REJECTED' => "Commercial rental request {$requestNo} was rejected.",
            default => "Commercial rental request {$requestNo} status changed to " . str_replace('_', ' ', strtolower($status)) . '.',
        };
        return $notes !== '' ? $message . ' ' . $notes : $message;
    }

    private function taxiTitle(string $status, string $service): string
    {
        return match (strtolower($status)) {
            'confirmed' => 'Taxi booking confirmed',
            'assigned' => 'Taxi driver assigned',
            'driver arrived' => 'Your taxi driver has arrived',
            'in progress' => 'Your taxi trip has started',
            'completed' => 'Taxi trip completed',
            'cancelled' => 'Taxi booking cancelled',
            'no show' => 'Taxi booking marked No Show',
            default => $service . ' booking updated',
        };
    }

    private function taxiMessage(string $status, string $bookingNo, string $notes): string
    {
        $message = match (strtolower($status)) {
            'pending' => "Taxi booking {$bookingNo} has been received.",
            'confirmed' => "Taxi booking {$bookingNo} has been confirmed.",
            'assigned' => "A driver and vehicle have been assigned to booking {$bookingNo}.",
            'driver arrived' => "Your driver has arrived for booking {$bookingNo}.",
            'in progress' => "Your taxi trip for booking {$bookingNo} is now in progress.",
            'completed' => "Taxi trip {$bookingNo} has been completed.",
            'cancelled' => "Taxi booking {$bookingNo} was cancelled.",
            'no show' => "Taxi booking {$bookingNo} was marked as No Show.",
            default => "Taxi booking {$bookingNo} status changed to {$status}.",
        };
        return $notes !== '' ? $message . ' ' . $notes : $message;
    }
}
