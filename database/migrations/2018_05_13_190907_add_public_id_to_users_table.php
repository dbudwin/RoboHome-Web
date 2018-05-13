<?php

use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Webpatser\Uuid\Uuid;

class AddPublicIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('public_id')->after('remember_token')->default('');
        });

        foreach (User::all() as $user) {
            $user->public_id = (string)Uuid::generate(4);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
}
