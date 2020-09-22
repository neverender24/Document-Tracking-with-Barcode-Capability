<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileTagToRoutes extends Migration
{

    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('file_tag')->nullable();
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('file_tag');
        });
    }
}
