<?php

namespace FkAdder;

use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Config;

class FkConfig
{
    /**
     * Fk datatypes and reference table configuration.
     *
     * @var collect
     */
    protected static $all;

    /**
     * Build fk config array.
     *
     * @return collect
     */
    public static function buildFkConfig()
    {
        $config = require Config::get('fk_adder.fk_datatypes_path');

        return collect($config)->map(function ($value, $fk) {
            list($datatype, $referenceTable) = (explode(',', $value.','));

            return new Fluent([
                'datatype' => trim($datatype),
                'referenceTable' => trim($referenceTable ?: BaseFk::guessReferenceTable($fk)),
            ]);
        });
    }

    /**
     * Get all fk config collection.
     *
     * @return collect
     */
    public static function all()
    {
        return static::$all ?: static::$all = static::buildFkConfig();
    }

    /**
     * Get fk config.
     *
     * @param  string $fk
     * @return object
     */
    public static function get($fk)
    {
        return static::all()->get($fk);
    }
}
