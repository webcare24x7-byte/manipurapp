<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

final class AITourismTools
{
    private $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function allowedScopes(): array
    {
        return [
            'TOURISM' => '🏔️ Tourism overview',
            'DESTINATIONS' => '📍 Destinations',
            'STAYS' => '🏨 Places to stay',
            'PACKAGES' => '🧳 Tour packages',
            'GUIDES' => '🧭 Local guides',
            'EXPERIENCES' => '✨ Experiences',
            'EVENTS' => '🎉 Events & festivals',
            'RESTAURANTS' => '🍽️ Restaurants & menus',
            'RESTAURANT_MENU' => '🍛 Restaurant menu items',
        ];
    }

    public function query(int $tenantId, string $scope, string $question = ''): array
    {
        $scope = strtoupper(trim($scope));
        if (!array_key_exists($scope, $this->allowedScopes())) {
            throw new \InvalidArgumentException('Please choose a valid Tourism AI category.');
        }

        return match ($scope) {
            'TOURISM' => $this->overview($tenantId),
            'DESTINATIONS' => $this->destinations($tenantId, $question),
            'STAYS' => $this->stays($tenantId, $question),
            'PACKAGES' => $this->packages($tenantId, $question),
            'GUIDES' => $this->guides($tenantId, $question),
            'EXPERIENCES' => $this->experiences($tenantId, $question),
            'EVENTS' => $this->events($tenantId, $question),
            'RESTAURANTS' => $this->restaurants($tenantId, $question),
            'RESTAURANT_MENU' => $this->restaurantMenu($tenantId, $question),
        };
    }

    private function overview(int $tenantId): array
    {
        return [
            'scope' => 'TOURISM',
            'label' => $this->allowedScopes()['TOURISM'],
            'records' => [],
            'summary' => [
                'destinations' => $this->count('tourism_destinations', $tenantId, true, "status='Active'"),
                'stays' => $this->count('tourism_stays', $tenantId, true, "status='Active'"),
                'packages' => $this->count('tourism_packages', $tenantId, true, "status='Active'"),
                'guides' => $this->count('tourism_guides', $tenantId, true, "status='Active'"),
                'experiences' => $this->count('tourism_experiences', $tenantId, true, "status='Active'"),
                'events' => $this->count('tourism_events', $tenantId, true, "status='Active'"),
            ],
        ];
    }

    private function destinations(int $tenantId, string $question): array
    {
        [$locationSql, $locationParams, $locationLabel] = $this->locationFilter(
            $tenantId,
            'tourism_destinations',
            ['district', 'city', 'name'],
            $question,
            'd'
        );

        $sql = "SELECT d.id,d.name,d.description,d.district,d.city,d.best_time_to_visit,d.suggested_duration,d.latitude,d.longitude,d.cover_image_path
                FROM tourism_destinations d
                WHERE d.tenant_id=? AND d.status='Active' AND d.deleted_at IS NULL
                {$locationSql}
                ORDER BY d.name ASC LIMIT 25";
        $rows = $this->db->fetchAll($sql, array_merge([$tenantId], $locationParams));
        return $this->result('DESTINATIONS', $rows, $locationLabel);
    }

    private function stays(int $tenantId, string $question): array
    {
        [$locationSql, $locationParams, $locationLabel] = $this->locationFilter(
            $tenantId,
            'tourism_stays',
            ['district', 'city', 'name'],
            $question,
            's'
        );

        $sql = "SELECT s.id,s.name,s.stay_type,s.description,s.city,s.district,s.address,
                       s.starting_price_per_night,s.amenities,
                       d.name AS destination_name
                FROM tourism_stays s
                LEFT JOIN tourism_destinations d ON d.id=s.destination_id AND d.tenant_id=s.tenant_id
                WHERE s.tenant_id=? AND s.status='Active' AND s.deleted_at IS NULL
                {$locationSql}
                ORDER BY s.name ASC LIMIT 25";
        $rows = $this->db->fetchAll($sql, array_merge([$tenantId], $locationParams));

        foreach ($rows as &$row) {
            $row['room_types'] = $this->db->fetchAll(
                "SELECT id,name,description,bed_type,max_guests,quantity,price_per_night
                 FROM tourism_stay_room_types
                 WHERE tenant_id=? AND stay_id=? AND status='Active' AND deleted_at IS NULL
                 ORDER BY price_per_night ASC LIMIT 10",
                [$tenantId, (int)$row['id']]
            );
        }
        unset($row);

        return $this->result('STAYS', $rows, $locationLabel);
    }

