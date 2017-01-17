<?php

namespace Corcel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Model
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */

class Model extends Eloquent
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->connection = config('corcel.connection');
        parent::__construct($attributes);
    }

    /**
     * Set the current connection name to the related model instance
     *
     * @param string $related
     * @param null|string $foreignKey
     * @param null|string $localKey
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        $instance->setConnection($this->connection);

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * @param string $related
     * @param null|string $foreignKey
     * @param null|string $localKey
     * @return HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        $instance->setConnection($this->connection);

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * @param string $related
     * @param null $foreignKey
     * @param null $otherKey
     * @param null $relation
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            list(, $caller) = debug_backtrace(false, 2);
            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = snake_case($relation).'_id';
        }

        $instance = new $related;
        $instance->setConnection($this->connection);

        $query = $instance->newQuery();
        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * @param string $related
     * @param null|string $table
     * @param null|string $foreignKey
     * @param null|string $otherKey
     * @param null|string $relation
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related();
        $instance->setConnection($this->connection);

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * @param string $relation
     * @return Model|Collection
     */
    public function getRelation($relation)
    {
        $relation = parent::getRelation($relation);

        if ($relation instanceof Collection) {
            return $relation->each(function (Eloquent $model) {
                $model->setConnection($this->connection);
            });
        }

        return $relation->setConnection($this->connection);
    }
}
