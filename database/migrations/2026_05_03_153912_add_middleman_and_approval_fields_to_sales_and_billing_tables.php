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
        // 1. Update Sales Orders for 'Buying' step
        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'current_step')) {
                // Ensure default is 'Buying' for new orders if you want to start there
                // $table->string('current_step')->default('Buying')->change(); 
            }
        });

        // 2. Create Buying Details tables
        if (!Schema::hasTable('sales_buying_details')) {
            Schema::create('sales_buying_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->string('supplier_name')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->foreign('order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sales_buying_items')) {
            Schema::create('sales_buying_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('buying_id');
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->string('unit')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->timestamps();
                $table->foreign('buying_id')->references('id')->on('sales_buying_details')->onDelete('cascade');
            });
        }

        // 3. Update Sales Deliveries for Drum Billing
        Schema::table('sales_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_deliveries', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_qty')) {
                $table->decimal('drum_qty', 15, 2)->default(0)->after('required_units');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_unit')) {
                $table->string('drum_unit')->nullable()->after('drum_qty');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_buying_price')) {
                $table->decimal('drum_buying_price', 15, 2)->default(0)->after('drum_unit');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_buying_total')) {
                $table->decimal('drum_buying_total', 15, 2)->default(0)->after('drum_buying_price');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_selling_price')) {
                $table->decimal('drum_selling_price', 15, 2)->default(0)->after('drum_buying_total');
            }
            if (!Schema::hasColumn('sales_deliveries', 'drum_selling_total')) {
                $table->decimal('drum_selling_total', 15, 2)->default(0)->after('drum_selling_price');
            }
        });

        // 4. Update Payables & Receivables for Approval and Links
        Schema::table('payables', function (Blueprint $table) {
            if (!Schema::hasColumn('payables', 'approval_status')) {
                $table->string('approval_status')->default('Approved')->after('status');
            }
            if (!Schema::hasColumn('payables', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('payables', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('payables', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });

        Schema::table('receivables', function (Blueprint $table) {
            if (!Schema::hasColumn('receivables', 'sales_order_id')) {
                $table->unsignedBigInteger('sales_order_id')->nullable()->after('entity_id');
            }
            if (!Schema::hasColumn('receivables', 'ci_id')) {
                $table->unsignedBigInteger('ci_id')->nullable()->after('sales_order_id');
            }
            if (!Schema::hasColumn('receivables', 'approval_status')) {
                $table->string('approval_status')->default('Approved')->after('status');
            }
            if (!Schema::hasColumn('receivables', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('receivables', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('receivables', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_buying_items');
        Schema::dropIfExists('sales_buying_details');

        Schema::table('sales_deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'ci_id', 'drum_qty', 'drum_unit', 'drum_buying_price', 
                'drum_buying_total', 'drum_selling_price', 'drum_selling_total'
            ]);
        });

        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn([
                'sales_order_id', 'ci_id', 'approval_status', 
                'approved_by', 'approved_at', 'rejection_reason'
            ]);
        });
    }
};
