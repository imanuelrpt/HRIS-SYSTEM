<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 30)->unique()->nullable()->after('id');
            $table->enum('role', array_column(Role::cases(), 'value'))
                ->default(Role::Employee->value)
                ->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->foreignId('department_id')->nullable()->after('phone')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('position_id')->constrained('users')->nullOnDelete();
            $table->date('join_date')->nullable()->after('manager_id');
            $table->boolean('is_active')->default(true)->after('join_date');
            $table->string('photo_path')->nullable()->after('is_active');
            $table->boolean('must_change_password')->default(false)->after('photo_path');
            $table->softDeletes();

            $table->index(['role', 'is_active']);
            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('manager_id');
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nik',
                'role',
                'phone',
                'department_id',
                'position_id',
                'manager_id',
                'join_date',
                'is_active',
                'photo_path',
                'must_change_password',
            ]);
        });
    }
};
