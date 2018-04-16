## FkAdder for Laravel Migration

Lets you add foreign keys in a swift! Foreign key adder for laravel migration.
For Laravel `4.2` and `5.*`.

The purpose of `FkAdder` is to simplify declaration of foreign key columns in migration.

##### Things that `FkAdder` do for you:
  * __Remembers the data type of a foreign key__, and it will provide the correct data type for you, so that you don't have to recall the data type of foreign key column
      whenever you need that certain foreign key.
  * __Lets you create migration tables in any order__. This solves the problem when your table is created prior than the reference table.


### Installation

`composer require ajcastro/fk-adder`

### Alias

Add alias into `config/app.php` file.

```php
 'Fk' => FkAdder\Fk::class
```

### Configuration

Create a config file named `config/fk_adder.php`

```php
<?php

return [
    'fk_namespace' => 'Your\Fk\Namespace', // for class-based declaration
    'fk_datatypes_path' => app_path('database/foreign_keys/fk_datatypes.php') // for string-based declaration
];
```

### Setup

There are two ways to setup your foreign keys: __string-based declaration__ and __class-based declaration__. 
String-based is preferred for simpler datatype declaration.

##### String-based declaration

Open your `fk_datatypes_path` file and add the foreign key declaration in the array.

```php
<?php

/*
 * Fk datatypes. Simple  datatype declaration.
 */
return [
    'user_id'       => 'unsignedInteger',
    'group_id'      => 'unsignedInteger',
    'preference_id' => 'unsignedBigInteger',
];
```


##### Class-based declaration

Create classes of foreign keys declaration inside your `fk_namespace` directory path.

__Naming Convention__

If the foreign key is e.g. `user_id`, then the class name should be `UserId`.

Example:

```php
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


### Sample Usage, Before vs After

__Before__

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->nullable()->comment('Group of the user');
            $table->unsignedBigInteger('preference_id')->nullable()->comment('Preference of the user');

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('preference_id')->references('id')->on('preferences');
        });
    }
}

```

__After__

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            // Foreign key declaration is simpler, more compact and cleaner. 
            // You dont have to type what datatype it is. You will just declare it once.
            Fk::make($table)->add('group_id')->nullable()->comment('Group of the user');
            Fk::make($table)->onDelete('cascade')->add('preference_id')
                ->nullable()->comment('Preference of the user');
        });

        // Migrate and persist foreign keys in mysql database.
        // You can call this once at the very end of migration, 
        // so all your foreign key declarations accross different migration files will be persisted.
        Fk::migrate();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Fk::rollback(); // delete foreign keys persisted by Fk::migrate(), (coming soon...)
        Schema::dropIfExists('users');
    }
}

```

As you can see, fk-adder make it simpler for you to add foreign keys in migration.

#### License

Released under MIT License.
