<?php

namespace FkAdder;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Schema;

class Fk
{
    /**
     * Table blueprint.
     *
     * @var \Illuminate\Database\Schema\Blueprint
     */
    protected $table;

    /**
     * Array of foreign keys declaration.
     *
     * @var array
     */
    public static $foreignKeys = [];

    protected $column;
    protected $keyName;
    protected $onDelete = 'restrict';
    protected $onUpdate = 'cascade';

    /**
     * Constructor.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Instantiate.
     *
     * @param  \Illuminate\Database\Schema\Blueprint $table
     * @return static
     */
    public static function make($table)
    {
        return new static($table);
    }

    /**
     * Set the column for the fk.
     *
     * @param  string $column
     * @return $this
     */
    public function column($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Set the key name of the fk.
     *
     * @param  string $keyName
     * @return $this
     */
    public function keyName($keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * Set the on_delete of the fk.
     *
     * @param  string $onDelete
     * @return $this
     */
    public function onDelete($onDelete)
    {
        $this->onDelete = $onDelete;

        return $this;
    }

    /**
     * Set the on_update of the fk.
     *
     * @param  string $onUpdate
     * @return $this
     */
    public function onUpdate($onUpdate)
    {
        $this->onUpdate = $onUpdate;

        return $this;
    }

    /**
     * Add a foreign key to table and defer its foreign key creation.
     *
     * @param string $fk
     * @param string $column
     * @param string $keyName
     * @param string $onDelete
     * @param string $onUpdate
     * @return \Illuminate\Support\Fluent
     */
    public function add($fk, $column = null, $keyName = null, $onDelete = null, $onUpdate = null)
    {
        $baseFk = $this->baseFk($fk);

        $column = $column ?: $baseFk->defaultColumn();

        static::$foreignKeys[] = [
            'column'          => $column ?: $this->column,
            'key_name'        => $keyName ?: $this->keyName,
            'table'           => $this->table->getTable(),
            'reference_table' => $baseFk->referenceTable(),
            'primary_key'     => $baseFk->primaryKey,
            'on_delete'       => $onDelete ?: $this->onDelete ?: $baseFk->onDelete,
            'on_update'       => $onUpdate ?: $this->onUpdate ?: $baseFk->onUpdate,
        ];

        return $baseFk->createFkColumn($column);
    }

    /**
     * Migrate creation of foreign keys base from the fk calls.
     *
     * @return void
     */
    public static function migrate()
    {
        foreach (static::$foreignKeys as $foreignKey) {
            Schema::table($foreignKey['table'], function (Blueprint $table) use ($foreignKey) {
                $table->foreign($foreignKey['column'], $foreignKey['key_name'])
                ->references($foreignKey['primary_key'])
                ->on($foreignKey['reference_table'])
                ->onDelete($foreignKey['on_delete'])
                ->onUpdate($foreignKey['on_update']);
            });
        }

        static::createMigrationJson(
            static::getMigrationFilename(),
            static::$foreignKeys
        );

        static::$foreignKeys = [];
    }

    protected function getMigrationFilename()
    {
        return 'sample.json';
    }

    protected static function createMigrationJson($filename, $foreignKeys)
    {
        $foreignKeys = array_map(function ($foreignKey) {
            return [
                'table' => $foreignKey['table'],
                'key_name' => $foreignKey['key_name'],
            ];
        }, $foreignKeys);

        file_put_contents(Config::get('fk_adder.foreign_keys_dir'), json_encode($foreignKeys));
    }

    /**
     * Rollback creation of foreign keys.
     *
     * @return void
     */
    public static function rollback()
    {
        // Coming soon!
    }

    /**
     * Return the baseFk for foreign key column creation.
     *
     * @param  string $fk
     * @return \FkAdder\ColumnCreator
     */
    public function baseFk($fk)
    {
        if (!is_null(BaseFk::getFkDatatype($fk))) {
            return new BaseFk($this->table, $fk);
        }

        $class = Config::get('fk_adder.fk_namespace').'\\'.studly_case($fk);

        return new $class($this->table);
    }

    /**
     * Alias of add(). Older version uses addFk(). Recommended to use add().
     * Add a foreign key to table and defer its foreign key creation.
     *
     * @deprecated
     * @param string $fk
     * @param string $column
     * @param string $keyName
     * @param string $onDelete
     * @param string $onUpdate
     * @return \Illuminate\Support\Fluent
     */
    public function addFk($fk, $column = null, $keyName = null, $onDelete = null, $onUpdate = null)
    {
        return $this->add($fk, $column, $keyName, $onDelete, $onUpdate);
    }
}
