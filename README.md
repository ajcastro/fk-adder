### FkAdder for Laravel Migration

Lets you add foreign keys in a swift! Foreign key adder for laravel migration.
For Laravel `4.2` and `5.*`.

The purpose of `FkAdder` is to simplify declaration of foreign key columns in migration.

__Things that `FkAdder` do for you:__
  *   __Remembers the data type of a foreign key__, and it will provide the correct data type for you, so that you don't have to recall the data type of foreign key column
      whenever you need that certain foreign key.
  *   __Lets you create migration tables in any order__. This solves the problem when your table is created prior than the reference table.


#### Installation

`composer require ajcastro/fk-adder`

#### Alias

Add alias into `config/app.php` file.

```
 'Fk' => FkAdder\Fk::class
```

#### Configuration

Create a config file named `config/fk_adder.php`

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

__Naming Convention__

If the foreign key is e.g. `user_id`, then the class name should be `UserId`.

Example:

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
    'user_id' => 'unsignedInteger',
    'user_group_id' => 'unsignedInteger',
];
```

#### Usage

__Sample__:
```
Schema::create('users', function(Blueprint $table) {
    $table->increments('id');
    Fk::make($table)->add('user_group_id')->nullable()->comment('User group of the user');
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
