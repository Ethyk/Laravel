<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// class CreateTables extends Migration
return new class extends Migration

{
    public function up()
    {


        // Table tatoueurs
        Schema::create('tatoueurs', function (Blueprint $table) {
            $table->uuid('id')->primary()->references('id')->on('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->jsonb('style')->nullable();
            $table->uuid('localisation_actuelle')->nullable()->references('id')->on('salons')->onDelete('set null');
            $table->jsonb('disponibilites')->nullable();
            $table->string('instagram', 255)->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table salons
        Schema::create('salons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->string('name', 255)->nullable(false);
            $table->text('description')->nullable();
            $table->string('adresse', 255)->nullable(false);
            $table->string('ville', 100)->nullable(false);
            $table->string('code_postal', 20)->nullable(false);
            $table->string('pays', 50)->nullable(false);
            $table->uuid('gestionnaire_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table tatoueurs_salons
        Schema::create('tatoueurs_salons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->uuid('salon_id')->references('id')->on('salons')->onDelete('cascade');
            $table->date('date_debut')->nullable(false);
            $table->date('date_fin')->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
            $table->timestamp('updated_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table flashs
        Schema::create('flashs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
            $table->string('image_url', 255)->nullable(false);
            $table->decimal('prix', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table demandes
        Schema::create('demandes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
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
            $table->uuid('demande_id')->references('id')->on('demandes')->onDelete('cascade');
            $table->timestamp('date_rdv')->nullable(false);
            $table->integer('duree')->check('duree > 0');
            $table->string('statut', 50)->nullable(false)->default('confirmé')->checkIn(['confirmé', 'annulé', 'terminé']);
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('now()'));
        });

        // Table portfolios
        Schema::create('portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->uuid('tatoueur_id')->references('id')->on('tatoueurs')->onDelete('cascade');
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
        // Schema::dropIfExists('users');
    }
};
