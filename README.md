### FkAdder\FkAdder


#### Usage

Create your migration and  
Name your migration filename like `3000_03_01_094045_add_foreign_keys_to_all_table.php` 
to make sure it will be the last migration to be executed.

__Migration Template__

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // IMPORTANT NOTE: Make sure this is the last migration being called.
        // Execute creation of foreign keys by all migrations which use FkAdder. \m/ :).
        foreach (FkAdder::$foreignKeys as $foreignKey) {
            Schema::table($foreignKey['table'], function (Blueprint $table) use ($foreignKey) {
                $table->foreign($foreignKey['column', $foreignKey['key_name')
                ->references($foreignKey['primary_key')
                ->on($foreignKey['reference_table')
                ->onDelete($foreignKey['on_delete')
                ->onUpdate($foreignKey['on_update');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

```