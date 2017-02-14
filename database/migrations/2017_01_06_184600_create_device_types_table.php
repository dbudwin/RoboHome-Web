<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTypesTable extends Migration
{
    private $tableName = 'device_types';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 4);
            $table->timestamps();
        });

        $this->seedDefaultValues();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    private function seedDefaultValues()
    {
        DB::table($this->tableName)->insert(
            array(
                'type' => 'RF'
            )
        );

        DB::table($this->tableName)->insert(
            array(
                'type' => 'IR'
            )
        );

        DB::table($this->tableName)->insert(
            array(
                'type' => 'WIFI'
            )
        );
    }
}
