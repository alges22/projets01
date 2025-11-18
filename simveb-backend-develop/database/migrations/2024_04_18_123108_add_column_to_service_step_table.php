
<?php

use App\Enums\ProcessTypeEnum;
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
        Schema::table('service_step', function (Blueprint $table) {
            $table->enum('process_type', ProcessTypeEnum::toArray())->default(ProcessTypeEnum::automatic->name);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_step', function (Blueprint $table) {
            //
        });
    }
};
