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
        $driver = DB::connection()->getDriverName();
        
        // Update customer_id to nullable
        Schema::table('sales', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['customer_id']);
        });
        
        // Make customer_id nullable
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('customer_id')->nullable()->change();
        });
        
        // Re-add the foreign key constraint with nullable support
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // Update payment_method enum to include 'qris'
        if ($driver === 'pgsql') {
            // Get the actual column type
            $result = DB::selectOne("
                SELECT 
                    c.data_type,
                    c.udt_name,
                    c.column_default
                FROM information_schema.columns c
                WHERE c.table_name = 'sales' 
                AND c.column_name = 'payment_method'
            ");
            
            if ($result) {
                // Check if it's using a custom type (enum) or standard type
                $isEnum = DB::selectOne("
                    SELECT EXISTS (
                        SELECT 1 
                        FROM pg_type t 
                        JOIN pg_attribute a ON a.atttypid = t.oid 
                        JOIN pg_class cl ON a.attrelid = cl.oid 
                        WHERE cl.relname = 'sales' 
                        AND a.attname = 'payment_method'
                        AND t.typtype = 'e'
                    ) as is_enum
                ");
                
                if ($isEnum && $isEnum->is_enum) {
                    // It's an enum type - get the type name
                    $enumResult = DB::selectOne("
                        SELECT t.typname 
                        FROM pg_type t 
                        JOIN pg_attribute a ON a.atttypid = t.oid 
                        JOIN pg_class c ON a.attrelid = c.oid 
                        WHERE c.relname = 'sales' 
                        AND a.attname = 'payment_method'
                        AND t.typtype = 'e'
                        LIMIT 1
                    ");
                    
                    if ($enumResult && $enumResult->typname) {
                        $oldEnumType = $enumResult->typname;
                        $newEnumType = $oldEnumType . '_new';
                        
                        // Drop default first
                        DB::statement("ALTER TABLE sales ALTER COLUMN payment_method DROP DEFAULT IF EXISTS");
                        
                        // Create new enum type with 'qris' included
                        DB::statement("CREATE TYPE {$newEnumType} AS ENUM ('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'qris')");
                        
                        // Convert column to use new enum type
                        DB::statement("
                            ALTER TABLE sales 
                            ALTER COLUMN payment_method TYPE {$newEnumType} 
                            USING payment_method::text::{$newEnumType}
                        ");
                        
                        // Drop old enum type
                        DB::statement("DROP TYPE IF EXISTS {$oldEnumType} CASCADE");
                        
                        // Rename new enum type to original name
                        DB::statement("ALTER TYPE {$newEnumType} RENAME TO {$oldEnumType}");
                        
                        // Restore default
                        DB::statement("ALTER TABLE sales ALTER COLUMN payment_method SET DEFAULT 'cash'");
                    }
                } else {
                    // It's varchar/text - use CHECK constraint approach
                    // First ensure all existing values are valid
                    DB::statement("
                        UPDATE sales 
                        SET payment_method = 'cash' 
                        WHERE payment_method NOT IN ('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet')
                    ");
                    
                    // Drop existing constraint if any
                    DB::statement("
                        ALTER TABLE sales 
                        DROP CONSTRAINT IF EXISTS sales_payment_method_check
                    ");
                    
                    // Add CHECK constraint
                    DB::statement("
                        ALTER TABLE sales 
                        ADD CONSTRAINT sales_payment_method_check 
                        CHECK (payment_method IN ('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'qris'))
                    ");
                }
            }
        } else {
            // MySQL/MariaDB syntax
            DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'qris') DEFAULT 'cash'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        // Revert payment_method enum (remove 'qris')
        if ($driver === 'pgsql') {
            $isEnum = DB::selectOne("
                SELECT EXISTS (
                    SELECT 1 
                    FROM pg_type t 
                    JOIN pg_attribute a ON a.atttypid = t.oid 
                    JOIN pg_class cl ON a.attrelid = cl.oid 
                    WHERE cl.relname = 'sales' 
                    AND a.attname = 'payment_method'
                    AND t.typtype = 'e'
                ) as is_enum
            ");
            
            if ($isEnum && $isEnum->is_enum) {
                // It's an enum type
                $enumResult = DB::selectOne("
                    SELECT t.typname 
                    FROM pg_type t 
                    JOIN pg_attribute a ON a.atttypid = t.oid 
                    JOIN pg_class c ON a.attrelid = c.oid 
                    WHERE c.relname = 'sales' 
                    AND a.attname = 'payment_method'
                    AND t.typtype = 'e'
                    LIMIT 1
                ");
                
                if ($enumResult && $enumResult->typname) {
                    $oldEnumType = $enumResult->typname;
                    $newEnumType = $oldEnumType . '_old';
                    
                    // Update any 'qris' values to 'cash' first
                    DB::statement("UPDATE sales SET payment_method = 'cash'::text WHERE payment_method::text = 'qris'");
                    
                    // Drop default
                    DB::statement("ALTER TABLE sales ALTER COLUMN payment_method DROP DEFAULT IF EXISTS");
                    
                    // Create new enum type without 'qris'
                    DB::statement("CREATE TYPE {$newEnumType} AS ENUM ('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet')");
                    
                    // Convert column to use new enum type
                    DB::statement("
                        ALTER TABLE sales 
                        ALTER COLUMN payment_method TYPE {$newEnumType} 
                        USING payment_method::text::{$newEnumType}
                    ");
                    
                    // Drop old enum type
                    DB::statement("DROP TYPE IF EXISTS {$oldEnumType} CASCADE");
                    
                    // Rename new enum type to original name
                    DB::statement("ALTER TYPE {$newEnumType} RENAME TO {$oldEnumType}");
                    
                    // Restore default
                    DB::statement("ALTER TABLE sales ALTER COLUMN payment_method SET DEFAULT 'cash'");
                }
            } else {
                // It's varchar, just update the CHECK constraint
                // Update any 'qris' values to 'cash' first
                DB::statement("UPDATE sales SET payment_method = 'cash' WHERE payment_method = 'qris'");
                
                // Drop existing constraint
                DB::statement("
                    ALTER TABLE sales 
                    DROP CONSTRAINT IF EXISTS sales_payment_method_check
                ");
                
                // Add CHECK constraint without 'qris'
                DB::statement("
                    ALTER TABLE sales 
                    ADD CONSTRAINT sales_payment_method_check 
                    CHECK (payment_method IN ('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet'))
                ");
            }
        } else {
            // MySQL/MariaDB syntax
            DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet') DEFAULT 'cash'");
        }
        
        // Revert customer_id to not nullable
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('customer_id')->nullable(false)->change();
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
