<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRadacctTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radacct', function (Blueprint $table) {
            $table->bigIncrements('radacctid');
            $table->string('acctsessionid', 64);
            $table->string('acctuniqueid', 32);
            $table->string('username', 64);
            $table->string('groupname', 64)
                ->nullable();
            $table->string('realm', 64)
                ->nullable();
            $table->string('nasipaddress', 15);
            $table->string('nasportid', 15)
                ->nullable();
            $table->string('nasporttype', 32)
                ->nullable();
            $table->dateTime('acctstarttime')
                ->nullable();
            $table->dateTime('acctstoptime')
                ->nullable();
            $table->integer('acctsessiontime')
                ->nullable();
            $table->string('acctauthentic', 32)
                ->nullable();
            $table->string('connectinfo_start', 50)
                ->nullable();
            $table->string('connectinfo_stop', 50)
                ->nullable();
            $table->bigInteger('acctinputoctets')
                ->nullable();
            $table->bigInteger('acctoutputoctets')
                ->nullable();
            $table->string('calledstationid', 50)
                ->default("''");
            $table->string('callingstationid', 50)
                ->default("''");
            $table->string('acctterminatecause', 32)
                ->default("''");
            $table->string('servicetype', 32)
                ->nullable();
            $table->string('framedprotocol', 32)
                ->nullable();
            $table->string('framedipaddress', 32)
                ->default("''");
            $table->integer('acctstartdelay')
                ->nullable();
            $table->integer('acctstopdelay')
                ->nullable();
            $table->string('xascendsessionsvrkey', 10)
                ->nullable();

            $table->unique('acctuniqueid');
            $table->index('username');
            $table->index('framedipaddress');
            $table->index('acctsessionid');
            $table->index('acctsessiontime');
            $table->index('acctstarttime');
            $table->index('acctstoptime');
            $table->index('nasipaddress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('radacct');
    }
}