    private function packages(int $tenantId, string $question): array
    {
        // Packages are filtered through their linked destinations when a location is mentioned.
        [$destinationSql, $destinationParams, $locationLabel] = $this->destinationRelationshipFilter(
            $tenantId,
            'p',
            $question
        );

        $sql = "SELECT DISTINCT p.id,p.title,p.description,p.duration_days,p.duration_nights,p.cover_image_path,
                       p.pricing_type,p.base_price,p.discount_price,p.max_travellers
                FROM tourism_packages p
                {$destinationSql}
                WHERE p.tenant_id=? AND p.status='Active' AND p.deleted_at IS NULL
                ORDER BY p.title ASC LIMIT 25";
        $rows = $this->db->fetchAll($sql, array_merge($destinationParams, [$tenantId]));

        foreach ($rows as &$row) {
            $row['destinations'] = $this->db->fetchAll(
                "SELECT d.id,d.name,d.district,d.city,pd.day_number,pd.sequence_no,pd.notes
                 FROM tourism_package_destinations pd
                 INNER JOIN tourism_destinations d ON d.id=pd.destination_id AND d.tenant_id=pd.tenant_id
                 WHERE pd.tenant_id=? AND pd.package_id=?
                 ORDER BY pd.day_number ASC,pd.sequence_no ASC",
                [$tenantId, (int)$row['id']]
            );
        }
        unset($row);

        return $this->result('PACKAGES', $rows, $locationLabel);
    }

    private function guides(int $tenantId, string $question): array
    {
        [$locationSql, $locationParams, $locationLabel] = $this->locationFilter(
            $tenantId,
            'tourism_guides',
            ['district', 'city', 'name'],
            $question,
            'g'
        );

        $rows = $this->db->fetchAll(
            "SELECT g.id,g.name,g.bio,g.profile_image_path,g.city,g.district,g.languages,g.specializations,g.experience_years,g.price_per_day,g.verification_status
             FROM tourism_guides g
             WHERE g.tenant_id=? AND g.status='Active' AND g.deleted_at IS NULL
             {$locationSql}
             ORDER BY g.name ASC LIMIT 25",
            array_merge([$tenantId], $locationParams)
        );
        return $this->result('GUIDES', $rows, $locationLabel);
    }

    private function experiences(int $tenantId, string $question): array
    {
        [$destinationSql, $destinationParams, $locationLabel] = $this->experienceLocationFilter(
            $tenantId,
            $question
        );

        $rows = $this->db->fetchAll(
            "SELECT e.id,e.title,e.description,e.experience_type,e.duration_minutes,e.price,
                    e.max_participants,e.meeting_point,e.included,e.requirements,
                    e.cover_image_path,e.latitude,e.longitude,d.name AS destination_name,d.district AS destination_district
             FROM tourism_experiences e
             LEFT JOIN tourism_destinations d ON d.id=e.destination_id AND d.tenant_id=e.tenant_id
             WHERE e.tenant_id=? AND e.status='Active' AND e.deleted_at IS NULL
             {$destinationSql}
             ORDER BY e.featured DESC,e.title ASC LIMIT 25",
            array_merge([$tenantId], $destinationParams)
        );
        return $this->result('EXPERIENCES', $rows, $locationLabel);
    }

    private function events(int $tenantId, string $question): array
    {
        [$locationSql, $locationParams, $locationLabel] = $this->locationFilter(
            $tenantId,
            'tourism_events',
            ['district', 'city', 'venue', 'title'],
            $question,
            'e'
        );

        $rows = $this->db->fetchAll(
            "SELECT e.id,e.title,e.description,e.event_type,e.start_at,e.end_at,e.venue,e.city,e.district,
                    e.organizer_name,e.ticket_info,e.featured,e.cover_image_path
             FROM tourism_events e
             WHERE e.tenant_id=? AND e.status='Active' AND e.deleted_at IS NULL
             {$locationSql}
             ORDER BY e.start_at ASC LIMIT 25",
            array_merge([$tenantId], $locationParams)
        );
        return $this->result('EVENTS', $rows, $locationLabel);
    }

