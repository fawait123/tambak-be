<?php

use App\Models\Kolam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputKualitasAirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_kualitas_air', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Kolam::class);
            $table->integer('suhu_kolam');
            $table->date('tgl');
            $table->time('waktu');
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_kualitas_air');
    }
}
