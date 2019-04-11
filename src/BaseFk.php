<?php

namespace FkAdder;

use Illuminate\Support\Facades\Config;

class BaseFk
{
    public $primaryKey = 'id';

    public $onDelete = 'restrict';

    public $onUpdate = 'cascade';

    protected $referenceTable;

    protected $table;

    protected $fk;

    protected $datatype;

    public function __construct($table = null, $fk = null, $datatype = null, $referenceTable = null)
    {
        $this->table          = $table;
        $this->fk             = $fk;
        $this->datatype       = $datatype;
        $this->referenceTable = $referenceTable;
    }

    /**
     * Return the datatype of the fk.
     *
     * @param  string $fk
     * @return string|mixed
     */
    public function datatype()
    {
        return $this->datatype;
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

    /**
     * Return the reference table of a foreign key.
     *
     * @return string
     */
    public function referenceTable()
    {
        return $this->referenceTable ?: $this->referenceTable = static::guessReferenceTable($this->defaultColumn());
    }

    /**
     * Guess reference table.
     *
     * @param  string $fk
     * @return string
     */
    public static function guessReferenceTable($fk)
    {
        return str_plural(str_replace('_id', '', $fk));
    }
}
