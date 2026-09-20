SET FOREIGN_KEY_CHECKS=0;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_questions`
--
ALTER TABLE `ai_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_questions_tenant` (`tenant_id`),
  ADD KEY `idx_ai_questions_user` (`user_id`),
  ADD KEY `idx_ai_questions_created` (`created_at`),
  ADD KEY `idx_ai_questions_scope` (`tenant_id`,`ai_scope`,`created_at`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `businesses_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `businesses_slug_unique` (`slug`),
  ADD KEY `businesses_tenant_id_index` (`tenant_id`),
  ADD KEY `businesses_business_type_index` (`business_type`),
  ADD KEY `businesses_status_index` (`status`);

--
-- Indexes for table `commercial_rental_categories`
--
ALTER TABLE `commercial_rental_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cr_category_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_cr_category_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_cr_category_status` (`tenant_id`,`status`),
  ADD KEY `fk_cr_category_created` (`created_by`),
  ADD KEY `fk_cr_category_updated` (`updated_by`);

--
-- Indexes for table `commercial_rental_providers`
--
ALTER TABLE `commercial_rental_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cr_provider_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_cr_provider_business` (`tenant_id`,`business_id`),
  ADD KEY `idx_cr_provider_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_cr_provider_district` (`tenant_id`,`district`),
  ADD KEY `idx_cr_provider_status` (`tenant_id`,`status`),
  ADD KEY `idx_cr_provider_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_cr_provider_business` (`business_id`),
  ADD KEY `fk_cr_provider_created` (`created_by`),
  ADD KEY `fk_cr_provider_updated` (`updated_by`);

--
-- Indexes for table `commercial_rental_requests`
--
ALTER TABLE `commercial_rental_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cr_request_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_cr_request_no` (`tenant_id`,`request_no`),
  ADD KEY `idx_cr_request_member` (`tenant_id`,`member_id`),
  ADD KEY `idx_cr_request_provider` (`tenant_id`,`provider_id`),
  ADD KEY `idx_cr_request_vehicle` (`tenant_id`,`vehicle_id`),
  ADD KEY `idx_cr_request_status` (`tenant_id`,`status`),
  ADD KEY `idx_cr_request_pickup` (`tenant_id`,`pickup_lat`,`pickup_lng`),
  ADD KEY `fk_cr_request_member` (`member_id`),
  ADD KEY `fk_cr_request_provider` (`provider_id`),
  ADD KEY `fk_cr_request_vehicle` (`vehicle_id`),
  ADD KEY `fk_cr_request_created` (`created_by`),
  ADD KEY `fk_cr_request_updated` (`updated_by`);

--
-- Indexes for table `commercial_rental_request_status_history`
--
ALTER TABLE `commercial_rental_request_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cr_req_status_history_request` (`tenant_id`,`request_id`,`id`),
  ADD KEY `idx_cr_req_status_history_member_lookup` (`tenant_id`,`request_id`,`new_status`,`created_at`);

--
-- Indexes for table `commercial_rental_vehicles`
--
ALTER TABLE `commercial_rental_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cr_vehicle_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_cr_vehicle_registration` (`tenant_id`,`registration_no`),
  ADD KEY `idx_cr_vehicle_provider` (`tenant_id`,`provider_id`),
  ADD KEY `idx_cr_vehicle_category` (`tenant_id`,`category_id`),
  ADD KEY `idx_cr_vehicle_status` (`tenant_id`,`status`),
  ADD KEY `idx_cr_vehicle_availability` (`tenant_id`,`availability`),
  ADD KEY `fk_cr_vehicle_provider` (`provider_id`),
  ADD KEY `fk_cr_vehicle_category` (`category_id`),
  ADD KEY `fk_cr_vehicle_created` (`created_by`),
  ADD KEY `fk_cr_vehicle_updated` (`updated_by`);

--
-- Indexes for table `fresh_food_categories`
--
ALTER TABLE `fresh_food_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_categories_uuid_unique` (`uuid`),
  ADD KEY `fresh_food_categories_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_categories_business_index` (`fresh_food_id`);

--
-- Indexes for table `fresh_food_coupons`
--
ALTER TABLE `fresh_food_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_coupons_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_coupons_code_unique` (`tenant_id`,`fresh_food_id`,`code`),
  ADD KEY `fresh_food_coupons_business_index` (`fresh_food_id`),
  ADD KEY `fresh_food_coupons_status_index` (`fresh_food_id`,`status`);

--
-- Indexes for table `fresh_food_hours`
--
ALTER TABLE `fresh_food_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_hours_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_hours_day_unique` (`fresh_food_id`,`day_of_week`),
  ADD KEY `fresh_food_hours_tenant_index` (`tenant_id`);

--
-- Indexes for table `fresh_food_inventory`
--
ALTER TABLE `fresh_food_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_inventory_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_inventory_product_unique` (`tenant_id`,`product_id`),
  ADD KEY `fresh_food_inventory_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_inventory_business_index` (`fresh_food_id`),
  ADD KEY `fresh_food_inventory_product_index` (`product_id`);

--
-- Indexes for table `fresh_food_inventory_movements`
--
ALTER TABLE `fresh_food_inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_inventory_movements_uuid_unique` (`uuid`),
  ADD KEY `fresh_food_inventory_movements_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_inventory_movements_inventory_index` (`inventory_id`),
  ADD KEY `fresh_food_inventory_movements_product_index` (`product_id`),
  ADD KEY `fresh_food_inventory_movements_created_index` (`created_at`);

