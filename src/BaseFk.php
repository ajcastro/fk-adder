<?php

namespace FkAdder;

class BaseFk
{
    public $primaryKey = 'id';

    public $onDelete = 'restrict';

    public $onUpdate = 'cascade';

    protected $referenceTable;

    protected $table;

    protected $fk;

    /**
     * Fk datatypes. Registry of datatypes per fk, if ever createFkColumn is just a datatype declaration.
     * For simple fk datatype column creation. See fk_datatypes.php file to add fkDatatypes.
     *
     * @var array
     */
    protected static $fkDatatypes = [];

    public function __construct($table = null, $fk = null)
    {
        $this->table = $table;
        $this->fk    = $fk;
    }

    /**
     * Return array of fkDatatypes.
     *
     * @return array
     */
    public static function fkDatatypes()
    {
        if (!empty(static::$fkDatatypes)) {
            return static::$fkDatatypes;
        }

        $required = require_once Fk::$fkDatatypesPath;

        return static::$fkDatatypes = $required === true ? static::$fkDatatypes : $required;
    }

    /**
     * Return the datatype for the given fk.
     *
     * @param  string $fk
     * @return string|null
     */
    public static function getFkDatatype($fk)
    {
        if (array_key_exists($fk, $fkDatatypes = static::fkDatatypes())) {
            return $fkDatatypes[$fk];
        }
    }

    /**
     * Return the datatype of the fk.
     *
     * @param  string $fk
     * @return string|mixed
     */
    public function datatype()
    {
        return static::getFkDatatype($this->fk);
    }

    /**
     * Create the foreign key column of the table. Contains the data type definition of the column foreign key.
     *
     * @param  string $column
     * @return \Illuminate\Support\Fluent
     */
    public function createFkColumn($column)
    {
        return call_user_func_array([$this->table, $this->datatype()], [$column]);
    }

    /**
     * Return the default column name of a foreign key.
     *
     * @return string
     */
    public function defaultColumn()
    {
        return $this->fk ?: snake_case(last(explode('\\', get_class($this))));
    }

    public function referenceTable()
    {
        return $this->referenceTable ?: $this->referenceTable = str_plural(str_replace('_id', '', $this->defaultColumn()));
    }
}
