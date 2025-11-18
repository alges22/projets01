<?php

use App\Enums\GenderEnum;
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
        Schema::create('identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->nullable()->unique();
            $table->string('telephone')->unique();
            $table->string('telephone_fix')->unique()->nullable();
            $table->string('telephone_professional')->nullable()->unique();
            $table->string('photo')->nullable();
            $table->string('profession_id')->nullable();
            $table->string('address')->nullable();
            $table->string('civility')->nullable();
            $table->string('npi')->nullable()->unique();
            $table->string('ifu')->nullable()->unique();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', GenderEnum::toArray())->nullable();
            $table->integer('country_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identities');
    }
};
