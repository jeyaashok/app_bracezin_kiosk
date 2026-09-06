<?php

namespace Tji\Traits;

use Arr;
use Auth;
use Config;
use DB;
use Storage;

/******** Index *********/

trait TjiConstantTrait
{
    public $dbTables;

    public $dbTablesWithColumns;

    public $user;

    public $authUser;

    // Get QueryParameter from Array
    public function buildHttpQuery($input): string
    {
        $query_array = [];
        foreach ($input as $key => $key_value) {
            $query_array[] = urlencode($key).'='.urlencode($key_value);
        }

        return implode('&', $query_array);
    }

    /******************************
        * DB Tables
    *******************************/
    public function getDbTables()
    {
        if (is_null($this->dbTables)) {
            $this->setAllTablesMySQL();
        }

        return (is_null($this->dbTables)) ? [] : $this->dbTables;
    }

    public function getDbTablesWithColumns()
    {
        if (is_null($this->dbTablesWithColumns)) {
            $this->setAllTablesMySQL();
        }

        return (is_null($this->dbTablesWithColumns)) ? [] : $this->dbTablesWithColumns;
    }

    public function deleteDatabaseJson()
    {
        if (Storage::disk('public')->exists('json/database.json')) {
            Storage::disk('public')->delete('json/database.json');
        }

    }

    public function getColumnsByTable($tableName)
    {
        if (in_array($tableName, $this->getDbTables()) && is_array($this->getDbTablesWithColumns()) && Arr::has($this->getDbTablesWithColumns(), $tableName)) {
            return $this->getDbTablesWithColumns()[$tableName];
        }

        return [];
    }

    public function setAllTablesMySQL()
    {
        if (is_null($this->dbTables) || is_null($this->dbTablesWithColumns)) {
            $databaseName = Config::get('database.connections.'.Config::get('database.default'))['database'];
            if (Storage::disk('public')->exists('json/database.json')) {
                $path = storage_path('app/public/json/database.json');
                $storedData = json_decode(file_get_contents($path), true);
                if ($storedData && Arr::has($storedData, 'database')) {
                    $val = $storedData['database'];
                }
                if ($storedData && Arr::has($storedData, 'tables')) {
                    $tables = $storedData['tables'];
                    if (is_null($this->dbTablesWithColumns) || empty($this->dbTablesWithColumns)) {
                        $this->dbTablesWithColumns = $tables;
                    }
                }
                if ($storedData && Arr::has($storedData, 'tableNames')) {
                    $tableNames = $storedData['tableNames'];
                    if (is_null($this->dbTables) || empty($this->dbTables)) {
                        $this->dbTables = $tableNames;
                    }
                }
            } else {
                $val = collect(DB::select('select COLUMN_NAME,TABLE_NAME from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.$databaseName.'" order by TABLE_NAME'))->toArray();

                $tables = [];
                $tableNames = [];
                foreach ($val as $tbl) {
                    if (Arr::has($tables, $tbl->TABLE_NAME)) {
                        array_push($tables[$tbl->TABLE_NAME], $tbl->COLUMN_NAME);
                    } else {
                        $tables[$tbl->TABLE_NAME] = [$tbl->COLUMN_NAME];
                    }
                    if (! in_array($tbl->TABLE_NAME, $tableNames)) {
                        array_push($tableNames, $tbl->TABLE_NAME);
                    }
                }
                if (is_null($this->dbTables) || empty($this->dbTables)) {
                    $this->dbTables = $tableNames;
                }
                if (is_null($this->dbTablesWithColumns) || empty($this->dbTablesWithColumns)) {
                    $this->dbTablesWithColumns = $tables;
                }
                $storeData = [
                    'database' => $val,
                    'tables' => $tables,
                    'tableNames' => $tableNames,
                ];
                Storage::disk('public')->put('json/database.json', json_encode($storeData));
            }
        }
    }

    /******************************
        * Auth User
    *******************************/
    public function setAuthUser($authUser = null, $force = false)
    {
        if (! ($authUser && $authUser->id)) {
            return $this;
        }
        if ($force) {
            $this->authUser = $authUser;

            return $this;
        }
        if (is_null($this->authUser) || ($this->authUser && $authUser && $this->authUser->id != @$authUser->id)) {
            $this->authUser = $authUser;
        }
        if (is_null($this->authUser)) {
            $this->authUser = (auth('admin')->check()) ? auth('admin')->user() : null;
        }

        return $this;
    }

    public function getAuthUser()
    {
        if ($this->authUser && $this->authUser->id) {
            return $this->authUser;
        } elseif (auth('admin')->check()) {
            $authUser = auth('admin')->user();
            $this->setAuthUser($authUser);

            return $authUser;
        } else {
            return null;
        }

        return null;
    }

    public function authUser()
    {
        if ($this->authUser && $this->authUser->id) {
            return $this->authUser;
        } elseif (auth('admin')->check()) {
            $authUser = auth('admin')->user();
            $this->setAuthUser($authUser);

            return $authUser;
        } else {
            return null;
        }

        return null;
    }

    /******************************
        * User
    *******************************/
    public function setUser($user = null, $force = false)
    {
        if (! ($user && $user->id)) {
            return $this;
        }
        if ($force) {
            $this->user = $user;

            return $this;
        }
        if (is_null($this->user) || ($this->user && $user && $this->user->id != @$user->id)) {
            $this->user = $user;
        }

        return $this;
    }

    public function getUser()
    {
        if ($this->user && $this->user->id) {
            return $this->user;
        }

        return null;
    }

    public function user()
    {
        if ($this->user && $this->user->id) {
            return $this->user;
        }

        return null;
    }
}
