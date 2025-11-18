<?php

use App\Models\Auth\Profile;
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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('number')->nullable();
        });

        Profile::chunk(10, function ($profiles) {
            foreach ($profiles as $profile) {
                $profile->update(['number' => getUniqueProfileNumber()]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
};
