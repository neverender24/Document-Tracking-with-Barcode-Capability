<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPoApprovedTag extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->datetime('approved_po')->nullable();
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('approved_po');
        });
    }
}
