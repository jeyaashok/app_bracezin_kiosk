<?php

namespace Tji\Traits;

use Arr;
use ErrorResponse;
use Str;
use Sys;

/******* Available Index *********
 * General Filters:
 * GetData($query, $count, $input = null)
 * IncludeFilter($query, $input = null) => collections for 'with' Query
 * SearchFilter($query, $input = null)
 * OrderByFilter($query, $input = null)
 * SlugFilterOn($query, $input = null)
 * scopeStringFilterOn($query, $input = null, $columnName)
 * scopeIdFilterOn($query, $input = null, $columnName)
 * scopeMorphFilterOn($query, $input = null, $columnName)
 * scopeBooleanFilterOn($query, $input = null, $columnName)
 * scopeUniqueFilterOn($query, $input = null, $columnName)
 * scopeNullFilterOn($query, $input = null)
 * DeletedFilter($query, $input = null)
 * NearbyFilterOn($query, $input = null)
 ******* Index *********/

trait FilterTrait
{
    /**
     * Scope Set Append Attribute Dynamically.
     *
     * @input $input = [] with contains appends params
     *
     * @return $query
     */
    public function scopeSetAppendByRequest($query, $input = null)
    {
        $appends = Arr::has($input, 'appends') ? $input['appends'] : null;
        if ($appends) {
            $appends = str_replace(['|'], ',', $appends);
            $appends = explode(',', $appends);
            $oldAppends = $this->getAppends();
            if ($oldAppends && count($oldAppends) > 0) {
                $newArray = array_filter(array_merge($oldAppends, $appends));

                return $this->setAppends($newArray);
            } else {
                $newArray = array_filter($appends);

                return $this->setAppends($newArray);
            }
        }

        return $this;
    }

    /**
     * Scope Search Filter.
     *
     * @input $input = [] with contains Search params
     *
     * @return $query
     */
    public function scopeSearchFilter($query, $input = null)
    {
        $search = Arr::has($input, 'search') ? $input['search'] : null;

        return $query->when($search, function ($query) use ($search) {
            return $query->search($search, null, true, true);
        });
    }

    /**
     * Scope Collect Data.
     *
     * @input $count = number; count of All Records
     * @input $input = [] with contains all, paginate, page params
     *
     * @return $query
     */
    public function scopeGetData($query, $count, $input = null)
    {
        $array = Arr::has($input, 'array') ? $input['array'] : false;
        $all = Arr::has($input, 'all') ? $input['all'] : 0;
        $paginate = Arr::has($input, 'paginate') ? $input['paginate'] : $count;
        $page = Arr::has($input, 'page') ? $input['page'] : 1;

        $only = Arr::has($input, 'only') ? $input['only'] : null;
        if ($only && ! is_array($only)) {
            $replaces = ['[', ']', '-', ' ', '{', '}', '(', ')', '&#39;'];
            $only = explode(',', str_replace($replaces, '', $only));
        } else {
            $only = ['*'];
        }
        if ($only && is_array($only)) {
            $only = array_filter($only);
        }
        if ((int) $all < 1 && (bool) $array) {
            return $query->paginate($paginate, $only)->toArray();
        }
        if ((int) $all < 1 && ! (bool) $array) {
            return $query->paginate($paginate, $only, 'page', $page);
        }
        if ((int) $all > 0) {
            return ($only !== ['*']) ? $query->get($only) : $query->get();
        }
    }

    /**
     * Scope SelectColumn Filter.
     *
     * @input $input = [] with contains select params
     * @input select => string / Array (OR) with => string / Array
     *
     * @return $query
     */
    public function scopeSelectColumnFilter($query, $input = null)
    {
        $selectColumn = Arr::has($input, 'select') ? $input['select'] : null;
        $selectColumn = (! $selectColumn && Arr::has($input, 'selectColumn')) ? $input['selectColumn'] : $selectColumn;
        if ($selectColumn === null || $selectColumn === '') {
            return $query;
        }
        if ($selectColumn && ! is_array($selectColumn)) {
            $replaces = ['[', ']', '-', ' ', '{', '}', '(', ')', '&#39;'];
            $selectColumn = explode(',', str_replace($replaces, '', $selectColumn));
        }

        return $query->when(($selectColumn !== null && $selectColumn !== ''), function ($query) use ($selectColumn) {
            $query->select($selectColumn);

            return $query;
        });
    }

