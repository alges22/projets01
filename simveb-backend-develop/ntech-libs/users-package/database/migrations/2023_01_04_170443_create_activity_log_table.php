<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('log_name')->index()->nullable();
            $table->string('event')->index()->nullable();
            $table->text('description');
            $table->uuid('subject_id')->index()->nullable();
            $table->uuid('causer_id')->index()->nullable();
            $table->ipAddress()->nullable();
            $table->string("causer_type")->nullable();
            $table->string("subject_type")->nullable();
            $table->string('log_action')->nullable();
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->index()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
