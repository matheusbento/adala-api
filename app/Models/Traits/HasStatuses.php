<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Exceptions\InvalidStatusReversal;
use App\Models\BaslakeModelStatus;

/**
 * Trait HasStatuses
 * @package App\Models\Traits
 *
 * @property-read string|null $primary_status {@see HasStatuses::getPrimaryStatusAttribute()}
 * @property-read string|null $secondary_status {@see HasStatuses::getSecondaryStatusAttribute()}
 * @property-read array|null $statuses {@see HasStatuses::getStatusesAttribute()}
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaslakeModelStatus[] whereCurrentStatus($value)
 */
trait HasStatuses
{
    public static function bootHasStatuses()
    {
        static::created(function (Model $model) {
            if ($model->currentStatusColumn && $model->{$model->currentStatusColumn}) {
                $model->setStatus($model->{$model->currentStatusColumn});
            }
        });
    }

    public static function isPrimary(string $status): bool
    {
        return in_array($status, config('model-status.' . self::class . '.primary', []));
    }

    public static function isSecondary(string $status): bool
    {
        return in_array($status, config('model-status.' . self::class . '.secondary', []));
    }

    public static function isValid(string $status): bool
    {
        return self::isPrimary($status) || self::isSecondary($status);
    }

    /**
     * @param string $status
     * @return string|null
     * @throws Exception
     */
    public static function getType(string $status): ?string
    {
        if (is_null($status)) {
            return null;
        }
        if (self::isPrimary($status)) {
            return 'primary';
        }
        if (self::isSecondary($status)) {
            return 'secondary';
        }
        throw new Exception('Status ' . $status . ' is invalid.');
    }

    /**
     * @param string|null $status
     * @param string|null $reason
     * @param null $type
     * @return BaslakeModelStatus
     * @throws Exception
     */
    public function setStatus(?string $status, ?string $reason = null, $type = null): BaslakeModelStatus
    {
        if (!$this->id) {
            throw new Exception('Model must be saved before setting statuses.');
        }
        if ($this->currentStatusColumn) {
            $this->{$this->currentStatusColumn} = $status;
            $this->save();
        }

        $type = $type ?: self::getType($status);

        /**
         * @var BaslakeModelStatus $newStatus
         */
        $newStatus = null;
        if ($type === 'primary') {
            $newStatus = $this->statuses()->create([
                'type' => $type,
                'status' => $status,
                'reason' => $reason,
            ]);
        } else {
            $newStatus = $this->statuses()->create([
                'type' => 'secondary',
                'status' => $status,
                'reason' => $reason,
            ]);
        }
        return $newStatus;
    }

    public function statuses(): MorphMany
    {
        return $this->morphMany(BaslakeModelStatus::class, 'model')
            ->orderBy('id', 'DESC');
    }

    public function currentPrimaryStatus(): MorphOne
    {
        return $this->morphOne(BaslakeModelStatus::class, 'model')
            ->where('type', 'primary')
            ->orderBy('id', 'DESC');
    }

    // Ignores mutator;
    public function rawStatuses(): MorphMany
    {
        return $this->morphMany(BaslakeModelStatus::class, 'model')
            ->orderBy('id', 'DESC');
    }

    public function primaryStatus(): ?BaslakeModelStatus
    {
        $morphMany = $this->morphMany(BaslakeModelStatus::class, 'model')
            ->whereType('primary')
            ->orderBy('id', 'DESC');

        return $morphMany->latest()->first();
    }

    public function secondaryStatus(): ?BaslakeModelStatus
    {
        $morphMany = $this->morphMany(BaslakeModelStatus::class, 'model')
            ->whereType('secondary')
            ->orderBy('id', 'DESC');

        return $morphMany->latest()->first();
    }

    public function getStatusesAttribute(): array
    {
        $morphMany = $this->morphMany(BaslakeModelStatus::class, 'model')
            ->orderBy('id', 'DESC');
        return $morphMany->pluck('status')->all();
    }

    public function getStatusAttribute(): ?string
    {
        return optional($this->primaryStatus())->status;
    }

    public function getPrimaryStatusAttribute(): ?string
    {
        $morphMany = $this->morphMany(BaslakeModelStatus::class, 'model')
            ->whereType('primary')
            ->orderBy('id', 'DESC');

        return optional($morphMany->latest()->first())->status ?: null;
    }

    public function getSecondaryStatusAttribute(): ?string
    {
        $morphMany = $this->morphMany(BaslakeModelStatus::class, 'model')
            ->whereType('secondary')
            ->orderBy('id', 'DESC');

        return optional($morphMany->latest()->first())->status;
    }

    /**
     * @return string Previous status or the current one, if there is no previous status
     */
    public function getPreviousStatus(): ?string
    {
        $statuses = $this->statuses()
            ->whereNotNull('status')
            ->get();
        if (count($statuses) > 1) {
            return $statuses[1]->status;
        }
        return $this->status;
    }

    /**
     * @throws InvalidStatusReversal
     * @throws Exception
     */
    public function revertLastPrimaryStatus()
    {
        $statuses = $this->statuses()
            ->whereType('primary');
        $statusArray = $statuses->get();
        if (count($statusArray) > 1) {
            $this->setStatus($statusArray[1]->status);
        } else {
            throw new InvalidStatusReversal();
        }
    }

    /**
     * @param string $status
     * @throws InvalidStatusReversal
     * @throws Exception
     */
    public function revertIfCurrentStatus(string $status)
    {
        if (self::isPrimary($status)) {
            if ($this->primary_status !== $status) {
                return;
            }
            $this->revertLastPrimaryStatus();
        }
        if (self::isSecondary($status)) {
            if ($this->secondary_status !== $status) {
                return;
            }
            $previousStatus = $this->getPreviousStatus();
            $this->setStatus(null, null, 'secondary');
            $this->setStatus($previousStatus);
        }
    }

    /**
     * @param Builder $builder
     * @param string $status
     * @return Builder
     */
    public function scopeWherePrimaryStatus(Builder $builder, string $status)
    {
        return $builder->whereHas('currentPrimaryStatus', function ($query) use ($status) {
            $query->where('status', $status);
        });
    }

    /**
     * @param $query
     * @param string $status
     * @return mixed
     * @throws Exception
     */
    public function scopeWhereCurrentStatus($query, string $status)
    {
        if (!$this->currentStatusColumn) {
            throw new \Exception('HasTrait currentStatusColumn is required to use this method.');
        }
        return $query->where($this->currentStatusColumn, '=', $status);
    }

    /**
     * @param $query
     * @param array $statuses
     * @return mixed
     * @throws Exception
     */
    public function scopeWhereCurrentStatusIn($query, array $statuses)
    {
        if (!$this->currentStatusColumn) {
            throw new \Exception('HasTrait currentStatusColumn is required to use this method.');
        }
        return $query->whereIn($this->currentStatusColumn, $statuses);
    }

    /**
     * @param $query
     * @param string $status
     * @return mixed
     * @throws Exception
     */
    public function scopeWhereCurrentStatusNot($query, string $status)
    {
        if (!$this->currentStatusColumn) {
            throw new \Exception('HasTrait currentStatusColumn is required to use this method.');
        }
        return $query->where($this->currentStatusColumn, '!=', $status);
    }

    /**
     * @param $query
     * @param array $statuses
     * @return mixed
     * @throws Exception
     */
    public function scopeWhereCurrentStatusNotIn($query, array $statuses)
    {
        if (!$this->currentStatusColumn) {
            throw new \Exception('HasTrait currentStatusColumn is required to use this method.');
        }
        return $query->whereNotIn($this->currentStatusColumn, $statuses);
    }
}
