<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [
            'Super Admin' => '*',

            'Admin' => [
                // User Management - Full Access
                'view_all_users', 'create_all_users', 'edit_all_users', 'delete_all_users', 'restore_users', 'force_delete_users',

                // Role & Permission Management
                'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
                'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions',

                // Vendor Management - Full Access
                'view_all_vendors', 'create_vendors_for_users', 'edit_all_vendors', 'delete_all_vendors', 'restore_vendors', 'force_delete_vendors',

                // Product Management
                'view_products', 'create_products', 'edit_products', 'delete_products', 'restore_products', 'force_delete_products',
                'view_product_attributes', 'create_product_attributes', 'edit_product_attributes', 'delete_product_attributes',
                'view_product_categories', 'create_product_categories', 'edit_product_categories', 'delete_product_categories',
                'view_product_tags', 'create_product_tags', 'edit_product_tags', 'delete_product_tags',
                'view_product_statuses', 'create_product_statuses', 'edit_product_statuses', 'delete_product_statuses',

                // Category Management
                'view_categories', 'create_categories', 'edit_categories', 'delete_categories',

                // Order Management - Full Access
                'view_all_orders', 'create_orders_for_users', 'edit_all_orders', 'delete_all_orders', 'restore_orders', 'force_delete_orders',

                // Customer Service
                'view_customer_data', 'manage_refunds', 'manage_returns',

                // Payment Management
                'view_payments', 'create_payments', 'edit_payments', 'delete_payments',
                'view_payment_methods', 'create_payment_methods', 'edit_payment_methods', 'delete_payment_methods',

                // Inventory Management - Full Access
                'view_inventory', 'edit_inventory', 'manage_inventory', 'update_stock_thresholds', 'bulk_update_inventory', 'trigger_inventory_alerts', 'view_inventory_reports', 'manage_stock_alerts',

                // Review Management - Full Access
                'view_reviews', 'create_reviews', 'edit_own_reviews', 'delete_own_reviews',
                'vote_review_helpfulness', 'report_reviews', 'manage_reviews',
                'moderate_reviews', 'delete_reviews', 'bulk_moderate_reviews',
                'feature_reviews', 'view_review_reports', 'handle_review_reports',
                'manage_review_reports', 'view_analytics', 'view_review_trends',
                'export_review_data', 'manage_review_responses', 'approve_review_responses',
                'delete_review_responses',

                // Shipping Management - Full Access
                'view_shipping_methods', 'create_shipping_methods', 'edit_shipping_methods', 'delete_shipping_methods',
                'view_shipping_zones', 'create_shipping_zones', 'edit_shipping_zones', 'delete_shipping_zones',
                'view_shipping_rates', 'create_shipping_rates', 'edit_shipping_rates', 'delete_shipping_rates',
                'view_shipments', 'create_shipments', 'edit_shipments', 'delete_shipments', 'manage_shipments',
                'view_shipping_addresses', 'create_shipping_addresses', 'edit_shipping_addresses', 'delete_shipping_addresses',
                'calculate_shipping', 'validate_addresses', 'purchase_labels', 'track_shipments',

                // Dropshipping - Full Access
                'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers', 'approve_suppliers',
                'manage_supplier_integrations', 'test_supplier_connections',
                'view_supplier_products', 'sync_supplier_products', 'map_supplier_products', 'bulk_update_supplier_products', 'manage_product_mappings',
                'view_dropship_orders', 'create_dropship_orders', 'edit_dropship_orders', 'cancel_dropship_orders', 'retry_dropship_orders', 'bulk_manage_dropship_orders',
                'view_supplier_integrations', 'create_supplier_integrations', 'edit_supplier_integrations', 'delete_supplier_integrations', 'test_integrations', 'view_integration_logs',
                'view_dropshipping_analytics', 'view_supplier_performance', 'view_profit_margins', 'export_dropshipping_data',
                'manage_automated_fulfillment', 'configure_sync_settings', 'manage_markup_rules', 'override_supplier_prices',
                'view_dropshipping_settings', 'edit_dropshipping_settings', 'manage_default_markups', 'configure_automation_rules',
            ],

            'Vendor Manager' => [
                // User Management - Limited
                'view_all_users',

                // Vendor Management - Full Access
                'view_all_vendors', 'create_vendors_for_users', 'edit_all_vendors', 'delete_all_vendors',

                // Product Management
                'view_products', 'edit_products',

                // Order Management - View All Orders for Management
                'view_all_orders', 'edit_all_orders',

                // Customer Service
                'view_customer_data',

                // Inventory Management - Limited
                'view_inventory', 'edit_inventory', 'update_stock_thresholds', 'view_inventory_reports',

                // Review Management - Management Level
                'view_reviews', 'manage_reviews', 'moderate_reviews', 'view_review_reports',
                'handle_review_reports', 'view_analytics', 'view_review_trends',
                'manage_review_responses', 'approve_review_responses',

                // Shipping Management - Management Level
                'view_shipping_methods', 'view_shipping_zones', 'view_shipping_rates',
                'view_shipments', 'edit_shipments', 'manage_shipments',
                'calculate_shipping', 'track_shipments',

                // Dropshipping - Management Level
                'view_suppliers', 'edit_suppliers',
                'view_supplier_products', 'sync_supplier_products', 'map_supplier_products', 'manage_product_mappings',
                'view_dropship_orders', 'create_dropship_orders', 'edit_dropship_orders', 'cancel_dropship_orders',
                'view_supplier_integrations', 'test_integrations',
                'view_dropshipping_analytics', 'view_supplier_performance', 'view_profit_margins',
                'manage_automated_fulfillment', 'configure_sync_settings', 'manage_markup_rules',
                'view_dropshipping_settings',

                // Digital Products - Management Level
                'view_digital_products', 'create_digital_products', 'edit_digital_products',
                'view_product_files', 'create_product_files', 'edit_product_files',
                'view_download_access', 'manage_download_access',
                'view_license_keys', 'manage_license_keys',
                'view_download_analytics', 'view_digital_product_reports',
            ],

            'Vendor' => [
                // User Management - Own Profile Only
                'view_own_profile', 'edit_own_profile',

                // Vendor Management - Own Vendor Only
                'view_own_vendor', 'create_own_vendor', 'edit_own_vendor', 'delete_own_vendor',
                'view_vendors_public', // Can view other vendors publicly

                // Product Management (Own Products)
                'view_products', 'create_products', 'edit_products', 'delete_products',

                // Order Management - View Orders Related to Their Products
                'view_all_orders', // Note: You might want to create 'view_vendor_orders' for vendor-specific orders

                // Product Attributes/Categories (if they can manage these)
                'view_product_attributes', 'view_product_categories', 'view_product_tags', 'view_product_statuses',

                // Inventory Management - Own Products Only
                'view_inventory', 'edit_inventory', 'update_stock_thresholds',

                // Review Management - Vendor Level
                'view_reviews', 'create_reviews', 'edit_own_reviews', 'delete_own_reviews',
                'vote_review_helpfulness', 'report_reviews', 'respond_to_reviews',
                'edit_own_responses', 'delete_own_responses', 'view_product_reviews',

                // Shipping Management - Vendor Level
                'view_shipping_methods', 'view_shipping_zones', 'view_shipments',
                'calculate_shipping', 'track_shipments',

                // Dropshipping - Vendor Level
                'view_suppliers',
                'view_supplier_products',
                'view_dropship_orders',
                'view_supplier_performance',
                'view_profit_margins',

                // Digital Products - Vendor Level
                'view_digital_products', 'create_digital_products', 'edit_digital_products',
                'manage_digital_products', 'upload_digital_files',
                'view_product_files', 'create_product_files', 'edit_product_files', 'manage_product_files',
                'view_download_access', 'create_download_access',
                'view_license_keys', 'create_license_keys',
                'view_download_analytics',
            ],

            'Customer Service' => [
                // User Management - View Only
                'view_all_users',

                // Vendor Management - View Only
                'view_all_vendors',

                // Customer Support
                'view_customer_data',
                'manage_refunds',
                'manage_returns',

                // Order Management - View and Edit for Support
                'view_all_orders', 'edit_all_orders',

                // Product Viewing (for support purposes)
                'view_products', 'view_product_categories',

                // Inventory Management - View Only
                'view_inventory', 'view_inventory_reports',

                // Review Management - Customer Service Level
                'view_reviews', 'moderate_reviews', 'view_review_reports',
                'handle_review_reports', 'manage_review_reports', 'view_analytics',

                // Shipping Management - Customer Service Level
                'view_shipping_methods', 'view_shipping_zones', 'view_shipments',
                'edit_shipments', 'track_shipments', 'validate_addresses',

                // Dropshipping - Customer Service Level
                'view_suppliers',
                'view_supplier_products',
                'view_dropship_orders', 'edit_dropship_orders',
                'view_dropshipping_analytics',
            ],

            'Content Manager' => [
                // User Management - View Only
                'view_all_users',

                // Vendor Management - View Only
                'view_all_vendors',

                // Product Content Management
                'view_products', 'create_products', 'edit_products',
                'view_product_attributes', 'create_product_attributes', 'edit_product_attributes',
                'view_product_categories', 'create_product_categories', 'edit_product_categories',
                'view_product_tags', 'create_product_tags', 'edit_product_tags',
                'view_product_statuses', 'create_product_statuses', 'edit_product_statuses',

                // Category Management
                'view_categories', 'create_categories', 'edit_categories',

                // Inventory Management - View Only
                'view_inventory', 'view_inventory_reports',

                // Review Management - Content Management Level
                'view_reviews', 'moderate_reviews', 'feature_reviews',
                'view_review_reports', 'handle_review_reports', 'view_analytics',

                // Shipping Management - Content Management Level
                'view_shipping_methods', 'view_shipping_zones', 'calculate_shipping',

                // Dropshipping - Content Management Level
                'view_suppliers',
                'view_supplier_products', 'sync_supplier_products', 'map_supplier_products',
                'view_dropship_orders',
                'view_dropshipping_analytics',

                // Digital Products - Content Management Level
                'view_digital_products', 'create_digital_products', 'edit_digital_products',
                'view_product_files', 'create_product_files', 'edit_product_files',
                'view_download_access',
                'view_license_keys',
            ],

            'User' => [
                // User Management - Own Profile Only
                'view_own_profile', 'edit_own_profile', 'delete_own_account',
                'create_user_account', // For self-registration

                // Vendor Management - Public View and Own Vendor
                'view_vendors_public', 'view_own_vendor', 'create_own_vendor', 'edit_own_vendor', 'delete_own_vendor',

                // Basic Customer Permissions
                'view_products',
                'view_product_categories',

                // Order Management - Own Orders Only
                'view_own_orders', 'create_own_orders', 'edit_own_orders', 'delete_own_orders',

                // Review Management - Standard User Level
                'view_reviews', 'create_reviews', 'edit_own_reviews', 'delete_own_reviews',
                'vote_review_helpfulness', 'report_reviews',

                // Shipping Management - User Level
                'view_shipping_addresses', 'create_shipping_addresses', 'edit_shipping_addresses', 'delete_shipping_addresses',
                'calculate_shipping', 'track_shipments',

                // Digital Products - User Level
                'download_digital_files',
                'view_download_access',
                'view_license_keys',
            ],

            'Guest' => [
                // User Registration
                'create_user_account',

                // Browse Only
                'view_products',
                'view_product_categories',
                'view_vendors_public',

                // Review Management - Guest Level
                'view_reviews',
            ],
        ];

        $rolePermissionsToInsert = [];
        $roles = Role::all()->keyBy('name');
        $permissions = Permission::all();

        foreach ($rolePermissions as $roleName => $rolePerms) {
            $roleId = $roles[$roleName]->id;

            if ($rolePerms === '*') {
                foreach ($permissions as $permission) {
                    $rolePermissionsToInsert[] = [
                        'role_id'       => $roleId,
                        'permission_id' => $permission->id,
                    ];
                }

                continue;
            }

            foreach ($rolePerms as $permName) {
                $permission = $permissions->firstWhere('name', $permName);
                if ($permission) {
                    $rolePermissionsToInsert[] = [
                        'role_id'       => $roleId,
                        'permission_id' => $permission->id,
                    ];
                }
            }
        }

        DB::table('role_permission')->insert($rolePermissionsToInsert);
    }
}
