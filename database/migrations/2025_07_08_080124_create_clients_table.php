<?php

use App\Models\Clients\Client;
use App\Models\User;
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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('notes')->nullable();
            $table->foreignIdFor(User::class, 'created_by_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('client_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained();
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('client_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_users');
        Schema::dropIfExists('client_infos');
        Schema::dropIfExists('clients');
    }
};