    /**
     * Resolve a location from the administrator's natural-language question using
     * values that actually exist in the tenant's Tourism records. This prevents
     * Gemini from being asked to guess which records belong to a district/city.
     */
    private function locationFilter(
        int $tenantId,
        string $table,
        array $columns,
        string $question,
        string $alias
    ): array {
        $values = [];
        foreach ($columns as $column) {
            $rows = $this->db->fetchAll(
                "SELECT DISTINCT {$column} AS value FROM {$table} WHERE tenant_id=? AND {$column} IS NOT NULL AND TRIM({$column})<>'' LIMIT 200",
                [$tenantId]
            );
            foreach ($rows as $row) {
                $value = trim((string)($row['value'] ?? ''));
                if ($value !== '') $values[$value] = true;
            }
        }

        $matched = $this->matchQuestionValue($question, array_keys($values));
        if ($matched === null) return ['', [], null];

        $parts = [];
        $params = [];
        foreach ($columns as $column) {
            $parts[] = "LOWER(COALESCE({$alias}.{$column},'')) = LOWER(?)";
            $params[] = $matched;
        }

        return ['AND (' . implode(' OR ', $parts) . ')', $params, $matched];
    }

    /**
     * Location filter for entities whose location is represented by a linked
     * Tourism destination (packages and experiences).
     */
    private function destinationRelationshipFilter(int $tenantId, string $parentAlias, string $question): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT district,city,name FROM tourism_destinations
             WHERE tenant_id=? AND status='Active' AND deleted_at IS NULL",
            [$tenantId]
        );

        $values = [];
        foreach ($rows as $row) {
            foreach (['district', 'city', 'name'] as $key) {
                $value = trim((string)($row[$key] ?? ''));
                if ($value !== '') $values[$value] = true;
            }
        }

        $matched = $this->matchQuestionValue($question, array_keys($values));
        if ($matched === null) return ['', [], null];

        return [
            "INNER JOIN tourism_package_destinations _pd ON _pd.package_id={$parentAlias}.id AND _pd.tenant_id={$parentAlias}.tenant_id
             INNER JOIN tourism_destinations _locd ON _locd.id=_pd.destination_id AND _locd.tenant_id=_pd.tenant_id
             AND (LOWER(_locd.district)=LOWER(?) OR LOWER(_locd.city)=LOWER(?) OR LOWER(_locd.name)=LOWER(?))",
            [$matched, $matched, $matched],
            $matched,
        ];
    }

    private function experienceLocationFilter(int $tenantId, string $question): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT district,city,name FROM tourism_destinations
             WHERE tenant_id=? AND status='Active' AND deleted_at IS NULL",
            [$tenantId]
        );

        $values = [];
        foreach ($rows as $row) {
            foreach (['district', 'city', 'name'] as $key) {
                $value = trim((string)($row[$key] ?? ''));
                if ($value !== '') $values[$value] = true;
            }
        }

        $matched = $this->matchQuestionValue($question, array_keys($values));
        if ($matched === null) return ['', [], null];

        return [
            "AND (LOWER(COALESCE(d.district,''))=LOWER(?) OR LOWER(COALESCE(d.city,''))=LOWER(?) OR LOWER(COALESCE(d.name,''))=LOWER(?))",
            [$matched, $matched, $matched],
            $matched,
        ];
    }

    private function matchQuestionValue(string $question, array $values): ?string
    {
        $question = mb_strtolower(trim($question));
        if ($question === '') return null;

        usort($values, static function (string $a, string $b): int {
            return mb_strlen($b) <=> mb_strlen($a);
        });

        foreach ($values as $value) {
            if (mb_strpos($question, mb_strtolower($value)) !== false) {
                return $value;
            }
        }

        return null;
    }

    private function restaurants(int $tenantId, string $question): array
    {
        $filters = $this->restaurantFilters($tenantId, $question);

        $sql = "SELECT DISTINCT rp.id,rp.business_id,rp.description,rp.logo_path,rp.cover_image_path,
                       rp.cuisine_type,rp.minimum_order_amount,rp.delivery_available,rp.pickup_available,
                       rp.delivery_fee,rp.free_delivery_above,rp.estimated_prep_minutes,rp.accepting_orders,
                       b.name,b.phone,
                       b.address,b.city,b.district,b.state,b.postal_code
                FROM restaurant_profiles rp
                INNER JOIN businesses b ON b.id=rp.business_id AND b.tenant_id=rp.tenant_id
                WHERE rp.tenant_id=? AND rp.status='Active' AND rp.deleted_at IS NULL
                  AND b.status='Active' AND b.deleted_at IS NULL
                {$filters['restaurant_sql']}
                ORDER BY b.name ASC LIMIT 25";
        $rows = $this->db->fetchAll($sql, array_merge([$tenantId], $filters['restaurant_params']));

        foreach ($rows as &$row) {
            $row['menu_items'] = $this->restaurantMenuForRestaurant(
                $tenantId,
                (int)$row['id'],
                $filters['menu_sql'],
                $filters['menu_params']
            );
        }
        unset($row);

        return $this->result('RESTAURANTS', $rows, $filters['location']);
    }

    private function restaurantMenu(int $tenantId, string $question): array
    {
        $filters = $this->restaurantFilters($tenantId, $question);

        $sql = "SELECT i.id,i.restaurant_id,i.category_id,i.name,i.slug,i.description,i.price,
                       i.discount_type,i.discount_value,i.is_veg,i.is_available,
                       c.name AS category_name,b.name AS restaurant_name,
                       b.city,b.district,b.state,rp.cuisine_type,rp.delivery_fee,rp.minimum_order_amount
                FROM restaurant_menu_items i
                INNER JOIN restaurant_profiles rp ON rp.id=i.restaurant_id AND rp.tenant_id=i.tenant_id
                INNER JOIN businesses b ON b.id=rp.business_id AND b.tenant_id=rp.tenant_id
                LEFT JOIN restaurant_menu_categories c ON c.id=i.category_id AND c.tenant_id=i.tenant_id
                WHERE i.tenant_id=? AND i.status='Active' AND i.deleted_at IS NULL
                  AND i.is_available=1
                  AND rp.status='Active' AND rp.deleted_at IS NULL
                  AND b.status='Active' AND b.deleted_at IS NULL
                {$filters['menu_sql']}
                ORDER BY i.price ASC,i.name ASC LIMIT 50";
        $rows = $this->db->fetchAll($sql, array_merge([$tenantId], $filters['menu_params']));

        foreach ($rows as &$row) {
            $row['variants'] = $this->db->fetchAll(
                "SELECT id,name,price FROM restaurant_menu_item_variants
                 WHERE tenant_id=? AND restaurant_id=? AND item_id=? AND status='Active'
                   AND is_available=1 AND deleted_at IS NULL
                 ORDER BY price ASC LIMIT 10",
                [$tenantId, (int)$row['restaurant_id'], (int)$row['id']]
            );
            $row['modifier_groups'] = $this->db->fetchAll(
                "SELECT g.id,g.name,g.selection_type,g.min_selections,g.max_selections,g.is_required
                 FROM restaurant_menu_item_modifier_groups x
                 INNER JOIN restaurant_modifier_groups g ON g.id=x.modifier_group_id AND g.tenant_id=x.tenant_id
                 WHERE x.tenant_id=? AND x.restaurant_id=? AND x.item_id=?
                   AND g.status='Active' AND g.deleted_at IS NULL
                 ORDER BY g.sort_order ASC LIMIT 10",
                [$tenantId, (int)$row['restaurant_id'], (int)$row['id']]
            );
        }
        unset($row);

        return $this->result('RESTAURANT_MENU', $rows, $filters['location']);
    }

    private function restaurantMenuForRestaurant(
        int $tenantId,
        int $restaurantId,
        string $menuSql,
        array $menuParams
    ): array {
        $items = $this->db->fetchAll(
            "SELECT i.id,i.restaurant_id,i.name,i.slug,i.description,i.price,i.discount_type,i.discount_value,i.is_veg,
                    i.is_available,i.image_path,c.name AS category_name
             FROM restaurant_menu_items i
             LEFT JOIN restaurant_menu_categories c ON c.id=i.category_id AND c.tenant_id=i.tenant_id
             INNER JOIN restaurant_profiles rp ON rp.id=i.restaurant_id AND rp.tenant_id=i.tenant_id
             INNER JOIN businesses b ON b.id=rp.business_id AND b.tenant_id=rp.tenant_id
             WHERE i.tenant_id=? AND i.restaurant_id=? AND i.status='Active' AND i.deleted_at IS NULL
               AND i.is_available=1 {$menuSql}
             ORDER BY i.price ASC,i.name ASC LIMIT 30",
            array_merge([$tenantId, $restaurantId], $menuParams)
        );

        foreach ($items as &$item) {
            $item['_ai'] = [
                'record_type' => 'RESTAURANT_MENU',
                'record_id' => (int)($item['id'] ?? 0),
                'restaurant_id' => $restaurantId,
                'member_path' => sprintf('/member/restaurants/%d/items/%d', $restaurantId, (int)($item['id'] ?? 0)),
            ];
        }
        unset($item);

        return $items;
    }

    private function restaurantFilters(int $tenantId, string $question): array
    {
        $questionLower = mb_strtolower(trim($question));
        $restaurantSql = '';
        $restaurantParams = [];
        $menuSql = '';
        $menuParams = [];
        $location = null;

        // Resolve locations from actual restaurant/business data instead of asking Gemini to guess.
        $locationValues = [];
        $locationRows = $this->db->fetchAll(
            "SELECT DISTINCT b.city,b.district,b.state
             FROM restaurant_profiles rp
             INNER JOIN businesses b ON b.id=rp.business_id AND b.tenant_id=rp.tenant_id
             WHERE rp.tenant_id=? AND rp.status='Active' AND rp.deleted_at IS NULL
               AND b.status='Active' AND b.deleted_at IS NULL",
            [$tenantId]
        );
        foreach ($locationRows as $row) {
            foreach (['city','district','state'] as $key) {
                $v = trim((string)($row[$key] ?? ''));
                if ($v !== '') $locationValues[$v] = true;
            }
        }
        $location = $this->matchQuestionValue($question, array_keys($locationValues));
        if ($location !== null) {
            $restaurantSql .= " AND (LOWER(COALESCE(b.city,''))=LOWER(?) OR LOWER(COALESCE(b.district,''))=LOWER(?) OR LOWER(COALESCE(b.state,''))=LOWER(?))";
            array_push($restaurantParams, $location, $location, $location);
            $menuSql .= " AND (LOWER(COALESCE(b.city,''))=LOWER(?) OR LOWER(COALESCE(b.district,''))=LOWER(?) OR LOWER(COALESCE(b.state,''))=LOWER(?))";
            array_push($menuParams, $location, $location, $location);
        }

        // Vegetarian / non-vegetarian intent.
        $vegOnly = preg_match('/\b(vegetarian|veg|veggie|vegetarians)\b/i', $question) === 1
            && preg_match('/\b(non[- ]?veg|nonvegetarian|chicken|pork|beef|buff|mutton|meat|fish|seafood)\b/i', $question) !== 1;
        $nonVeg = preg_match('/\b(non[- ]?veg|nonvegetarian)\b/i', $question) === 1;
        if ($vegOnly) {
            $menuSql .= " AND i.is_veg=1";
        } elseif ($nonVeg) {
            $menuSql .= " AND i.is_veg=0";
        }

        // Protein/dish intent such as "chicken dishes" or "pork under 400".
        $protein = null;
        foreach (['chicken','pork','beef','buff','mutton','fish','seafood','egg','paneer'] as $candidate) {
            if (preg_match('/\b' . preg_quote($candidate, '/') . '\b/i', $question)) {
                $protein = $candidate;
                break;
            }
        }
        if ($protein !== null) {
            $menuSql .= " AND (LOWER(i.name) LIKE LOWER(?) OR LOWER(COALESCE(i.description,'')) LIKE LOWER(?))";
            $proteinLike = '%' . $protein . '%';
            array_push($menuParams, $proteinLike, $proteinLike);
        }

        // Price ceiling: under/below/less than/max ₹300, Rs 300, etc.
        $maxPrice = $this->extractMaxPrice($question);
        if ($maxPrice !== null) {
            $menuSql .= " AND i.price<=?";
            $menuParams[] = $maxPrice;
        }

        // Match an actual menu item/category/cuisine value when the question names one.
        $terms = [];
        $itemRows = $this->db->fetchAll(
            "SELECT DISTINCT i.name AS value FROM restaurant_menu_items i
             WHERE i.tenant_id=? AND i.status='Active' AND i.deleted_at IS NULL
             UNION SELECT DISTINCT c.name AS value FROM restaurant_menu_categories c
             WHERE c.tenant_id=? AND c.status='Active' AND c.deleted_at IS NULL",
            [$tenantId, $tenantId]
        );
        foreach ($itemRows as $row) {
            $v = trim((string)($row['value'] ?? ''));
            if ($v !== '') $terms[$v] = true;
        }
        $cuisineRows = $this->db->fetchAll(
            "SELECT DISTINCT cuisine_type AS value FROM restaurant_profiles
             WHERE tenant_id=? AND status='Active' AND deleted_at IS NULL AND cuisine_type IS NOT NULL AND TRIM(cuisine_type)<>''",
            [$tenantId]
        );
        foreach ($cuisineRows as $row) {
            foreach (preg_split('/[,;|]+/', (string)$row['value']) ?: [] as $v) {
                $v = trim($v);
                if ($v !== '') $terms[$v] = true;
            }
        }
        $matchedTerm = $this->matchQuestionValue($question, array_keys($terms));
        if ($matchedTerm !== null) {
            $menuSql .= " AND (LOWER(i.name) LIKE LOWER(?) OR LOWER(COALESCE(i.description,'')) LIKE LOWER(?) OR LOWER(COALESCE(c.name,'')) LIKE LOWER(?) OR LOWER(COALESCE(rp.cuisine_type,'')) LIKE LOWER(?))";
            $like = '%' . $matchedTerm . '%';
            array_push($menuParams, $like, $like, $like, $like);
            $restaurantSql .= " AND EXISTS (
                SELECT 1 FROM restaurant_menu_items fi
                LEFT JOIN restaurant_menu_categories fc ON fc.id=fi.category_id AND fc.tenant_id=fi.tenant_id
                WHERE fi.tenant_id=rp.tenant_id AND fi.restaurant_id=rp.id
                  AND fi.status='Active' AND fi.deleted_at IS NULL AND fi.is_available=1
                  AND (LOWER(fi.name) LIKE LOWER(?) OR LOWER(COALESCE(fi.description,'')) LIKE LOWER(?) OR LOWER(COALESCE(fc.name,'')) LIKE LOWER(?) OR LOWER(COALESCE(rp.cuisine_type,'')) LIKE LOWER(?))
            )";
            array_push($restaurantParams, $like, $like, $like, $like);
        }

        // A restaurant question about delivery/pickup should use actual profile flags.
        if (preg_match('/\b(delivery|deliver)\b/i', $question)) {
            $restaurantSql .= " AND rp.delivery_available=1";
            $menuSql .= " AND rp.delivery_available=1";
        }
        if (preg_match('/\b(pickup|pick-up|takeaway|take away)\b/i', $question)) {
            $restaurantSql .= " AND rp.pickup_available=1";
            $menuSql .= " AND rp.pickup_available=1";
        }

        // Restaurant-level results must also respect menu constraints. Otherwise a
        // restaurant could appear even when none of its dishes satisfy the question.
        $existsParts = [
            "fi.tenant_id=rp.tenant_id",
            "fi.restaurant_id=rp.id",
            "fi.status='Active'",
            "fi.deleted_at IS NULL",
            "fi.is_available=1",
        ];
        $existsParams = [];
        if ($vegOnly) {
            $existsParts[] = 'fi.is_veg=1';
        } elseif ($nonVeg) {
            $existsParts[] = 'fi.is_veg=0';
        }
        if ($protein !== null) {
            $existsParts[] = "(LOWER(fi.name) LIKE LOWER(?) OR LOWER(COALESCE(fi.description,'')) LIKE LOWER(?))";
            $proteinLike = '%' . $protein . '%';
            array_push($existsParams, $proteinLike, $proteinLike);
        }
        if ($maxPrice !== null) {
            $existsParts[] = 'fi.price<=?';
            $existsParams[] = $maxPrice;
        }
        if ($matchedTerm !== null) {
            $existsParts[] = "(LOWER(fi.name) LIKE LOWER(?) OR LOWER(COALESCE(fi.description,'')) LIKE LOWER(?) OR LOWER(COALESCE(fc.name,'')) LIKE LOWER(?) OR LOWER(COALESCE(rp.cuisine_type,'')) LIKE LOWER(?))";
            $like = '%' . $matchedTerm . '%';
            array_push($existsParams, $like, $like, $like, $like);
        }
        if (count($existsParts) > 4) {
            $restaurantSql .= " AND EXISTS (SELECT 1 FROM restaurant_menu_items fi LEFT JOIN restaurant_menu_categories fc ON fc.id=fi.category_id AND fc.tenant_id=fi.tenant_id WHERE " . implode(' AND ', $existsParts) . ")";
            $restaurantParams = array_merge($restaurantParams, $existsParams);
        }

        return [
            'restaurant_sql' => $restaurantSql,
            'restaurant_params' => $restaurantParams,
            'menu_sql' => $menuSql,
            'menu_params' => $menuParams,
            'location' => $location,
        ];
    }

    private function extractMaxPrice(string $question): ?float
    {
        $q = mb_strtolower($question);
        $patterns = [
            '/(?:under|below|less than|upto|up to|max(?:imum)?|within)\s*(?:₹|rs\.?|inr\s*)?\s*([0-9][0-9,]*(?:\.\d+)?)/i',
            '/(?:₹|rs\.?|inr)\s*([0-9][0-9,]*(?:\.\d+)?)\s*(?:or less|maximum|max)?/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $q, $m)) {
                $n = (float)str_replace(',', '', $m[1]);
                if ($n > 0 && $n < 1000000) return $n;
            }
        }
        return null;
    }

    private function result(string $scope, array $rows, ?string $location = null): array
    {
        $routes = [
            'DESTINATIONS' => '/member/tourism/destinations/%d',
            'STAYS' => '/member/tourism/stays/%d',
            'PACKAGES' => '/member/tourism/packages/%d',
            'GUIDES' => '/member/tourism/guides/%d',
            'EXPERIENCES' => '/member/tourism/experiences/%d',
            'EVENTS' => '/member/tourism/events/%d',
            'RESTAURANTS' => '/member/restaurants/%d',
        ];
        $route = $routes[$scope] ?? '';
        foreach ($rows as &$row) {
            $id = (int)($row['id'] ?? 0);
            $memberPath = $route !== '' ? sprintf($route, $id) : '';
            if ($scope === 'RESTAURANT_MENU') {
                $memberPath = sprintf('/member/restaurants/%d/items/%d', (int)($row['restaurant_id'] ?? 0), $id);
            }
            $row['_ai'] = [
                'record_type' => $scope,
                'record_id' => $id,
                'restaurant_id' => (int)($row['restaurant_id'] ?? 0),
                'member_path' => $memberPath,
            ];
        }
        unset($row);

        return [
            'scope' => $scope,
            'label' => $this->allowedScopes()[$scope],
            'records' => $rows,
            'records_found' => count($rows),
            'location_filter' => $location,
            'summary' => [],
        ];
    }

    private function count(string $table, int $tenantId, bool $softDelete, string $extra = ''): int
    {
        $sql = "SELECT COUNT(*) AS c FROM {$table} WHERE tenant_id=?";
        if ($softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        if ($extra !== '') {
            $sql .= " AND {$extra}";
        }
        $row = $this->db->fetch($sql, [$tenantId]);
        return (int)($row['c'] ?? 0);
    }
}
