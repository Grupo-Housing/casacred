<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateCardinalZoneEnumInListingsTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE listings MODIFY COLUMN cardinal_zone 
            ENUM('norte', 'sur', 'este', 'oeste', 'centro', 
                 'noreste', 'noroeste', 'sureste', 'suroeste') 
            NULL DEFAULT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE listings MODIFY COLUMN cardinal_zone 
            ENUM('norte', 'sur', 'este', 'oeste', 'centro') 
            NULL DEFAULT NULL");
    }
}
