<?php

use App\Enums\Status;
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
        Schema::create('treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid("demand_id")->constrained();
            $table->longText('rejected_reason',)->nullable();
            $table->longText('suspended_reason',)->nullable();
            $table->longText('closed_reason',)->nullable();
            $table->longText('verification_comment',)->nullable();
            $table->timestamp('assigned_to_service_at')->nullable();
            $table->timestamp('assigned_to_staff_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignUuid("responsible_id")->nullable()->constrained('profiles');
            $table->foreignUuid('assigned_to_staff_by')->nullable()->constrained("profiles");
            $table->foreignUuid('assigned_to_service_by')->nullable()->constrained("profiles");
            $table->foreignUuid('validated_by')->nullable()->constrained("profiles");
            $table->foreignUuid('rejected_by')->nullable()->constrained("profiles");
            $table->foreignUuid('suspended_by')->nullable()->constrained("profiles");
            $table->foreignUuid('verified_by')->nullable()->constrained("profiles");
            $table->foreignUuid('closed_by')->nullable()->constrained("profiles");
            $table->foreignUuid('service_id')->nullable()->constrained();
            $table->foreignUuid('interpol_service_id')->nullable()->constrained('services');
            $table->foreignUuid('interpol_staff_id')->nullable()->constrained('profiles');
            $table->foreignUuid('pre_validated_by')->nullable()->constrained('profiles');
            $table->foreignUuid('affected_to_interpol_service_by')->nullable()->constrained('profiles');
            $table->foreignUuid('assigned_to_interpol_staff_by')->nullable()->constrained('profiles');
            $table->foreignUuid('interpol_pre_validated_by')->nullable()->constrained('profiles');
            $table->foreignUuid('interpol_validated_by')->nullable()->constrained('profiles');
            $table->timestamp('affected_to_interpol_at')->nullable();
            $table->timestamp('assigned_to_interpol_staff_at')->nullable();
            $table->timestamp('pre_validated_at')->nullable();
            $table->timestamp('interpol_validated_at')->nullable();
            $table->timestamp('interpol_pre_validated_at')->nullable();
            $table->timestamp('interpol_pre_rejected_at')->nullable();
            $table->timestamp('interpol_rejected_at')->nullable();
            $table->text('interpol_comment')->nullable();
            $table->timestamp('print_order_emitted_at')->nullable();
            $table->foreignUuid('print_order_emitted_by')->nullable()->constrained('profiles');
            $table->foreignUuid('printed_by')->nullable()->constrained('profiles');
            $table->timestamp('printed_at')->nullable();
            $table->text('print_observations')->nullable();
            $table->uuid('printer_id')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->timestamp('assigned_to_institution_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
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
        Schema::dropIfExists('treatments');
    }
};
