<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileToUsersTable extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->unique()->after('email')->nullable();
        });
    }
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_mobile_unique');
            $table->dropColumn('mobile');
        });
    }
}
