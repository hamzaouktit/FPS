
<?php
// 2024_01_01_000011_create_affectations_table.php
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
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->string('groupe');
            $table->string('code_module');
            $table->string('mle_formateur')->nullable();
            $table->string('mode')->nullable();
            $table->decimal('mh_affectee')->default(0);
            $table->timestamp('date_affectation')->nullable();
            $table->foreign('groupe')->references('groupe')->on('groupes')->onDelete('cascade');
            $table->foreign('code_module')->references('code_module')->on('modules')->onDelete('cascade');
            $table->foreign('mle_formateur')->references('mle')->on('formateurs')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};