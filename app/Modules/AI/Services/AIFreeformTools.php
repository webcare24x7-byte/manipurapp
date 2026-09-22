<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use InvalidArgumentException;

final class AIFreeformTools
{
    private $db;
    private AITourismTools $tourism;

    public function __construct()
    {
        $this->db = app()->get('db');
        $this->tourism = new AITourismTools();
    }

    /**
     * Function declarations exposed to Gemini. These are read-only application tools.
     * They never create/update/delete bookings or other records.
     */
    public function definitions(): array
    {
        return [
            [
                'type' => 'function',
                'name' => 'search_tourism',
                'description' => 'Search live ManipurApp tourism records for destinations, places to stay, tour packages, local guides, experiences, and events. Use this for travel planning and tourism questions. Results come only from the current tenant database.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'The natural-language tourism need, including interests, location, duration, budget, or activity preferences.',
                        ],
                        'categories' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'string',
                                'enum' => ['destinations','stays','packages','guides','experiences','events'],
                            ],
                            'description' => 'Optional categories to search. If omitted, search the tourism categories relevant to the request.',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'search_restaurants',
                'description' => 'Search live ManipurApp restaurant profiles and available menu items. Use for cuisine, dishes, vegetarian/non-vegetarian, budget, delivery, pickup, or restaurant recommendations. Results come only from the current tenant database.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'The natural-language food need, such as pork dishes, vegetarian dinner, Manipuri food, or food under a budget.',
                        ],
                        'location' => [
                            'type' => 'string',
                            'description' => 'Optional city, district, or locality explicitly mentioned by the user.',
                        ],
                        'max_price' => [
                            'type' => 'number',
                            'description' => 'Optional maximum menu-item price in INR.',
                        ],
                        'diet' => [
                            'type' => 'string',
                            'enum' => ['any','vegetarian','non_vegetarian'],
                            'description' => 'Optional dietary preference.',
                        ],
                        'service' => [
                            'type' => 'string',
                            'enum' => ['any','delivery','pickup'],
                            'description' => 'Optional ordering preference.',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'search_taxi',
                'description' => 'Search active ManipurApp taxi services, taxi vendors, and active vehicles for travel planning. Use for airport transfers, local rides, outstation rides, service pricing, vehicle capacity, and vehicle type. This tool does not expose private customer bookings.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'The natural-language taxi requirement, including airport/local/outstation intent, route, vehicle type, seating, or budget.',
                        ],
                        'service_type' => [
                            'type' => 'string',
                            'enum' => ['any','Local','Airport Transfer','Outstation','Scheduled','Other'],
                            'description' => 'Optional taxi service type.',
                        ],
                        'vehicle_type' => [
                            'type' => 'string',
                            'description' => 'Optional vehicle type such as Sedan or SUV.',
                        ],
                        'minimum_seats' => [
                            'type' => 'integer',
                            'description' => 'Optional minimum passenger seating capacity.',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
        ];
    }

    public function execute(int $tenantId, string $name, array $arguments): array
    {
        return match ($name) {
            'search_tourism' => $this->searchTourism($tenantId, $arguments),
            'search_restaurants' => $this->searchRestaurants($tenantId, $arguments),
            'search_taxi' => $this->searchTaxi($tenantId, $arguments),
            default => throw new InvalidArgumentException('Unknown AI tool: ' . $name),
        };
    }

    private function searchTourism(int $tenantId, array $args): array
    {
        $query = trim((string)($args['query'] ?? ''));
        if ($query === '') {
            throw new InvalidArgumentException('Tourism search query is required.');
        }

        $map = [
            'destinations' => 'DESTINATIONS',
            'stays' => 'STAYS',
            'packages' => 'PACKAGES',
            'guides' => 'GUIDES',
            'experiences' => 'EXPERIENCES',
            'events' => 'EVENTS',
        ];

        $requested = [];
        foreach ((array)($args['categories'] ?? []) as $category) {
            $key = strtolower(trim((string)$category));
            if (isset($map[$key])) {
                $requested[$map[$key]] = true;
            }
        }

        if (!$requested) {
            $requested = [
                'DESTINATIONS' => true,
                'STAYS' => true,
                'PACKAGES' => true,
                'GUIDES' => true,
                'EXPERIENCES' => true,
                'EVENTS' => true,
            ];
        }

        $out = [
            'tool' => 'search_tourism',
            'query' => $query,
            'records' => [],
            'record_counts' => [],
        ];

        foreach (array_keys($requested) as $scope) {
            $result = $this->tourism->query($tenantId, $scope, $query);
            $records = array_slice((array)$result['records'], 0, 8);
            $out['record_counts'][$scope] = count($records);
            $out['records'][$scope] = $records;
        }

        return $out;
    }

    private function searchRestaurants(int $tenantId, array $args): array
    {
        $query = trim((string)($args['query'] ?? ''));
        if ($query === '') {
            throw new InvalidArgumentException('Restaurant search query is required.');
        }

        // Add explicit structured arguments to the query so the existing,
        // database-backed restaurant filters can do the actual filtering.
        $parts = [$query];

        $location = trim((string)($args['location'] ?? ''));
        if ($location !== '') $parts[] = $location;

        $maxPrice = $args['max_price'] ?? null;
        if (is_numeric($maxPrice) && (float)$maxPrice > 0) {
            $parts[] = 'under ₹' . (float)$maxPrice;
        }

        $diet = strtolower(trim((string)($args['diet'] ?? 'any')));
        if ($diet === 'vegetarian') $parts[] = 'vegetarian';
        if ($diet === 'non_vegetarian') $parts[] = 'non-vegetarian';

        $service = strtolower(trim((string)($args['service'] ?? 'any')));
        if ($service === 'delivery') $parts[] = 'delivery';
        if ($service === 'pickup') $parts[] = 'pickup';

        $result = $this->tourism->query($tenantId, 'RESTAURANTS', implode(' ', $parts));
        $records = array_slice((array)$result['records'], 0, 12);

        $menu = [];
        foreach ($records as $restaurant) {
            foreach ((array)($restaurant['menu_items'] ?? []) as $item) {
                $menu[] = [
                    'id' => (int)($item['id'] ?? 0),
                    'restaurant_id' => (int)($restaurant['id'] ?? 0),
                    'restaurant_name' => (string)($restaurant['name'] ?? ''),
                    'name' => (string)($item['name'] ?? ''),
                    'description' => (string)($item['description'] ?? ''),
                    'price' => $item['price'] ?? null,
                    'is_veg' => (int)($item['is_veg'] ?? 0),
                    'category_name' => (string)($item['category_name'] ?? ''),
                    '_ai' => $item['_ai'] ?? null,
                ];
            }
        }
        $menu = array_slice($menu, 0, 25);

        return [
            'tool' => 'search_restaurants',
            'query' => $query,
            'records_found' => count($records),
            'restaurants' => $records,
            'matching_menu_items' => $menu,
        ];
    }

    private function searchTaxi(int $tenantId, array $args): array
    {
        $query = trim((string)($args['query'] ?? ''));
        if ($query === '') {
            throw new InvalidArgumentException('Taxi search query is required.');
        }

        $serviceType = trim((string)($args['service_type'] ?? 'any'));
        $vehicleType = trim((string)($args['vehicle_type'] ?? ''));
        $minimumSeats = isset($args['minimum_seats']) && is_numeric($args['minimum_seats'])
            ? max(1, (int)$args['minimum_seats'])
            : null;

        $q = mb_strtolower($query);
        $params = [$tenantId];
        $where = "s.tenant_id=? AND s.status='Active' AND s.deleted_at IS NULL
                  AND v.status='Active' AND v.deleted_at IS NULL";

        $types = ['local','airport transfer','outstation','scheduled','other'];
        $type = $serviceType !== '' && strtolower($serviceType) !== 'any' ? $serviceType : null;
        if ($type !== null && in_array(strtolower($type), $types, true)) {
            $where .= " AND s.service_type=?";
            $params[] = $type;
        } else {
            if (preg_match('/airport|airport transfer|tulihal/i', $q)) {
                $where .= " AND s.service_type='Airport Transfer'";
            } elseif (preg_match('/outstation|outside imphal|intercity|long distance/i', $q)) {
                $where .= " AND s.service_type='Outstation'";
            } elseif (preg_match('/scheduled|advance|later|tomorrow|book.*time/i', $q)) {
                $where .= " AND s.service_type='Scheduled'";
            } elseif (preg_match('/local|city|around imphal|within imphal/i', $q)) {
                $where .= " AND s.service_type='Local'";
            }
        }

        if ($vehicleType !== '') {
            $where .= " AND LOWER(v.vehicle_type)=LOWER(?)";
            $params[] = $vehicleType;
        } elseif (preg_match('/\b(suv|sedan|hatchback|van|mpv)\b/i', $q, $m)) {
            $where .= " AND LOWER(v.vehicle_type)=LOWER(?)";
            $params[] = $m[1];
        }

        if ($minimumSeats !== null) {
            $where .= " AND v.seating_capacity>=?";
            $params[] = $minimumSeats;
        } elseif (preg_match('/\b(\d+)\s*(?:seater|seats|people|persons|passengers)\b/i', $q, $m)) {
            $where .= " AND v.seating_capacity>=?";
            $params[] = (int)$m[1];
        }

        // Match a user-mentioned service name, vendor name, or vehicle make/model.
        $searchLike = '%' . $query . '%';
        $where .= " AND (
            LOWER(s.name) LIKE LOWER(?) OR LOWER(COALESCE(s.description,'')) LIKE LOWER(?) OR
            LOWER(COALESCE(tv.legal_name,'')) LIKE LOWER(?) OR
            LOWER(COALESCE(b.name,'')) LIKE LOWER(?) OR
            LOWER(COALESCE(v.vehicle_type,'')) LIKE LOWER(?) OR
            LOWER(COALESCE(v.make,'')) LIKE LOWER(?) OR
            LOWER(COALESCE(v.model,'')) LIKE LOWER(?)
        )";
        array_push($params, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);

        // If the query is a broad intent phrase, the exact LIKE can be too strict.
        // Retry without the text clause when the structured filters identify the service.
        $sql = "SELECT DISTINCT s.id AS service_id,s.name AS service_name,s.code,s.service_type,
                       s.description,s.pricing_mode,s.base_fare,s.per_km,s.per_minute,s.minimum_fare,
                       s.included_km,s.daily_rate,s.extra_km_rate,
                       tv.id AS vendor_id,COALESCE(tv.legal_name,b.name) AS vendor_name,
                       v.id AS vehicle_id,v.vehicle_type,v.make,v.model,v.model_year,v.color,v.seating_capacity,v.photo_path
                FROM taxi_services s
                INNER JOIN taxi_vendors tv ON tv.id=s.vendor_id AND tv.tenant_id=s.tenant_id
                LEFT JOIN businesses b ON b.id=tv.business_id AND b.tenant_id=tv.tenant_id
                INNER JOIN taxi_vehicles v ON v.vendor_id=s.vendor_id AND v.tenant_id=s.tenant_id
                WHERE {$where}
                ORDER BY s.name ASC,v.seating_capacity DESC
                LIMIT 20";
        $rows = $this->db->fetchAll($sql, $params);

        if (!$rows && preg_match('/airport|local|city|outstation|taxi|cab|ride|vehicle|car|sedan|suv|airport/i', $q)) {
            $fallbackParams = [$tenantId];
            $fallbackWhere = "s.tenant_id=? AND s.status='Active' AND s.deleted_at IS NULL
                              AND v.status='Active' AND v.deleted_at IS NULL";
            if ($type !== null && in_array(strtolower($type), $types, true)) {
                $fallbackWhere .= " AND s.service_type=?";
                $fallbackParams[] = $type;
            } elseif (preg_match('/airport/i', $q)) {
                $fallbackWhere .= " AND s.service_type='Airport Transfer'";
            } elseif (preg_match('/outstation/i', $q)) {
                $fallbackWhere .= " AND s.service_type='Outstation'";
            }
            if ($vehicleType !== '') {
                $fallbackWhere .= " AND LOWER(v.vehicle_type)=LOWER(?)";
                $fallbackParams[] = $vehicleType;
            }
            if ($minimumSeats !== null) {
                $fallbackWhere .= " AND v.seating_capacity>=?";
                $fallbackParams[] = $minimumSeats;
            }
            $rows = $this->db->fetchAll(
                "SELECT DISTINCT s.id AS service_id,s.name AS service_name,s.code,s.service_type,
                        s.description,s.pricing_mode,s.base_fare,s.per_km,s.per_minute,s.minimum_fare,
                        s.included_km,s.daily_rate,s.extra_km_rate,
                        tv.id AS vendor_id,COALESCE(tv.legal_name,b.name) AS vendor_name,
                        v.id AS vehicle_id,v.vehicle_type,v.make,v.model,v.model_year,v.color,v.seating_capacity,v.photo_path
                 FROM taxi_services s
                 INNER JOIN taxi_vendors tv ON tv.id=s.vendor_id AND tv.tenant_id=s.tenant_id
                 LEFT JOIN businesses b ON b.id=tv.business_id AND b.tenant_id=tv.tenant_id
                 INNER JOIN taxi_vehicles v ON v.vendor_id=s.vendor_id AND v.tenant_id=s.tenant_id
                 WHERE {$fallbackWhere}
                 ORDER BY s.name ASC,v.seating_capacity DESC
                 LIMIT 20",
                $fallbackParams
            );
        }

        foreach ($rows as &$row) {
            $row['_ai'] = [
                'record_type' => 'TAXI_SERVICE',
                'record_id' => (int)$row['service_id'],
                'vehicle_id' => (int)$row['vehicle_id'],
                'member_path' => '/member/taxi?service_id=' . (int)$row['service_id'],
                'vehicle_path' => '/member/taxi/vehicles?service_id=' . (int)$row['service_id'],
            ];
        }
        unset($row);

        return [
            'tool' => 'search_taxi',
            'query' => $query,
            'records_found' => count($rows),
            'services' => $rows,
            'important_note' => 'Taxi results describe configured active services and vehicles. They are not a confirmation of real-time booking availability.',
        ];
    }
}
