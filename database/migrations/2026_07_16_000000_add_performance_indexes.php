<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance Indexes Migration
 *
 * Adds missing query-performance indexes identified during
 * Phase 13 Database Audit. No schema or data changes.
 * Fully reversible. Safe to run on live production PostgreSQL
 * without downtime (PostgreSQL builds indexes concurrently
 * by default for regular CREATE INDEX operations via Eloquent).
 *
 * WHY EACH INDEX:
 *
 * orders.user_id
 *   - CustomerOrderController::index() filters orders by user_id on every request
 *   - CustomerOrderController::show() also scopes by user_id
 *   - Without index: full table scan on orders for every customer page load
 *
 * orders.status
 *   - AdminDashboardController counts pending orders: WHERE status = 'pending'
 *   - AdminOrderController::index() filters by status
 *   - Without index: full table scan on every dashboard load
 *
 * orders.created_at
 *   - AdminDashboardController uses whereDate('created_at', today)
 *   - OrderService generates order numbers using whereDate('created_at')
 *   - Without index: full table scan on every order creation + dashboard load
 *
 * stock_movements.product_id
 *   - OrderService::restoreStock() queries by product_id
 *   - Without index: full scan on stock_movements per cancelled order item
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Index for customer order list & IDOR scoping queries
            $table->index('user_id', 'idx_orders_user_id');

            // Index for admin dashboard status filters & counts
            $table->index('status', 'idx_orders_status');

            // Index for date-based dashboard queries and order number generation
            $table->index('created_at', 'idx_orders_created_at');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            // Index for stock restore queries on order cancellation
            $table->index('product_id', 'idx_stock_movements_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_id');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('idx_stock_movements_product_id');
        });
    }
};