    /**
     * Scope Include Filter.
     *
     * @input $input = [] with contains includes params
     * @input includes => string / Array (OR) with => string / Array
     *
     * @return $query
     */
    public function scopeIncludeFilter($query, $input = null)
    {
        $include = Arr::has($input, 'include') ? $input['include'] : null;
        $include = (! $include && Arr::has($input, 'with')) ? $input['with'] : $include;
        if ($include && ! is_array($include)) {
            $replaces = ['[', ']', '-', ' ', '_', '{', '}', '(', ')', '&#39;'];
            $include = explode(',', str_replace($replaces, '', $include));
        }

        return $query->when(($include !== null), function ($query) use ($include) {
            for ($i = 0; $i < count($include); $i++) {
                $query->with($include[$i]);
            }

            return $query;
        });
    }

    /**
     * Scope OrderBy Filter.
     *
     * @input $input = [] with contains order_by & order_direction params
     * @input order_by => string (AND) order_direction => (string) = 'asc' / 'desc'
     * @input order => (String)CSV Array ex: order=column1|asc,column2|desc....
     *
     * @return $query
     */
    public function scopeOrderByFilter($query, $input = null, $tableName = null)
    {
        $orders = [];
        $order = Arr::has($input, 'order') ? $input['order'] : null;
        if ($order) {
            $orders = explode(',', $order);
        }
        $orderBy = Arr::has($input, 'order_by') ? $input['order_by'] : null;
        $orderMethod = Arr::has($input, 'order_direction') ? $input['order_direction'] : 'asc';
        $orderMethod = ($orderMethod === 'asc' || $orderMethod === 'desc') ? $orderMethod : 'asc';
        if ($orderBy && $orderMethod) {
            $orders = array_merge($orders, [$orderBy.'|'.$orderMethod]);
        }

        if ($orders && count($orders) > 0) {
            // $columnArray = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
            $columnArray = Sys::getColumnsByTable($this->getTable());
            $query = $query->when(($orders && count($orders) > 0), function ($query) use ($orders, $columnArray, $tableName) {
                foreach ($orders as $key => $order) {
                    $data = explode('|', $order);
                    if (in_array($data[0], $columnArray)) {
                        $column = ($tableName) ? $tableName.'.'.$data[0] : $data[0];
                        $query->orderBy($column, $data[1]);
                    }
                }

                return $query;
            });
        }

        return $query;
    }

    /**
     * Scope Slug Filter.
     *
     * @input $input = [] with contains slug
     * @input columnName => string (Optional)
     * @input slug => string
     *
     * @return $query
     */
    public function scopeSlugFilterOn($query, $input = null, $columnName = null, $withReplaces = true, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : 'slug';
        if ($columnName === null || $columnName === '') {
            return $query;
        }
        $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;
        if (! is_null($scopeValue) && ! is_array($scopeValue)) {
            $replaces = ($withReplaces) ? ['[', ']', ' ', '_', '{', '}', '(', ')', '&#39;'] : [];
            $scopeValue = explode(',', str_replace($replaces, '', $scopeValue));
        }
        if (is_array($scopeValue)) {
            $scopeValue = array_values(array_filter(array_map(function ($value) {
                return Str::slug($value, '-');
            }, $scopeValue)));
        } elseif (! is_null($scopeValue) && $scopeValue !== '') {
            $scopeValue = [Str::slug($scopeValue, '-')];
        }

        return $query->when(($scopeValue && $scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
            if (is_array($scopeValue) && count($scopeValue) > 1) {
                $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                return $query->whereIn($column, $scopeValue);
            } else {
                $newScopeValue = is_array($scopeValue) ? ($scopeValue[0] ?? null) : $scopeValue;
                if (is_null($newScopeValue)) {
                    return $query;
                }
                $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                return $query->where($column, $newScopeValue);
            }
        });
    }

