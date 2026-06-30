    <?php

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
            Schema::create('projet_tache', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('projet_id');
                $table->unsignedBigInteger('tache_id');

                $table->enum('statut_tache', ['backlog', 'en_cours', 'en_revue', 'termine'])->default('backlog');
                $table->enum('priorite', ['basse', 'moyenne', 'haute', 'bloquante'])->default('moyenne');
                $table->timestamps();

                $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
                $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');

                // Sécurité pour éviter de lier deux fois la même tâche au même projet
                $table->unique(['projet_id', 'tache_id']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('projet_tache');
        }
    };
