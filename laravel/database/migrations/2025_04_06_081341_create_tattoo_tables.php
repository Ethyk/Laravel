<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// class CreateTables extends Migration
return new class extends Migration

{
    public function up()
    {

        Schema::create('salons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->string('name', 255)->nullable(false);
            $table->text('description')->nullable();
            $table->string('adresse', 255)->nullable(false);
            $table->string('ville', 100)->nullable(false);
            $table->string('code_postal', 20)->nullable(false);
            $table->string('pays', 50)->nullable(false);
            $table->uuid('gestionnaire_id')->nullable();
            $table->foreign('gestionnaire_id')->references('id')->on('users')->onDelete('set null');
            // $table->uuid('gestionnaire_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->softDeletes(); // Adds a nullable 'deleted_at' column
        });

        // Table tatoueurs
        Schema::create('tatoueurs', function (Blueprint $table) {
            // $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            // $table->foreign('id')->primary()->references('id')->on('users')->onDelete('cascade');
             // 1. Clé primaire indépendante
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            
            // 2. Clé étrangère séparée vers users
            $table->uuid('user_id')->unique()->nullable(false);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Ou 'restrict' selon vos besoins
    
            $table->text('bio')->nullable();
            $table->jsonb('style')->nullable();
            // $table->uuid('localisation_actuelle')->nullable()->references('id')->on('salons')->onDelete('set null');
            $table->uuid('localisation_actuelle')->nullable();
            $table->foreign('localisation_actuelle')->references('id')->on('salons')->onDelete('set null');
            $table->jsonb('disponibilites')->nullable();
            $table->string('instagram', 255)->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table salons

        // Table tatoueurs_salons
        Schema::create('tatoueurs_salons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            // $table->uuid('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->uuid('tatoueur_id')->nullable(false);
            $table->foreign('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->uuid('salon_id')->nullable(false);
            $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade');
            $table->date('date_debut')->nullable(false);
            $table->date('date_fin')->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table flashs
        Schema::create('flashs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('tatoueur_id')->nullable();
            $table->foreign('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->string('image_url', 255)->nullable(false);
            $table->decimal('prix', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table demandes
        Schema::create('demandes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('tatoueur_id')->nullable();
            $table->foreign('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->text('description')->nullable(false);
            $table->jsonb('reference_images')->nullable();
            $table->jsonb('style')->nullable();
            $table->string('etat', 50)->nullable(false)->checkIn(['en attente', 'acceptée', 'refusée', 'programmée', 'terminée']);
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table rdv
        Schema::create('rdv', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('demande_id')->nullable();
            $table->foreign('demande_id')->references('id')->on('demandes')->onDelete('cascade');
            $table->timestamp('date_rdv')->nullable(false);
            $table->integer('duree')->check('duree > 0');
            $table->string('statut', 50)->nullable(false)->default('confirmé')->checkIn(['confirmé', 'annulé', 'terminé']);
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table portfolios
        Schema::create('portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('tatoueur_id')->nullable();
            $table->foreign('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->string('image_url', 255)->nullable(false);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('rdv');
        Schema::dropIfExists('demandes');
        Schema::dropIfExists('flashs');
        Schema::dropIfExists('tatoueurs_salons');
        Schema::dropIfExists('salons');
        Schema::dropIfExists('tatoueurs');
        // Schema::table('salons', function (Blueprint $table) {
        //     $table->dropSoftDeletes(); // Supprime la colonne 'deleted_at'
        // });
        // Schema::dropIfExists('users');
    }
};
