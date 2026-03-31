<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->after('id');
            $table->boolean('is_active')->default(true)->after('microsoft_tenant_id');
        });

        Schema::table('license_subscriptions', function (Blueprint $table) {
            $table->timestamp('cancellation_allowed_until_date')->nullable()->after('status');
            $table->timestamp('creation_date')->nullable()->after('cancellation_allowed_until_date');
            $table->integer('refundable_quantity')->nullable()->after('creation_date');
            $table->string('etag')->nullable()->after('refundable_quantity');
            $table->unique(['customer_id', 'partner_subscription_id'], 'license_customer_subscription_unique');
        });

        Schema::table('product_requests', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('customer_id');
            $table->integer('quantity')->nullable()->after('product_name');
            $table->text('notes')->nullable()->after('product_description');
            $table->timestamp('notified_at')->nullable()->after('admin_notes');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('product_requests', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'quantity', 'notes', 'notified_at']);
        });

        Schema::table('license_subscriptions', function (Blueprint $table) {
            $table->dropUnique('license_customer_subscription_unique');
            $table->dropColumn([
                'cancellation_allowed_until_date',
                'creation_date',
                'refundable_quantity',
                'etag',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->dropColumn(['external_id', 'is_active']);
        });
    }
};