    /**
     * Scope String Filter.
     *
     * @input $input = [] with contains slug
     * @input columnName => string (Should be as String Column)
     *
     * @return $query
     */
    public function scopeStringFilterOn($query, $input = null, $columnName = null, $withReplaces = true, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '') {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $replaces = ($withReplaces) ? ['[', ']', '-', ' ', '_', '{', '}', '(', ')', '&#39;'] : [];
                $scopeValue = explode(',', str_replace($replaces, '', $scopeValue));
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                    return $query->whereIn($column, $scopeValue);
                } else {
                    $newScopeValue = is_array($scopeValue) ? ($scopeValue[0] ?? null) : $scopeValue;
                    if (is_null($newScopeValue)) {
                        return $query;
                    }
                    $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                    return $query->where($column, $newScopeValue);
                }
            });
        }
    }

    /**
     * Scope Id Above and Below Filter.
     *
     * @input $input = [] with contains given columnName
     * @input $input = [] ; if Params occured include_parent = true then will collect id also
     * @input columnName => string (Should be as Integer Column)
     *
     * @return $query
     */
    public function scopeIdSortFilterOn($query, $input = null, $columnName = 'id', $tableName = null)
    {
        $sorts = [];
        $sort = Arr::has($input, 'idsort') ? $input['idsort'] : null;
        if ($sort) {
            $sorts = explode(',', $sort);
        }
        $sortBy = Arr::has($input, 'sort_by') ? $input['sort_by'] : null;
        $sortMethod = Arr::has($input, 'sort_direction') ? $input['sort_direction'] : 'below';
        $sortMethod = ($sortMethod === 'above' || $sortMethod === 'below') ? $sortMethod : 'below';
        if ($sortBy && $sortMethod) {
            $sorts = array_merge($sorts, [$sortBy.'|'.$sortMethod]);
        }

        if ($sorts && count($sorts) > 0) {
            $columnArray = Sys::getColumnsByTable($this->getTable());
            $query = $query->when(($sorts && count($sorts) > 0), function ($query) use ($sorts, $columnName, $columnArray, $tableName) {
                foreach ($sorts as $key => $sort) {
                    $data = explode('|', $sort);
                    if (in_array($columnName, $columnArray)) {
                        $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;
                        $direction = ($data[1] && $data[1] === 'above') ? '>' : '<';
                        $query->where($column, $direction, $data[0]);
                    }
                }

                return $query;
            });
        }

        return $query;
    }

    /**
     * Scope Id Filter.
     *
     * @input $input = [] with contains given columnName
     * @input $input = [] ; if Params occured include_parent = true then will collect id also
     * @input columnName => string (Should be as Integer Column)
     *
     * @return $query
     */
    public function scopeIdFilterOn($query, $input = null, $columnName = null, $checkParent = false, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        $includeParent = (Arr::has($input, 'include_parent') && $input['include_parent'] === 'true') ? true : false;
        if (! $columnName || $columnName === '') {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $replaces = ['[', ']', '-', ' ', '_', '{', '}', '(', ')', '&#39;'];
                $scopeValue = array_map('intval', explode(',', str_replace($replaces, '', $scopeValue)));
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $includeParent, $checkParent, $tableName) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    if ($includeParent && $checkParent) {
                        $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                        return $query->whereIn($column, $scopeValue)->orWhereIn('id', $scopeValue);
                    } else {
                        $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                        return $query->whereIn($column, $scopeValue);
                    }
                } else {
                    $newScopeValue = is_array($scopeValue) ? ($scopeValue[0] ?? null) : $scopeValue;
                    if (is_null($newScopeValue)) {
                        return $query;
                    }
                    if ($includeParent && $checkParent) {
                        $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                        return $query->where($column, $newScopeValue)->orWhere('id', $newScopeValue);
                    } else {
                        $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                        return $query->where($column, $newScopeValue);
                    }
                }
            });
        }
    }

    /**
     * Scope IdRelation Filter.
     *
     * @input $input = [] with contains given columnName
     * @input $input = [] ; if Params occured include_parent = true then will collect id also
     * @input columnName => string (Should be as Integer Column)
     *
     * @return $query
     */
    public function scopeIdRelationFilterOn($query, $input = null, $columnName = null, $relation = null, $relationType = 'hasMany')
    {
        $columnName = ($columnName) ? $columnName : null;
        $relation = ($relation) ? $relation : null;
        if ((! $columnName || $columnName === '') && ! $relation) {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $replaces = ['[', ']', '-', ' ', '_', '{', '}', '(', ')', '&#39;'];
                $scopeValue = array_map('intval', explode(',', str_replace($replaces, '', $scopeValue)));
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $relation, $relationType) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    if ($relationType === 'hasMany') {
                        return $query->whereHas($relation, function ($q) use ($columnName, $scopeValue) {
                            return $q->whereIn($columnName, $scopeValue);
                        });
                    } else {
                        return $query;
                    }
                } else {
                    $newScopeValue = is_array($scopeValue) ? ($scopeValue[0] ?? null) : $scopeValue;
                    if (is_null($newScopeValue)) {
                        return $query;
                    }
                    if ($relationType === 'hasMany') {
                        return $query->whereHas($relation, function ($q) use ($columnName, $newScopeValue) {
                            return $q->where($columnName, $newScopeValue);
                        });
                    } else {
                        return $query;
                    }
                }
            });
        }
    }

    /**
     * Scope Morph Filter.
     *
     * @input $input = [] with contains given columnName
     * @input columnName => string (Should be as Integer Column)
     *
     * @return $query
     */
    public function scopeMorphFilterOn($query, $input = null, $columnName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '') {
            return $query;
        } else {
            $scopeIdValue = Arr::has($input, $columnName.'_id') ? $input[$columnName.'_id'] : null;
            $scopeTypeValue = Arr::has($input, $columnName.'_type') ? $input[$columnName.'_type'] : null;

            if ($scopeIdValue && $scopeTypeValue) {
                if ($scopeIdValue && ! is_array($scopeIdValue)) {
                    $replaces = ['[', ']', '-', ' ', '{', '}', '(', ')', '&#39;'];
                    $scopeIdValue = array_filter(explode(',', str_replace($replaces, '', $scopeIdValue)));
                }

                if ($scopeTypeValue && ! is_array($scopeTypeValue)) {
                    $replaces = ['[', ']', '-', ' ', '{', '}', '(', ')', '&#39;'];
                    $scopeTypeValue = array_filter(explode(',', str_replace($replaces, '', $scopeTypeValue)));
                }

                if ($scopeIdValue && is_array($scopeIdValue) && count($scopeIdValue) === 1 &&
                    $scopeTypeValue && is_array($scopeTypeValue) && count($scopeTypeValue) === 1) {
                    return $query->when(($scopeIdValue !== null), function ($query) use ($scopeIdValue, $columnName) {
                        return $query->where($columnName.'_id', '=', $scopeIdValue[0]);
                    })
                        ->when(($scopeTypeValue !== null), function ($query) use ($scopeTypeValue, $columnName) {
                            return $query->where($columnName.'_type', '=', $scopeTypeValue[0]);
                        });
                } else {
                    return $query->when(($scopeIdValue !== null), function ($query) use ($scopeIdValue, $columnName) {
                        return $query->whereIn($columnName.'_id', $scopeIdValue);
                    })
                        ->when(($scopeTypeValue !== null), function ($query) use ($scopeTypeValue, $columnName) {
                            return $query->whereIn($columnName.'_type', $scopeTypeValue);
                        });
                }
            } else {
                $morphs = Arr::has($input, $columnName) ? explode(',', $input[$columnName]) : [];
                if ($morphs && count($morphs) > 0) {
                    foreach ($morphs as $key => $morph) {
                        $morphId = explode('|', $morph)[0] ?? null;
                        $morphType = explode('|', $morph)[1] ?? null;
                        if (! $morphId || ! $morphType) {
                            continue;
                        }
                        if ($key === 0) {
                            $query->where(function ($q) use ($morphId, $morphType, $columnName) {
                                return $q->whereIn($columnName.'_id', [$morphId])
                                    ->whereIn($columnName.'_type', [$morphType]);
                            });
                        } else {
                            $query->orWhere(function ($q) use ($morphId, $morphType, $columnName) {
                                return $q->whereIn($columnName.'_id', [$morphId])
                                    ->whereIn($columnName.'_type', [$morphType]);
                            });
                        }
                    }
                }

                return $query;
            }
        }
    }

    /**
     * Scope Boolean Filter.
     *
     * @input $input = [] with contains columnName params
     * @input $columnName = "true"/"false"/0/1
     *
     * @return $query
     */
    public function scopeBooleanFilterOn($query, $input = null, $columnName = null, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '') {
            return $query;
        } else {
            $scopeValue = (Arr::has($input, $columnName) && $input[$columnName] !== null) ? $input[$columnName] : null;
            if (! is_null($scopeValue)) {
                $scopeValue = ($scopeValue === 'true' || $scopeValue === true || $scopeValue === 1 || $scopeValue === '1') ? 1 : 0;
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
                $column = ($tableName) ? $tableName.'.'.$columnName : $columnName;

                return $query->where($column, $scopeValue);
            });
        }
    }

    /**
     * Scope Null Filter.
     *
     * @input $input = [] with contains null params
     * @input null => (String)CSV Array ex: null=column1|true,column2|false....
     *
     * @return $query
     */
    public function scopeNullFilterOn($query, $input = null, $tableName = null)
    {
        $nullDatas = [];
        $null = Arr::has($input, 'null') ? $input['null'] : null;
        if ($null) {
            $nullDatas = explode(',', $null);
        }
        $query = $query->when(($nullDatas && count($nullDatas) > 0), function ($query) use ($nullDatas, $tableName) {
            foreach ($nullDatas as $key => $nullData) {
                $data = explode('|', $nullData);
                if ($data[1] && ($data[1] === true || $data[1] === 'true')) {
                    $column = ($tableName) ? $tableName.'.'.$data[0] : $data[0];
                    $query->whereNull($column);
                } else {
                    $column = ($tableName) ? $tableName.'.'.$data[0] : $data[0];
                    $query->whereNotNull($column);
                }
            }

            return $query;
        });

        return $query;
    }

    /**
     * Scope Deleted Filter.
     *
     * @input $input = [] with contains deleted_at String
     * deleted_at = "all / only"
     *
     * @return $query
     */
    public function scopeDeletedFilter($query, $input = null)
    {
        $deletedAt = Arr::has($input, 'deleted_at') ? $input['deleted_at'] : null;

        return $query->when($deletedAt, function ($query) use ($deletedAt) {
            switch ($deletedAt) {
                case 'only':
                    // return $query;
                    return $query->onlyTrashed();
                    break;
                case 'all':
                    // return $query;
                    return $query->withTrashed();
                    break;
                default:
                    return $query;
                    break;
            }
        });
    }

    // /*** RAW Table Record Filters *** /////

    /**
     * Scope String Filter.
     *
     * @input $input = [] with contains slug
     * @input columnName => string (Should be as String Column)
     *
     * @return $query
     */
    public function scopeStringRawFilterOn($query, $input = null, $columnName = null, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '' || ! $tableName) {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $replaces = ['[', ']', '{', '}', '(', ')', '&#39;'];
                $scopeValue = explode(',', str_replace($replaces, '', $scopeValue));
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    return $query->whereIn($tableName.'.'.$columnName, $scopeValue);
                } else {
                    $newScopeValue = $scopeValue[0];

                    return $query->where($tableName.'.'.$columnName, $newScopeValue);
                }
            });
        }
    }

    /**
     * Scope String Filter.
     *
     * @input $input = [] with contains slug
     * @input columnName => string (Should be as String Column)
     *
     * @return $query
     */
    public function scopeStringRawBlankFilterOn($query, $input = null, $columnName = null, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '' || ! $tableName) {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $scopeValue = [$scopeValue];
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    return $query->whereIn($tableName.'.'.$columnName, $scopeValue);
                } else {
                    $newScopeValue = $scopeValue[0];

                    return $query->where($tableName.'.'.$columnName, $newScopeValue);
                }
            });
        }
    }

    /**
     * Scope Id Filter.
     *
     * @input $input = [] with contains given columnName
     * @input $input = [] ; if Params occured include_parent = true then will collect id also
     * @input columnName => string (Should be as Integer Column)
     *
     * @return $query
     */
    public function scopeIdRawFilterOn($query, $input = null, $columnName = null, $tableName = null, $checkParent = false)
    {
        $columnName = ($columnName) ? $columnName : null;
        $includeParent = (Arr::has($input, 'include_parent') && $input['include_parent'] === 'true') ? true : false;
        if (! $columnName || $columnName === '' || ! $tableName) {
            return $query;
        } else {
            $scopeValue = Arr::has($input, $columnName) ? $input[$columnName] : null;

            if ($scopeValue && ! is_array($scopeValue)) {
                $replaces = ['[', ']', '-', ' ', '_', '{', '}', '(', ')', '&#39;'];
                $scopeValue = array_map('intval', explode(',', str_replace($replaces, '', $scopeValue)));
            }

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $includeParent, $tableName, $checkParent) {
                if (is_array($scopeValue) && count($scopeValue) > 1) {
                    if ($includeParent && $checkParent) {
                        return $query->whereIn($tableName.'.'.$columnName, $scopeValue)->orWhereIn($tableName.'.'.'id', $scopeValue);
                    } else {
                        return $query->whereIn($tableName.'.'.$columnName, $scopeValue);
                    }
                } else {
                    $newScopeValue = $scopeValue[0];
                    if ($includeParent && $checkParent) {
                        return $query->where($tableName.'.'.$columnName, $newScopeValue)->orWhere($tableName.'.'.'id', $newScopeValue);
                    } else {
                        return $query->where($tableName.'.'.$columnName, $newScopeValue);
                    }
                }
            });
        }
    }

    /**
     * Scope Boolean Filter.
     *
     * @input $input = [] with contains columnName params
     * @input $columnName = "true"/"false"/0/1
     *
     * @return $query
     */
    public function scopeBooleanRawFilterOn($query, $input = null, $columnName = null, $tableName = null)
    {
        $columnName = ($columnName) ? $columnName : null;
        if (! $columnName || $columnName === '' || ! $tableName) {
            return $query;
        } else {
            $scopeValue = (Arr::has($input, $columnName) && $input[$columnName] !== null) ? (bool) json_decode(strtolower($input[$columnName]), true) : null;

            return $query->when(($scopeValue !== null), function ($query) use ($scopeValue, $columnName, $tableName) {
                return $query->where($tableName.'.'.$columnName, $scopeValue);
            });
        }
    }

    public function getWhereHasMorph()
    {
        return Arr::get($this->searchable, 'whereHasMorph', []);
    }

    public function ScopeMakeWhereHasMorph($query, $input)
    {
        $search = Arr::has($input, 'search') ? $input['search'] : null;
        if ($search) {
            $search = mb_strtolower(trim($search));
            foreach ($this->getWhereHasMorph() as $table => $keys) {
                if ($table && $keys) {
                    // $query->orWhereHasMorph('resource', ['Whatsapp\\Models\\Whatsapp'], function($q) use ($search) {
                    // 	$q->where('from', 'LIKE', '%'.$search.'%');
                    // });

                    $query->orWhereHasMorph($table, $keys[0], function ($q) use ($keys, $search) {
                        $q->where($keys[1], 'LIKE', '%'.$search.'%');
                    });
                } else {

                }
            }

            return $query;
        } else {
            return $query;
        }
    }

    /**
     * Scope Nearby Filter.
     *
     * @input $input = [] with contains nearby params
     * @input nearby => (number) ex: nearby=25.
     *
     * @return $query
     */
    public function scopeNearbyFilterOn($query, $input = null, $tableName = null)
    {
        $nearby = Arr::has($input, 'nearby') ? (int) @$input['nearby'] : null;
        if ($nearby && $nearby > 0) {
            $latitude = @$input['latitude'] ?: null;
            $longitude = @$input['longitude'] ?: null;
            if (is_null($latitude) || is_null($longitude)) {
                throw new ErrorResponse('Required Current Location latitude and longitude', 405, 'info');
            }
            $radius = (int) $nearby; // radius in kilometers, change as per our need
            $earthRadius = 6378.14; // Earth's radius in kilometers
            $maxLat = $latitude + rad2deg($radius / $earthRadius);
            $minLat = $latitude - rad2deg($radius / $earthRadius);
            $maxLon = $longitude + rad2deg(asin($radius / $earthRadius) / cos(deg2rad($latitude)));
            $minLon = $longitude - rad2deg(asin($radius / $earthRadius) / cos(deg2rad($latitude)));

            return $query->when(($nearby && $nearby > 0), function ($q) use ($minLat, $maxLat, $minLon, $maxLon, $tableName) {
                if (! is_null($minLat) && ! is_null($maxLat) && ! is_null($minLon) && ! is_null($maxLon)) {
                    $latColumn = ($tableName) ? $tableName.'.latitude' : 'latitude';
                    $lonColumn = ($tableName) ? $tableName.'.longitude' : 'longitude';

                    return $q->whereBetween($latColumn, [$minLat, $maxLat])
                        ->whereBetween($lonColumn, [$minLon, $maxLon]);
                } else {
                    return $q;
                }
            });
        }

        return $query;
    }
}
