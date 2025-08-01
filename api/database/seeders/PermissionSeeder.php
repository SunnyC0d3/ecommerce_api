<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management - Admin Level
            ['name' => 'view_all_users'],
            ['name' => 'create_all_users'],
            ['name' => 'edit_all_users'],
            ['name' => 'delete_all_users'],
            ['name' => 'restore_users'],
            ['name' => 'force_delete_users'],

            // User Management - User Level
            ['name' => 'view_own_profile'],
            ['name' => 'edit_own_profile'],
            ['name' => 'delete_own_account'],
            ['name' => 'create_user_account'], // For user registration

            // Role Management
            ['name' => 'view_roles'],
            ['name' => 'create_roles'],
            ['name' => 'edit_roles'],
            ['name' => 'delete_roles'],

            // Permission Management
            ['name' => 'view_permissions'],
            ['name' => 'create_permissions'],
            ['name' => 'edit_permissions'],
            ['name' => 'delete_permissions'],

            // Vendor Management - Admin Level
            ['name' => 'view_all_vendors'],
            ['name' => 'create_vendors_for_users'],
            ['name' => 'edit_all_vendors'],
            ['name' => 'delete_all_vendors'],
            ['name' => 'restore_vendors'],
            ['name' => 'force_delete_vendors'],

            // Vendor Management - Vendor Level
            ['name' => 'view_own_vendor'],
            ['name' => 'create_own_vendor'],
            ['name' => 'edit_own_vendor'],
            ['name' => 'delete_own_vendor'],

            // Vendor Management - Public Level
            ['name' => 'view_vendors_public'],

            // Product Management
            ['name' => 'view_products'],
            ['name' => 'create_products'],
            ['name' => 'edit_products'],
            ['name' => 'delete_products'],
            ['name' => 'restore_products'],
            ['name' => 'force_delete_products'],

            // Product Attribute
            ['name' => 'view_product_attributes'],
            ['name' => 'create_product_attributes'],
            ['name' => 'edit_product_attributes'],
            ['name' => 'delete_product_attributes'],

            // Product Category
            ['name' => 'view_product_categories'],
            ['name' => 'create_product_categories'],
            ['name' => 'edit_product_categories'],
            ['name' => 'delete_product_categories'],

            // Product Status
            ['name' => 'view_product_statuses'],
            ['name' => 'create_product_statuses'],
            ['name' => 'edit_product_statuses'],
            ['name' => 'delete_product_statuses'],

            // Product Tag
            ['name' => 'view_product_tags'],
            ['name' => 'create_product_tags'],
            ['name' => 'edit_product_tags'],
            ['name' => 'delete_product_tags'],

            // Category Management
            ['name' => 'view_categories'],
            ['name' => 'create_categories'],
            ['name' => 'edit_categories'],
            ['name' => 'delete_categories'],

            // Order Management - Admin/Staff Level
            ['name' => 'view_all_orders'],
            ['name' => 'create_orders_for_users'],
            ['name' => 'edit_all_orders'],
            ['name' => 'delete_all_orders'],
            ['name' => 'restore_orders'],
            ['name' => 'force_delete_orders'],

            // Order Management - User Level
            ['name' => 'view_own_orders'],
            ['name' => 'create_own_orders'],
            ['name' => 'edit_own_orders'],
            ['name' => 'delete_own_orders'],

            // Payment Methods
            ['name' => 'view_payment_methods'],
            ['name' => 'create_payment_methods'],
            ['name' => 'edit_payment_methods'],
            ['name' => 'delete_payment_methods'],

            // Payment
            ['name' => 'view_payments'],
            ['name' => 'create_payments'],
            ['name' => 'edit_payments'],
            ['name' => 'delete_payments'],

            // Customer Service
            ['name' => 'view_customer_data'],
            ['name' => 'manage_refunds'],
            ['name' => 'manage_returns'],

            // Inventory Management
            ['name' => 'view_inventory'],
            ['name' => 'edit_inventory'],
            ['name' => 'manage_inventory'],
            ['name' => 'update_stock_thresholds'],
            ['name' => 'bulk_update_inventory'],
            ['name' => 'trigger_inventory_alerts'],
            ['name' => 'view_inventory_reports'],
            ['name' => 'manage_stock_alerts'],

            // Public Review Permissions
            ['name' => 'view_reviews', 'description' => 'View reviews and ratings'],
            ['name' => 'create_reviews', 'description' => 'Create new reviews'],
            ['name' => 'edit_own_reviews', 'description' => 'Edit own reviews within time limit'],
            ['name' => 'delete_own_reviews', 'description' => 'Delete own reviews'],
            ['name' => 'vote_review_helpfulness', 'description' => 'Vote on review helpfulness'],
            ['name' => 'report_reviews', 'description' => 'Report inappropriate reviews'],

            // Vendor Review Permissions
            ['name' => 'respond_to_reviews', 'description' => 'Respond to reviews on vendor products'],
            ['name' => 'edit_own_responses', 'description' => 'Edit own review responses within time limit'],
            ['name' => 'delete_own_responses', 'description' => 'Delete own review responses'],
            ['name' => 'view_product_reviews', 'description' => 'View reviews for vendor products'],

            // Admin Review Management
            ['name' => 'manage_reviews', 'description' => 'Full review management access'],
            ['name' => 'moderate_reviews', 'description' => 'Moderate and approve/reject reviews'],
            ['name' => 'delete_reviews', 'description' => 'Delete any review permanently'],
            ['name' => 'bulk_moderate_reviews', 'description' => 'Perform bulk moderation actions'],
            ['name' => 'feature_reviews', 'description' => 'Feature/unfeature reviews'],

            // Review Reports Management
            ['name' => 'view_review_reports', 'description' => 'View reported reviews'],
            ['name' => 'handle_review_reports', 'description' => 'Resolve or dismiss review reports'],
            ['name' => 'manage_review_reports', 'description' => 'Full review reports management'],

            // Review Analytics
            ['name' => 'view_analytics', 'description' => 'View review analytics and statistics'],
            ['name' => 'view_review_trends', 'description' => 'View review trends and insights'],
            ['name' => 'export_review_data', 'description' => 'Export review data and reports'],

            // Review Response Management (Admin)
            ['name' => 'manage_review_responses', 'description' => 'Manage all vendor responses'],
            ['name' => 'approve_review_responses', 'description' => 'Approve/reject vendor responses'],
            ['name' => 'delete_review_responses', 'description' => 'Delete any review response'],

            // Shipping Method Management
            ['name' => 'view_shipping_methods'],
            ['name' => 'create_shipping_methods'],
            ['name' => 'edit_shipping_methods'],
            ['name' => 'delete_shipping_methods'],

            // Shipping Zone Management
            ['name' => 'view_shipping_zones'],
            ['name' => 'create_shipping_zones'],
            ['name' => 'edit_shipping_zones'],
            ['name' => 'delete_shipping_zones'],

            // Shipping Rate Management
            ['name' => 'view_shipping_rates'],
            ['name' => 'create_shipping_rates'],
            ['name' => 'edit_shipping_rates'],
            ['name' => 'delete_shipping_rates'],

            // Shipment Management
            ['name' => 'view_shipments'],
            ['name' => 'create_shipments'],
            ['name' => 'edit_shipments'],
            ['name' => 'delete_shipments'],
            ['name' => 'manage_shipments'],

            // Shipping Address Management
            ['name' => 'view_shipping_addresses'],
            ['name' => 'create_shipping_addresses'],
            ['name' => 'edit_shipping_addresses'],
            ['name' => 'delete_shipping_addresses'],

            // Shipping Operations
            ['name' => 'calculate_shipping'],
            ['name' => 'validate_addresses'],
            ['name' => 'purchase_labels'],
            ['name' => 'track_shipments'],

            // Dropshipping - Supplier Management
            ['name' => 'view_suppliers'],
            ['name' => 'create_suppliers'],
            ['name' => 'edit_suppliers'],
            ['name' => 'delete_suppliers'],
            ['name' => 'approve_suppliers'],
            ['name' => 'manage_supplier_integrations'],
            ['name' => 'test_supplier_connections'],

            // Dropshipping - Supplier Products
            ['name' => 'view_supplier_products'],
            ['name' => 'sync_supplier_products'],
            ['name' => 'map_supplier_products'],
            ['name' => 'bulk_update_supplier_products'],
            ['name' => 'manage_product_mappings'],

            // Dropshipping - Orders
            ['name' => 'view_dropship_orders'],
            ['name' => 'create_dropship_orders'],
            ['name' => 'edit_dropship_orders'],
            ['name' => 'cancel_dropship_orders'],
            ['name' => 'retry_dropship_orders'],
            ['name' => 'bulk_manage_dropship_orders'],

            // Dropshipping - Integrations
            ['name' => 'view_supplier_integrations'],
            ['name' => 'create_supplier_integrations'],
            ['name' => 'edit_supplier_integrations'],
            ['name' => 'delete_supplier_integrations'],
            ['name' => 'test_integrations'],
            ['name' => 'view_integration_logs'],

            // Dropshipping - Analytics & Reporting
            ['name' => 'view_dropshipping_analytics'],
            ['name' => 'view_supplier_performance'],
            ['name' => 'view_profit_margins'],
            ['name' => 'export_dropshipping_data'],

            // Dropshipping - Automation & Configuration
            ['name' => 'manage_automated_fulfillment'],
            ['name' => 'configure_sync_settings'],
            ['name' => 'manage_markup_rules'],
            ['name' => 'override_supplier_prices'],

            // Dropshipping - Settings
            ['name' => 'view_dropshipping_settings'],
            ['name' => 'edit_dropshipping_settings'],
            ['name' => 'manage_default_markups'],
            ['name' => 'configure_automation_rules'],

            ['name' => 'view_digital_products'],
            ['name' => 'create_digital_products'],
            ['name' => 'edit_digital_products'],
            ['name' => 'delete_digital_products'],
            ['name' => 'manage_digital_products'],
            ['name' => 'upload_digital_files'],
            ['name' => 'download_digital_files'],

            // Digital Product Files
            ['name' => 'view_product_files'],
            ['name' => 'create_product_files'],
            ['name' => 'edit_product_files'],
            ['name' => 'delete_product_files'],
            ['name' => 'manage_product_files'],

            // Download Access Management
            ['name' => 'view_download_access'],
            ['name' => 'create_download_access'],
            ['name' => 'edit_download_access'],
            ['name' => 'revoke_download_access'],
            ['name' => 'manage_download_access'],

            // License Management
            ['name' => 'view_license_keys'],
            ['name' => 'create_license_keys'],
            ['name' => 'edit_license_keys'],
            ['name' => 'revoke_license_keys'],
            ['name' => 'validate_license_keys'],
            ['name' => 'manage_license_keys'],

            // Digital Product Analytics
            ['name' => 'view_download_analytics'],
            ['name' => 'view_digital_product_reports'],
            ['name' => 'export_digital_product_data'],
        ];

        $permissionsToInsert = array_map(function ($permission) {
            return [
                'name'       => $permission['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $permissions);

        Permission::insert($permissionsToInsert);
    }
}
