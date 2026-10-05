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
        // Add adviser_id as nullable foreign key
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('adviser_id')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
        });

        // Attempt to map existing adviser string values to User IDs
        // This is a best-effort migration to preserve existing data
        if (Schema::hasColumn('sections', 'adviser')) {
            $sections = DB::table('sections')->whereNotNull('adviser')->get();
            
            foreach ($sections as $section) {
                // Try to find a user with the Teacher role whose name matches the adviser string
                $user = DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', 'Teacher')
                    ->where('model_has_roles.model_type', 'App\Models\User')
                    ->where('users.name', 'like', '%' . $section->adviser . '%')
                    ->first();
                
                if ($user) {
                    DB::table('sections')
                        ->where('id', $section->id)
                        ->update(['adviser_id' => $user->id]);
                }
            }

            // Drop the old adviser string column
            Schema::table('sections', function (Blueprint $table) {
                $table->dropColumn('adviser');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the adviser string column
        Schema::table('sections', function (Blueprint $table) {
            $table->string('adviser')->nullable()->after('is_active');
        });

        // Attempt to restore adviser strings from User names
        $sections = DB::table('sections')->whereNotNull('adviser_id')->get();
        
        foreach ($sections as $section) {
            $user = DB::table('users')->where('id', $section->adviser_id)->first();
            if ($user) {
                DB::table('sections')
                    ->where('id', $section->id)
                    ->update(['adviser' => $user->name]);
            }
        }

        // Drop the adviser_id foreign key
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['adviser_id']);
            $table->dropColumn('adviser_id');
        });
    }
};
