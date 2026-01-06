<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get first user ID for existing data assignment
        $firstUserId = DB::table('users')->orderBy('created_at')->value('id');

        // Sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            DB::table('sales')->whereNull('user_id')->update(['user_id' => $firstUserId]);
        }
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Sales Details table
        Schema::table('sales_details', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related sale
            DB::statement('
                UPDATE sales_details 
                SET user_id = (
                    SELECT user_id FROM sales 
                    WHERE sales.id = sales_details.sale_id
                )
            ');
        }
        Schema::table('sales_details', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Sales Returns table
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related sale
            DB::statement('
                UPDATE sales_returns 
                SET user_id = (
                    SELECT user_id FROM sales 
                    WHERE sales.id = sales_returns.sale_id
                )
            ');
        }
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Sales Return Items table
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related sales_return
            DB::statement('
                UPDATE sales_return_items 
                SET user_id = (
                    SELECT user_id FROM sales_returns 
                    WHERE sales_returns.id = sales_return_items.sales_return_id
                )
            ');
        }
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Use created_by if available, otherwise use first user
            DB::statement('
                UPDATE expenses 
                SET user_id = COALESCE(created_by, ?)
            ', [$firstUserId]);
        }
        Schema::table('expenses', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Financial Reports table
        Schema::table('financial_reports', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Use generated_by if available, otherwise use first user
            DB::statement('
                UPDATE financial_reports 
                SET user_id = COALESCE(generated_by, created_by, ?)
            ', [$firstUserId]);
        }
        Schema::table('financial_reports', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Use created_by if available, otherwise use first user
            DB::statement('
                UPDATE customers 
                SET user_id = COALESCE(created_by, ?)
            ', [$firstUserId]);
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Addresses table
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_addresses 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_addresses.customer_id
                )
            ');
        }
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Feedbacks table
        Schema::table('customer_feedbacks', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_feedbacks 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_feedbacks.customer_id
                )
            ');
        }
        Schema::table('customer_feedbacks', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Interactions table
        Schema::table('customer_interactions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_interactions 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_interactions.customer_id
                )
            ');
        }
        Schema::table('customer_interactions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Loyalty Points table
        Schema::table('customer_loyalty_points', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_loyalty_points 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_loyalty_points.customer_id
                )
            ');
        }
        Schema::table('customer_loyalty_points', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Loyalty Programs table
        Schema::table('customer_loyalty_programs', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Use created_by if available, otherwise use first user
            DB::statement('
                UPDATE customer_loyalty_programs 
                SET user_id = COALESCE(created_by, ?)
            ', [$firstUserId]);
        }
        Schema::table('customer_loyalty_programs', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Notes table
        Schema::table('customer_notes', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_notes 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_notes.customer_id
                )
            ');
        }
        Schema::table('customer_notes', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Segments table
        Schema::table('customer_segments', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Use created_by if available, otherwise use first user
            DB::statement('
                UPDATE customer_segments 
                SET user_id = COALESCE(created_by, ?)
            ', [$firstUserId]);
        }
        Schema::table('customer_segments', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        // Customer Wishlists table
        Schema::table('customer_wishlists', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
        });
        if ($firstUserId) {
            // Get user_id from related customer
            DB::statement('
                UPDATE customer_wishlists 
                SET user_id = (
                    SELECT user_id FROM customers 
                    WHERE customers.id = customer_wishlists.customer_id
                )
            ');
        }
        Schema::table('customer_wishlists', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys and columns in reverse order
        $tables = [
            'customer_wishlists',
            'customer_segments',
            'customer_notes',
            'customer_loyalty_programs',
            'customer_loyalty_points',
            'customer_interactions',
            'customer_feedbacks',
            'customer_addresses',
            'customers',
            'financial_reports',
            'expenses',
            'sales_return_items',
            'sales_returns',
            'sales_details',
            'sales',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'user_id')) {
                        $table->dropForeign(['user_id']);
                        $table->dropIndex(['user_id']);
                        $table->dropColumn('user_id');
                    }
                });
            }
        }
    }
};
