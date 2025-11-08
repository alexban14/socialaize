<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('profile_type')->default('personal');
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'profile_type']);
        });

        // Move existing data
        $users = User::whereNotNull('bio')->orWhereNotNull('location')->orWhereNotNull('website')->get();
        foreach ($users as $user) {
            DB::table('user_profiles')->insert([
                'user_id' => $user->id,
                'profile_type' => 'personal',
                'bio' => $user->bio,
                'location' => $user->location,
                'website' => $user->website,
                'is_active' => true, // Make the personal profile active by default
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'location', 'website']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
        });

        // Move data back
        $profiles = DB::table('user_profiles')->where('profile_type', 'personal')->get();
        foreach ($profiles as $profile) {
            User::where('id', $profile->user_id)->update([
                'bio' => $profile->bio,
                'location' => $profile->location,
                'website' => $profile->website,
            ]);
        }

        Schema::dropIfExists('user_profiles');
    }
};