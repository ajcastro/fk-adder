### FkAdder for Laravel Migration

Lets you add foreign keys in a swift! Foreign key adder for laravel migration.

#### Installation

`composer require ajcastro/fk-adder=dev-master`

#### Configuration

Create a config file named `app/config/fk_adder.php`

```
<?php

return [
    'fk_namespace' => 'Your\Fk\Namespace',
    'fk_datatypes_path' => app_path('database/foreign_keys/fk_datatypes.php')
];
```

#### Setup

Setup your foreign keys.

Create classes of foreign keys declaration inside your `fk_namespace`.

```
<?php

namespace Your\Fk\Namespace;

use FkAdder\BaseFk;

class UserId extends BaseFk
{
    protected $referenceTable = 'users';

    public function createFkColumn($column)
    {
        return $this->table->unsignedInteger($column);
    } 
}

```

If your foreign key declaration is so simple as it just needs the datatype declaration you can use your `fk_datatypes_path`.

```
<?php

/*
 * Fk datatypes. Registry of datatypes per fk, if ever createFkColumn is as simple as a datatype declaration.
 * For simple fk datatype column creation.
 */
return [
    'user_id' => 'unsignedInteger'
];
```

#### Usage

__Sample__:
```
Schema::create('users', function(Blueprint $table) {
    $table->increments('id');
    Fk::for($table)->add('user_group_id')->nullable()->comment('User group of the user');
});
```

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
        // Execute creation of foreign keys by all migrations which use Fk. \m/ :).
        foreach (Fk::$foreignKeys as $foreignKey) {
            Schema::table($foreignKey['table'], function (Blueprint $table) use ($foreignKey) {
                $table->foreign($foreignKey['column'], $foreignKey['key_name'])
                ->references($foreignKey['primary_key'])
                ->on($foreignKey['reference_table'])
                ->onDelete($foreignKey['on_delete'])
                ->onUpdate($foreignKey['on_update']);
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

#### License

Released under MIT License.
