<?php

use App\Models\SimvebFile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simveb_files', function (Blueprint $table)
        {
            $table->uuid("id")->primary();
            $table->json("path");
            $table->uuidMorphs("model");
            $table->string('type',)->default(SimvebFile::IMAGE);
            $table->uuid('file_type_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simveb_files');
    }
};