--
-- Indexes for table `fresh_food_orders`
--
ALTER TABLE `fresh_food_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_orders_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_orders_no_unique` (`tenant_id`,`order_no`),
  ADD KEY `fresh_food_orders_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_orders_business_index` (`fresh_food_id`),
  ADD KEY `fresh_food_orders_member_index` (`member_id`),
  ADD KEY `fresh_food_orders_status_index` (`tenant_id`,`status`),
  ADD KEY `fresh_food_orders_created_index` (`created_at`),
  ADD KEY `fk_fresh_food_orders_delivery_user` (`delivery_assigned_to`);

--
-- Indexes for table `fresh_food_order_items`
--
ALTER TABLE `fresh_food_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_order_items_uuid_unique` (`uuid`),
  ADD KEY `fresh_food_order_items_order_index` (`order_id`),
  ADD KEY `fresh_food_order_items_product_index` (`product_id`);

--
-- Indexes for table `fresh_food_order_status_history`
--
ALTER TABLE `fresh_food_order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_order_status_history_uuid_unique` (`uuid`),
  ADD KEY `fresh_food_order_status_history_order_index` (`order_id`),
  ADD KEY `fresh_food_order_status_history_created_index` (`created_at`),
  ADD KEY `fk_fresh_food_order_status_history_user` (`changed_by`);

--
-- Indexes for table `fresh_food_products`
--
ALTER TABLE `fresh_food_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_products_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_products_slug_unique` (`fresh_food_id`,`slug`),
  ADD KEY `fresh_food_products_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_products_business_index` (`fresh_food_id`),
  ADD KEY `fresh_food_products_category_index` (`category_id`);

