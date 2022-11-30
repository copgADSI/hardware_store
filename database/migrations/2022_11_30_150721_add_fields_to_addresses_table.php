<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->after('phone');
            $table->unsignedBigInteger('departament_id')->after('city_id');
            $table->foreign('city_id')
                ->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('departament_id')
                ->references('id')->on('departaments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('citiy_id');
            $table->dropColumn('departament_id');
        });
    }
};