--
-- Indexes for table `fresh_food_profiles`
--
ALTER TABLE `fresh_food_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fresh_food_profiles_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `fresh_food_profiles_business_unique` (`business_id`),
  ADD KEY `fresh_food_profiles_tenant_index` (`tenant_id`),
  ADD KEY `fresh_food_profiles_location_index` (`latitude`,`longitude`),
  ADD KEY `fresh_food_profiles_status_index` (`status`);

--
-- Indexes for table `lookup_types`
--
ALTER TABLE `lookup_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_lookup_types_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_lookup_types_tenant` (`tenant_id`),
  ADD KEY `idx_lookup_types_slug` (`slug`),
  ADD KEY `idx_lookup_types_status` (`status`);

--
-- Indexes for table `lookup_values`
--
ALTER TABLE `lookup_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_lookup_values_slug` (`tenant_id`,`lookup_type_id`,`slug`),
  ADD KEY `idx_lookup_values_tenant` (`tenant_id`),
  ADD KEY `idx_lookup_values_type` (`lookup_type_id`),
  ADD KEY `idx_lookup_values_slug` (`slug`),
  ADD KEY `idx_lookup_values_status` (`status`),
  ADD KEY `idx_lookup_values_order` (`display_order`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_member_no` (`member_no`),
  ADD KEY `idx_name` (`first_name`,`last_name`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_members_family` (`family_id`),
  ADD KEY `idx_members_tenant_family` (`tenant_id`,`family_id`),
  ADD KEY `idx_members_tenant_status` (`tenant_id`,`status`),
  ADD KEY `idx_members_tenant_deleted` (`tenant_id`,`deleted_at`);

--
-- Indexes for table `member_fresh_food_carts`
--
ALTER TABLE `member_fresh_food_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_fresh_food_carts_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `member_fresh_food_carts_member_business_unique` (`tenant_id`,`member_id`,`fresh_food_id`),
  ADD KEY `member_fresh_food_carts_member_index` (`tenant_id`,`member_id`),
  ADD KEY `member_fresh_food_carts_business_index` (`tenant_id`,`fresh_food_id`);

--
-- Indexes for table `member_notifications`
--
ALTER TABLE `member_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_member_notification_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_member_notification_event` (`tenant_id`,`member_id`,`source`,`source_event_id`),
  ADD KEY `idx_member_notifications_unread` (`tenant_id`,`member_id`,`read_at`,`created_at`),
  ADD KEY `idx_member_notifications_created` (`tenant_id`,`member_id`,`created_at`),
  ADD KEY `fk_member_notifications_member` (`member_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `migration` (`migration`);

--
-- Indexes for table `module_settings`
--
ALTER TABLE `module_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_module_setting` (`tenant_id`,`module`,`setting_key`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_module_group` (`module`,`setting_group`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_packages_uuid` (`uuid`),
  ADD UNIQUE KEY `uk_packages_code` (`tenant_id`,`code`),
  ADD KEY `idx_packages_tenant` (`tenant_id`),
  ADD KEY `idx_packages_status` (`status`),
  ADD KEY `fk_packages_installed_by` (`installed_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `restaurant_coupons`
--
ALTER TABLE `restaurant_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_coupons_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_coupons_code_unique` (`tenant_id`,`restaurant_id`,`code`),
  ADD KEY `restaurant_coupons_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_coupons_status_index` (`restaurant_id`,`status`);

--
-- Indexes for table `restaurant_hours`
--
ALTER TABLE `restaurant_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_hours_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_hours_day_unique` (`restaurant_id`,`day_of_week`),
  ADD KEY `restaurant_hours_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_hours_restaurant_index` (`restaurant_id`);

--
-- Indexes for table `restaurant_menu_categories`
--
ALTER TABLE `restaurant_menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_menu_categories_uuid_unique` (`uuid`),
  ADD KEY `restaurant_menu_categories_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_menu_categories_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_menu_categories_sort_index` (`restaurant_id`,`sort_order`),
  ADD KEY `restaurant_menu_categories_status_index` (`status`);

--
-- Indexes for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_menu_items_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_menu_items_restaurant_slug_unique` (`restaurant_id`,`slug`),
  ADD KEY `restaurant_menu_items_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_menu_items_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_menu_items_category_index` (`category_id`),
  ADD KEY `restaurant_menu_items_available_index` (`restaurant_id`,`is_available`),
  ADD KEY `restaurant_menu_items_sort_index` (`restaurant_id`,`category_id`,`sort_order`),
  ADD KEY `restaurant_menu_items_status_index` (`status`);

--
-- Indexes for table `restaurant_menu_item_modifier_groups`
--
ALTER TABLE `restaurant_menu_item_modifier_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_item_modifier_group_unique` (`item_id`,`modifier_group_id`),
  ADD KEY `restaurant_item_modifier_groups_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_item_modifier_groups_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_item_modifier_groups_item_index` (`item_id`),
  ADD KEY `restaurant_item_modifier_groups_group_index` (`modifier_group_id`);

--
-- Indexes for table `restaurant_menu_item_variants`
--
ALTER TABLE `restaurant_menu_item_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_menu_item_variants_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_menu_item_variants_item_name_unique` (`item_id`,`name`),
  ADD KEY `restaurant_menu_item_variants_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_menu_item_variants_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_menu_item_variants_item_index` (`item_id`),
  ADD KEY `restaurant_menu_item_variants_available_index` (`item_id`,`is_available`);

--
-- Indexes for table `restaurant_modifier_groups`
--
ALTER TABLE `restaurant_modifier_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_modifier_groups_uuid_unique` (`uuid`),
  ADD KEY `restaurant_modifier_groups_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_modifier_groups_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_modifier_groups_sort_index` (`restaurant_id`,`sort_order`);

--
-- Indexes for table `restaurant_modifier_options`
--
ALTER TABLE `restaurant_modifier_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_modifier_options_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_modifier_options_group_name_unique` (`modifier_group_id`,`name`),
  ADD KEY `restaurant_modifier_options_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_modifier_options_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_modifier_options_group_index` (`modifier_group_id`),
  ADD KEY `restaurant_modifier_options_available_index` (`modifier_group_id`,`is_available`);

--
-- Indexes for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_orders_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_orders_order_no_unique` (`tenant_id`,`order_no`),
  ADD KEY `restaurant_orders_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_orders_restaurant_index` (`restaurant_id`),
  ADD KEY `restaurant_orders_member_index` (`member_id`),
  ADD KEY `restaurant_orders_status_index` (`restaurant_id`,`status`),
  ADD KEY `restaurant_orders_created_index` (`restaurant_id`,`created_at`);

--
-- Indexes for table `restaurant_order_items`
--
ALTER TABLE `restaurant_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_order_items_uuid_unique` (`uuid`),
  ADD KEY `restaurant_order_items_order_index` (`order_id`),
  ADD KEY `restaurant_order_items_item_index` (`item_id`);

--
-- Indexes for table `restaurant_order_item_modifiers`
--
ALTER TABLE `restaurant_order_item_modifiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_order_item_modifiers_uuid_unique` (`uuid`),
  ADD KEY `restaurant_order_item_modifiers_item_index` (`order_item_id`);

--
-- Indexes for table `restaurant_order_status_history`
--
ALTER TABLE `restaurant_order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_order_status_history_uuid_unique` (`uuid`),
  ADD KEY `restaurant_order_status_history_order_index` (`tenant_id`,`order_id`,`id`),
  ADD KEY `restaurant_order_status_history_restaurant_index` (`tenant_id`,`restaurant_id`,`created_at`),
  ADD KEY `restaurant_order_status_history_changed_by_index` (`changed_by`),
  ADD KEY `fk_restaurant_order_status_history_order` (`order_id`);

--
-- Indexes for table `restaurant_profiles`
--
ALTER TABLE `restaurant_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_profiles_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `restaurant_profiles_business_unique` (`business_id`),
  ADD KEY `restaurant_profiles_tenant_index` (`tenant_id`),
  ADD KEY `restaurant_profiles_status_index` (`status`),
  ADD KEY `restaurant_profiles_accepting_orders_index` (`accepting_orders`),
  ADD KEY `restaurant_profiles_location_index` (`latitude`,`longitude`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `uk_role_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_roles_tenant` (`tenant_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_permission` (`permission_id`);

--
-- Indexes for table `taxi_bookings`
--
ALTER TABLE `taxi_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_taxi_booking_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_taxi_booking_no` (`tenant_id`,`booking_no`),
  ADD KEY `idx_taxi_booking_vendor` (`tenant_id`,`vendor_id`),
  ADD KEY `idx_taxi_booking_status` (`tenant_id`,`status`),
  ADD KEY `idx_taxi_booking_driver` (`tenant_id`,`driver_id`),
  ADD KEY `idx_taxi_booking_date` (`tenant_id`,`created_at`),
  ADD KEY `idx_taxi_booking_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_taxi_booking_vendor` (`vendor_id`),
  ADD KEY `fk_taxi_booking_service` (`service_id`),
  ADD KEY `fk_taxi_booking_vehicle` (`vehicle_id`),
  ADD KEY `fk_taxi_booking_driver` (`driver_id`),
  ADD KEY `fk_taxi_booking_created_by` (`created_by`),
  ADD KEY `fk_taxi_booking_updated_by` (`updated_by`),
  ADD KEY `idx_taxi_booking_member` (`tenant_id`,`member_id`),
  ADD KEY `fk_taxi_booking_member` (`member_id`),
  ADD KEY `fk_taxi_booking_route_commit_user` (`route_estimate_committed_by`);

--
-- Indexes for table `taxi_booking_status_history`
--
ALTER TABLE `taxi_booking_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_taxi_status_history_booking` (`tenant_id`,`booking_id`,`created_at`),
  ADD KEY `idx_taxi_status_history_actor` (`tenant_id`,`changed_by`),
  ADD KEY `fk_taxi_status_history_booking` (`booking_id`),
  ADD KEY `fk_taxi_status_history_user` (`changed_by`);

--
-- Indexes for table `taxi_drivers`
--
ALTER TABLE `taxi_drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_taxi_driver_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_taxi_driver_license` (`tenant_id`,`license_no`),
  ADD KEY `idx_taxi_driver_vendor` (`tenant_id`,`vendor_id`),
  ADD KEY `idx_taxi_driver_status` (`tenant_id`,`status`),
  ADD KEY `idx_taxi_driver_availability` (`tenant_id`,`availability`),
  ADD KEY `idx_taxi_driver_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_taxi_driver_vendor` (`vendor_id`),
  ADD KEY `fk_taxi_driver_user` (`user_id`),
  ADD KEY `fk_taxi_driver_created_by` (`created_by`),
  ADD KEY `fk_taxi_driver_updated_by` (`updated_by`);

--
-- Indexes for table `taxi_services`
--
ALTER TABLE `taxi_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_taxi_service_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_taxi_service_code` (`tenant_id`,`code`),
  ADD KEY `idx_taxi_service_vendor` (`tenant_id`,`vendor_id`),
  ADD KEY `idx_taxi_service_status` (`tenant_id`,`status`),
  ADD KEY `idx_taxi_service_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_taxi_service_vendor` (`vendor_id`),
  ADD KEY `fk_taxi_service_created_by` (`created_by`),
  ADD KEY `fk_taxi_service_updated_by` (`updated_by`);

--
-- Indexes for table `taxi_vehicles`
--
ALTER TABLE `taxi_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_taxi_vehicle_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_taxi_vehicle_registration` (`tenant_id`,`registration_no`),
  ADD KEY `idx_taxi_vehicle_vendor` (`tenant_id`,`vendor_id`),
  ADD KEY `idx_taxi_vehicle_status` (`tenant_id`,`status`),
  ADD KEY `idx_taxi_vehicle_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_taxi_vehicle_vendor` (`vendor_id`),
  ADD KEY `fk_taxi_vehicle_created_by` (`created_by`),
  ADD KEY `fk_taxi_vehicle_updated_by` (`updated_by`);

--
-- Indexes for table `taxi_vendors`
--
ALTER TABLE `taxi_vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_taxi_vendor_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_taxi_vendor_business` (`business_id`),
  ADD KEY `idx_taxi_vendor_tenant_status` (`tenant_id`,`status`),
  ADD KEY `idx_taxi_vendor_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_taxi_vendor_created_by` (`created_by`),
  ADD KEY `fk_taxi_vendor_updated_by` (`updated_by`),
  ADD KEY `idx_taxi_vendor_business` (`tenant_id`,`business_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `textbooks`
--
ALTER TABLE `textbooks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_uuid` (`uuid`),
  ADD KEY `idx_textbook_board` (`board_id`),
  ADD KEY `idx_textbook_subject` (`subject_id`),
  ADD KEY `idx_textbook_class` (`class_id`);

--
-- Indexes for table `textbook_chapters`
--
ALTER TABLE `textbook_chapters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_chapters_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_textbook_chapter_number_active` (`textbook_id`,`chapter_number`,`deleted_at`),
  ADD KEY `idx_textbook_chapters_textbook` (`textbook_id`);

--
-- Indexes for table `textbook_chapter_ingestions`
--
ALTER TABLE `textbook_chapter_ingestions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_chapter_ingestion_uuid` (`uuid`),
  ADD KEY `idx_tci_revision` (`chapter_revision_id`),
  ADD KEY `idx_tci_status` (`status`),
  ADD KEY `idx_tci_job` (`job_id`),
  ADD KEY `fk_tci_reviewed_by` (`reviewed_by`),
  ADD KEY `fk_tci_approved_by` (`approved_by`),
  ADD KEY `fk_tci_created_by` (`created_by`),
  ADD KEY `fk_tci_updated_by` (`updated_by`);

--
-- Indexes for table `textbook_chapter_ingestion_jobs`
--
ALTER TABLE `textbook_chapter_ingestion_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tcij_uuid` (`uuid`),
  ADD KEY `idx_tcij_ingestion` (`ingestion_id`);

--
-- Indexes for table `textbook_chapter_learning_intelligence`
--
ALTER TABLE `textbook_chapter_learning_intelligence`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_chapter_learning_intelligence_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_textbook_chapter_learning_intelligence_ingestion` (`ingestion_id`),
  ADD KEY `idx_textbook_chapter_learning_intelligence_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_textbook_chapter_learning_intelligence_created_by` (`created_by`);

--
-- Indexes for table `textbook_chapter_learning_intelligence_jobs`
--
ALTER TABLE `textbook_chapter_learning_intelligence_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tclij_uuid` (`uuid`),
  ADD KEY `idx_tclij_ingestion` (`ingestion_id`);

--
-- Indexes for table `textbook_chapter_revisions`
--
ALTER TABLE `textbook_chapter_revisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_chapter_revisions_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_textbook_chapter_revision_number` (`chapter_id`,`revision_number`),
  ADD KEY `idx_textbook_chapter_revisions_document` (`document_id`),
  ADD KEY `idx_textbook_chapter_revisions_hash` (`chapter_id`,`file_hash`);

--
-- Indexes for table `textbook_editions`
--
ALTER TABLE `textbook_editions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_edition_uuid` (`uuid`),
  ADD KEY `idx_textbook_edition_textbook` (`textbook_id`),
  ADD KEY `idx_textbook_edition_document` (`document_id`);

--
-- Indexes for table `textbook_pages`
--
ALTER TABLE `textbook_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_textbook_page` (`edition_id`,`page_number`),
  ADD KEY `fk_textbook_page_document_page` (`document_page_id`);

--
-- Indexes for table `tourism_destinations`
--
ALTER TABLE `tourism_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_destination_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_destination_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_destination_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_destination_district` (`tenant_id`,`district`),
  ADD KEY `idx_tourism_destination_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_destination_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_destination_created` (`created_by`),
  ADD KEY `fk_tourism_destination_updated` (`updated_by`);

--
-- Indexes for table `tourism_events`
--
ALTER TABLE `tourism_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_event_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_event_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_event_date` (`tenant_id`,`start_at`),
  ADD KEY `idx_tourism_event_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_event_status` (`tenant_id`,`status`,`deleted_at`),
  ADD KEY `fk_tourism_event_provider` (`provider_id`),
  ADD KEY `fk_tourism_event_created` (`created_by`),
  ADD KEY `fk_tourism_event_updated` (`updated_by`);

--
-- Indexes for table `tourism_experiences`
--
ALTER TABLE `tourism_experiences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_experience_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_experience_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_experience_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_experience_destination` (`tenant_id`,`destination_id`),
  ADD KEY `idx_tourism_experience_status` (`tenant_id`,`status`,`deleted_at`),
  ADD KEY `fk_tourism_experience_provider` (`provider_id`),
  ADD KEY `fk_tourism_experience_destination` (`destination_id`),
  ADD KEY `fk_tourism_experience_created` (`created_by`),
  ADD KEY `fk_tourism_experience_updated` (`updated_by`);

--
-- Indexes for table `tourism_guides`
--
ALTER TABLE `tourism_guides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_guide_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_guide_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_guide_business` (`tenant_id`,`business_id`),
  ADD KEY `idx_tourism_guide_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_guide_district` (`tenant_id`,`district`),
  ADD KEY `idx_tourism_guide_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_guide_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_guide_business` (`business_id`),
  ADD KEY `fk_tourism_guide_created` (`created_by`),
  ADD KEY `fk_tourism_guide_updated` (`updated_by`);

--
-- Indexes for table `tourism_packages`
--
ALTER TABLE `tourism_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_package_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_package_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_package_provider` (`tenant_id`,`provider_id`),
  ADD KEY `idx_tourism_package_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_package_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_package_provider` (`provider_id`),
  ADD KEY `fk_tourism_package_created` (`created_by`),
  ADD KEY `fk_tourism_package_updated` (`updated_by`);

--
-- Indexes for table `tourism_package_destinations`
--
ALTER TABLE `tourism_package_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_package_destination` (`package_id`,`destination_id`),
  ADD UNIQUE KEY `uq_tourism_package_destination_uuid` (`uuid`),
  ADD KEY `idx_tourism_package_destination_tenant` (`tenant_id`),
  ADD KEY `idx_tourism_package_destination_destination` (`tenant_id`,`destination_id`),
  ADD KEY `fk_tourism_package_destination_destination` (`destination_id`);

--
-- Indexes for table `tourism_providers`
--
ALTER TABLE `tourism_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_provider_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_provider_business` (`tenant_id`,`business_id`),
  ADD KEY `idx_tourism_provider_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_provider_district` (`tenant_id`,`district`),
  ADD KEY `idx_tourism_provider_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_provider_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_provider_business` (`business_id`),
  ADD KEY `fk_tourism_provider_created` (`created_by`),
  ADD KEY `fk_tourism_provider_updated` (`updated_by`);

--
-- Indexes for table `tourism_reviews`
--
ALTER TABLE `tourism_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_review_uuid` (`uuid`),
  ADD KEY `idx_tourism_review_target` (`tenant_id`,`reviewable_type`,`reviewable_id`,`status`),
  ADD KEY `idx_tourism_review_user` (`tenant_id`,`user_id`),
  ADD KEY `fk_tourism_review_user` (`user_id`);

--
-- Indexes for table `tourism_stays`
--
ALTER TABLE `tourism_stays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_stay_uuid` (`uuid`),
  ADD UNIQUE KEY `uq_tourism_stay_slug` (`tenant_id`,`slug`),
  ADD KEY `idx_tourism_stay_provider` (`tenant_id`,`provider_id`),
  ADD KEY `idx_tourism_stay_destination` (`tenant_id`,`destination_id`),
  ADD KEY `idx_tourism_stay_location` (`tenant_id`,`latitude`,`longitude`),
  ADD KEY `idx_tourism_stay_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_stay_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_stay_provider` (`provider_id`),
  ADD KEY `fk_tourism_stay_destination` (`destination_id`),
  ADD KEY `fk_tourism_stay_created` (`created_by`),
  ADD KEY `fk_tourism_stay_updated` (`updated_by`);

--
-- Indexes for table `tourism_stay_room_types`
--
ALTER TABLE `tourism_stay_room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_room_uuid` (`uuid`),
  ADD KEY `idx_tourism_room_stay` (`tenant_id`,`stay_id`),
  ADD KEY `idx_tourism_room_status` (`tenant_id`,`status`),
  ADD KEY `idx_tourism_room_deleted` (`tenant_id`,`deleted_at`),
  ADD KEY `fk_tourism_room_stay` (`stay_id`),
  ADD KEY `fk_tourism_room_created` (`created_by`),
  ADD KEY `fk_tourism_room_updated` (`updated_by`);

--
-- Indexes for table `tourism_trip_plans`
--
ALTER TABLE `tourism_trip_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_trip_uuid` (`uuid`),
  ADD KEY `idx_tourism_trip_user` (`tenant_id`,`user_id`),
  ADD KEY `idx_tourism_trip_status` (`tenant_id`,`status`),
  ADD KEY `fk_tourism_trip_user` (`user_id`);

--
-- Indexes for table `tourism_trip_plan_items`
--
ALTER TABLE `tourism_trip_plan_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tourism_trip_item_uuid` (`uuid`),
  ADD KEY `idx_tourism_trip_item` (`tenant_id`,`trip_plan_id`,`day_number`,`sequence_no`),
  ADD KEY `fk_tourism_trip_item_plan` (`trip_plan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `uk_users_email` (`tenant_id`,`email`),
  ADD KEY `idx_users_tenant` (`tenant_id`),
  ADD KEY `idx_users_role` (`role_id`),
  ADD KEY `idx_users_tenant_member` (`tenant_id`,`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_questions`
--
ALTER TABLE `ai_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `commercial_rental_categories`
--
ALTER TABLE `commercial_rental_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `commercial_rental_providers`
--
ALTER TABLE `commercial_rental_providers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commercial_rental_requests`
--
ALTER TABLE `commercial_rental_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commercial_rental_request_status_history`
--
ALTER TABLE `commercial_rental_request_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `commercial_rental_vehicles`
--
ALTER TABLE `commercial_rental_vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fresh_food_categories`
--
ALTER TABLE `fresh_food_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fresh_food_coupons`
--
ALTER TABLE `fresh_food_coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fresh_food_hours`
--
ALTER TABLE `fresh_food_hours`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fresh_food_inventory`
--
ALTER TABLE `fresh_food_inventory`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fresh_food_inventory_movements`
--
ALTER TABLE `fresh_food_inventory_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fresh_food_orders`
--
ALTER TABLE `fresh_food_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fresh_food_order_items`
--
ALTER TABLE `fresh_food_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fresh_food_order_status_history`
--
ALTER TABLE `fresh_food_order_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `fresh_food_products`
--
ALTER TABLE `fresh_food_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fresh_food_profiles`
--
ALTER TABLE `fresh_food_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lookup_types`
--
ALTER TABLE `lookup_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lookup_values`
--
ALTER TABLE `lookup_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `member_fresh_food_carts`
--
ALTER TABLE `member_fresh_food_carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `member_notifications`
--
ALTER TABLE `member_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274325;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `module_settings`
--
ALTER TABLE `module_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `restaurant_coupons`
--
ALTER TABLE `restaurant_coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restaurant_hours`
--
ALTER TABLE `restaurant_hours`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_menu_categories`
--
ALTER TABLE `restaurant_menu_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `restaurant_menu_item_modifier_groups`
--
ALTER TABLE `restaurant_menu_item_modifier_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `restaurant_menu_item_variants`
--
ALTER TABLE `restaurant_menu_item_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `restaurant_modifier_groups`
--
ALTER TABLE `restaurant_modifier_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `restaurant_modifier_options`
--
ALTER TABLE `restaurant_modifier_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurant_order_items`
--
ALTER TABLE `restaurant_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurant_order_item_modifiers`
--
ALTER TABLE `restaurant_order_item_modifiers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restaurant_order_status_history`
--
ALTER TABLE `restaurant_order_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `restaurant_profiles`
--
ALTER TABLE `restaurant_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `taxi_bookings`
--
ALTER TABLE `taxi_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `taxi_booking_status_history`
--
ALTER TABLE `taxi_booking_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `taxi_drivers`
--
ALTER TABLE `taxi_drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxi_services`
--
ALTER TABLE `taxi_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `taxi_vehicles`
--
ALTER TABLE `taxi_vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `taxi_vendors`
--
ALTER TABLE `taxi_vendors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `textbooks`
--
ALTER TABLE `textbooks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `textbook_chapters`
--
ALTER TABLE `textbook_chapters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `textbook_chapter_ingestions`
--
ALTER TABLE `textbook_chapter_ingestions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `textbook_chapter_ingestion_jobs`
--
ALTER TABLE `textbook_chapter_ingestion_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `textbook_chapter_learning_intelligence`
--
ALTER TABLE `textbook_chapter_learning_intelligence`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `textbook_chapter_learning_intelligence_jobs`
--
ALTER TABLE `textbook_chapter_learning_intelligence_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `textbook_chapter_revisions`
--
ALTER TABLE `textbook_chapter_revisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `textbook_editions`
--
ALTER TABLE `textbook_editions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `textbook_pages`
--
ALTER TABLE `textbook_pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tourism_destinations`
--
ALTER TABLE `tourism_destinations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tourism_events`
--
ALTER TABLE `tourism_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tourism_experiences`
--
ALTER TABLE `tourism_experiences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourism_guides`
--
ALTER TABLE `tourism_guides`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tourism_packages`
--
ALTER TABLE `tourism_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tourism_package_destinations`
--
ALTER TABLE `tourism_package_destinations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tourism_providers`
--
ALTER TABLE `tourism_providers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tourism_reviews`
--
ALTER TABLE `tourism_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tourism_stays`
--
ALTER TABLE `tourism_stays`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tourism_stay_room_types`
--
ALTER TABLE `tourism_stay_room_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tourism_trip_plans`
--
ALTER TABLE `tourism_trip_plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourism_trip_plan_items`
--
ALTER TABLE `tourism_trip_plan_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commercial_rental_categories`
--
ALTER TABLE `commercial_rental_categories`
  ADD CONSTRAINT `fk_cr_category_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_category_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_category_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commercial_rental_providers`
--
ALTER TABLE `commercial_rental_providers`
  ADD CONSTRAINT `fk_cr_provider_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_provider_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_provider_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_provider_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commercial_rental_requests`
--
ALTER TABLE `commercial_rental_requests`
  ADD CONSTRAINT `fk_cr_request_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_request_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_request_provider` FOREIGN KEY (`provider_id`) REFERENCES `commercial_rental_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_request_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_request_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_request_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `commercial_rental_vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commercial_rental_vehicles`
--
ALTER TABLE `commercial_rental_vehicles`
  ADD CONSTRAINT `fk_cr_vehicle_category` FOREIGN KEY (`category_id`) REFERENCES `commercial_rental_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_vehicle_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cr_vehicle_provider` FOREIGN KEY (`provider_id`) REFERENCES `commercial_rental_providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_vehicle_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cr_vehicle_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fresh_food_categories`
--
ALTER TABLE `fresh_food_categories`
  ADD CONSTRAINT `fk_fresh_food_categories_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_coupons`
--
ALTER TABLE `fresh_food_coupons`
  ADD CONSTRAINT `fk_fresh_food_coupons_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_hours`
--
ALTER TABLE `fresh_food_hours`
  ADD CONSTRAINT `fk_fresh_food_hours_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_inventory`
--
ALTER TABLE `fresh_food_inventory`
  ADD CONSTRAINT `fk_fresh_food_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `fresh_food_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_inventory_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_inventory_movements`
--
ALTER TABLE `fresh_food_inventory_movements`
  ADD CONSTRAINT `fk_fresh_food_inventory_movements_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `fresh_food_inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_orders`
--
ALTER TABLE `fresh_food_orders`
  ADD CONSTRAINT `fk_fresh_food_orders_delivery_user` FOREIGN KEY (`delivery_assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_orders_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_orders_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_order_items`
--
ALTER TABLE `fresh_food_order_items`
  ADD CONSTRAINT `fk_fresh_food_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `fresh_food_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `fresh_food_products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_order_status_history`
--
ALTER TABLE `fresh_food_order_status_history`
  ADD CONSTRAINT `fk_fresh_food_order_status_history_order` FOREIGN KEY (`order_id`) REFERENCES `fresh_food_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_order_status_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_products`
--
ALTER TABLE `fresh_food_products`
  ADD CONSTRAINT `fk_fresh_food_products_category` FOREIGN KEY (`category_id`) REFERENCES `fresh_food_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fresh_food_products_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fresh_food_profiles`
--
ALTER TABLE `fresh_food_profiles`
  ADD CONSTRAINT `fk_fresh_food_profiles_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `member_notifications`
--
ALTER TABLE `member_notifications`
  ADD CONSTRAINT `fk_member_notifications_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_coupons`
--
ALTER TABLE `restaurant_coupons`
  ADD CONSTRAINT `fk_restaurant_coupons_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_hours`
--
ALTER TABLE `restaurant_hours`
  ADD CONSTRAINT `fk_restaurant_hours_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_menu_categories`
--
ALTER TABLE `restaurant_menu_categories`
  ADD CONSTRAINT `fk_restaurant_menu_categories_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_menu_items`
--
ALTER TABLE `restaurant_menu_items`
  ADD CONSTRAINT `fk_restaurant_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `restaurant_menu_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_menu_items_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_menu_item_modifier_groups`
--
ALTER TABLE `restaurant_menu_item_modifier_groups`
  ADD CONSTRAINT `fk_restaurant_item_modifier_groups_group` FOREIGN KEY (`modifier_group_id`) REFERENCES `restaurant_modifier_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_item_modifier_groups_item` FOREIGN KEY (`item_id`) REFERENCES `restaurant_menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_item_modifier_groups_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_menu_item_variants`
--
ALTER TABLE `restaurant_menu_item_variants`
  ADD CONSTRAINT `fk_restaurant_menu_item_variants_item` FOREIGN KEY (`item_id`) REFERENCES `restaurant_menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_menu_item_variants_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_modifier_groups`
--
ALTER TABLE `restaurant_modifier_groups`
  ADD CONSTRAINT `fk_restaurant_modifier_groups_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_modifier_options`
--
ALTER TABLE `restaurant_modifier_options`
  ADD CONSTRAINT `fk_restaurant_modifier_options_group` FOREIGN KEY (`modifier_group_id`) REFERENCES `restaurant_modifier_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_modifier_options_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  ADD CONSTRAINT `fk_restaurant_orders_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_restaurant_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_profiles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_order_items`
--
ALTER TABLE `restaurant_order_items`
  ADD CONSTRAINT `fk_restaurant_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_order_item_modifiers`
--
ALTER TABLE `restaurant_order_item_modifiers`
  ADD CONSTRAINT `fk_restaurant_order_item_modifiers_item` FOREIGN KEY (`order_item_id`) REFERENCES `restaurant_order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_order_status_history`
--
ALTER TABLE `restaurant_order_status_history`
  ADD CONSTRAINT `fk_restaurant_order_status_history_order` FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_profiles`
--
ALTER TABLE `restaurant_profiles`
  ADD CONSTRAINT `fk_restaurant_profiles_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `taxi_bookings`
--
ALTER TABLE `taxi_bookings`
  ADD CONSTRAINT `fk_taxi_booking_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_driver` FOREIGN KEY (`driver_id`) REFERENCES `taxi_drivers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_route_commit_user` FOREIGN KEY (`route_estimate_committed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_service` FOREIGN KEY (`service_id`) REFERENCES `taxi_services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_booking_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `taxi_vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_booking_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `taxi_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxi_booking_status_history`
--
ALTER TABLE `taxi_booking_status_history`
  ADD CONSTRAINT `fk_taxi_status_history_booking` FOREIGN KEY (`booking_id`) REFERENCES `taxi_bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_status_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `taxi_drivers`
--
ALTER TABLE `taxi_drivers`
  ADD CONSTRAINT `fk_taxi_driver_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_driver_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_driver_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_driver_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_driver_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `taxi_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxi_services`
--
ALTER TABLE `taxi_services`
  ADD CONSTRAINT `fk_taxi_service_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_service_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_service_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_service_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `taxi_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxi_vehicles`
--
ALTER TABLE `taxi_vehicles`
  ADD CONSTRAINT `fk_taxi_vehicle_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_vehicle_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_vehicle_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_vehicle_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `taxi_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxi_vendors`
--
ALTER TABLE `taxi_vendors`
  ADD CONSTRAINT `fk_taxi_vendor_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_vendor_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_taxi_vendor_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taxi_vendor_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_destinations`
--
ALTER TABLE `tourism_destinations`
  ADD CONSTRAINT `fk_tourism_destination_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_destination_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_destination_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_events`
--
ALTER TABLE `tourism_events`
  ADD CONSTRAINT `fk_tourism_event_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_event_provider` FOREIGN KEY (`provider_id`) REFERENCES `tourism_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_event_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_event_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_experiences`
--
ALTER TABLE `tourism_experiences`
  ADD CONSTRAINT `fk_tourism_experience_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_experience_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourism_destinations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_experience_provider` FOREIGN KEY (`provider_id`) REFERENCES `tourism_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_experience_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_experience_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_guides`
--
ALTER TABLE `tourism_guides`
  ADD CONSTRAINT `fk_tourism_guide_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_guide_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_guide_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_guide_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_packages`
--
ALTER TABLE `tourism_packages`
  ADD CONSTRAINT `fk_tourism_package_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_package_provider` FOREIGN KEY (`provider_id`) REFERENCES `tourism_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_package_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_package_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_package_destinations`
--
ALTER TABLE `tourism_package_destinations`
  ADD CONSTRAINT `fk_tourism_package_destination_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourism_destinations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_package_destination_package` FOREIGN KEY (`package_id`) REFERENCES `tourism_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_package_destination_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tourism_providers`
--
ALTER TABLE `tourism_providers`
  ADD CONSTRAINT `fk_tourism_provider_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_provider_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_provider_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_provider_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_reviews`
--
ALTER TABLE `tourism_reviews`
  ADD CONSTRAINT `fk_tourism_review_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_stays`
--
ALTER TABLE `tourism_stays`
  ADD CONSTRAINT `fk_tourism_stay_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_stay_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourism_destinations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_stay_provider` FOREIGN KEY (`provider_id`) REFERENCES `tourism_providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_stay_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_stay_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_stay_room_types`
--
ALTER TABLE `tourism_stay_room_types`
  ADD CONSTRAINT `fk_tourism_room_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tourism_room_stay` FOREIGN KEY (`stay_id`) REFERENCES `tourism_stays` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_room_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_room_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_trip_plans`
--
ALTER TABLE `tourism_trip_plans`
  ADD CONSTRAINT `fk_tourism_trip_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_trip_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tourism_trip_plan_items`
--
ALTER TABLE `tourism_trip_plan_items`
  ADD CONSTRAINT `fk_tourism_trip_item_plan` FOREIGN KEY (`trip_plan_id`) REFERENCES `tourism_trip_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tourism_trip_item_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
