<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

trait HasDefaultStatus
{
    const
        STATUS_PENDING = 0,
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 2,
        STATUS_SUSPENDED = 3,
        STATUS_DELETED = -1;

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_SUSPENDED => 'Suspended',
        self::STATUS_DELETED => 'Deleted',
    ];

    const ACTIVE_STATUSES = [
        self::STATUS_ACTIVE,
    ];

    const INACTIVE_STATUSES = [
        self::STATUS_INACTIVE,
        self::STATUS_SUSPENDED,
        self::STATUS_DELETED,
    ];

    /**
    * Boot the trait and set default status on events
     */
    protected static function bootHasDefaultStatus(): void
    {
        static::creating(function ($model) {
            if (!isset($model->status))
            {
                $model->status = self::STATUS_PENDING;
            }
        });

        if (static::usesSoftDeletes()) {
            static::deleting(function ($model) {
                if (!$model->isForceDeleting())
                {
                    $model->status = self::STATUS_DELETED;
                    $model->saveQuietly();
                }
            });
            static::restoring(function ($model) {
                if ($model->isDirty('status') &&
                    $model->status === self::STATUS_DELETED &&
                    !$model->trashed())
                {
                    $model->shouldSoftDeleteAfterSave = true;
                }
            });
            static::updating(function ($model) {
                if (isset($model->shouldSoftDeleteAfterSave) &&
                    $model->shouldSoftDeleteAfterSave)
                {
                    $model->delete();
                }
            });
        }
    }

    public function statusUi(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'id' => $this->status,
                'description' => $this->statusDescription(),
                'color' => $this->statusColor(),
            ]
        );
    }

    public function statusDescription(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_DELETED => 'Deleted',
            default => 'Unknown',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING, self::STATUS_SUSPENDED => 'warning',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'contrast',
            self::STATUS_DELETED => 'danger',
            default => 'default',
        };
    }

    protected static function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class));
    }

    /**
     * Scope methods to filter some statuses in queries.
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeNotDeleted(Builder $query): Builder
    {
        $query = $query->where('status', '!=', self::STATUS_DELETED);

        if (static::usesSoftDeletes())
        {
            $query = $query->whereNull($this->getDeletedAtColumn());
        }

        return $query;
    }

    public function scopeWithStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeWithStatuses(Builder $query, array $statuses): Builder
    {
        return $query->whereIn('status', $statuses);
    }

    public function scopeWithStatusDeleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DELETED);
    }

    /**
     * Status check methods.
     * These methods can be used to check the status of the model instance.
     */

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return in_array($this->status, self::INACTIVE_STATUSES);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isDeleted(): bool
    {
        $statusDeleted = $this->status === self::STATUS_DELETED;

        if (static::usesSoftDeletes()) {
            return $statusDeleted || $this->trashed();
        }

        return $statusDeleted;
    }

    public function isStatusDeleted(): bool
    {
        return $this->status === self::STATUS_DELETED;
    }

    public function isSoftDeleted(): bool
    {
        return static::usesSoftDeletes() && $this->trashed();
    }

    /**
     * Status transition methods.
     * These methods can be used to change the status of the model instance.
     * They return a boolean indicating if the save was successful.
     */

    public function activate(): bool
    {
        $this->status = self::STATUS_ACTIVE;

        if (static::usesSoftDeletes() && $this->trashed()) {
            return $this->restore();
        }

        return $this->save();
    }

    public function deactivate(): bool
    {
        $this->status = self::STATUS_INACTIVE;
        return $this->save();
    }

    public function suspend(): bool
    {
        $this->status = self::STATUS_SUSPENDED;
        return $this->save();
    }

    public function markAsDeleted(): bool
    {
        if (static::usesSoftDeletes()) {
            return $this->delete();
        } else {
            $this->status = self::STATUS_DELETED;
            return $this->save();
        }
    }

    public function permanentlyDelete(): bool
    {
        if (static::usesSoftDeletes()) {
            return $this->forceDelete();
        } else {
            return $this->delete();
        }
    }

    public function restoreDeleted(): bool
    {
        if (static::usesSoftDeletes() && $this->trashed()) {
            return $this->restore();
        } elseif ($this->status === self::STATUS_DELETED) {
            $this->status = self::STATUS_ACTIVE;
            return $this->save();
        }
        return true;
    }

    public function setPending(): bool
    {
        $this->status = self::STATUS_PENDING;
        return $this->save();
    }

    /**
     * Util methods for statuses.
     * */

    public function getStatusName(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public static function getAllStatuses(): array
    {
        return self::STATUSES;
    }

    public static function getActiveStatuses(): array
    {
        return self::ACTIVE_STATUSES;
    }

    public static function getInactiveStatuses(): array
    {
        return self::INACTIVE_STATUSES;
    }

    public static function isValidStatus(int $status): bool
    {
        return array_key_exists($status, self::STATUSES);
    }

    public function changeStatus(int $newStatus): bool
    {
        if (!self::isValidStatus($newStatus)) {
            throw new \InvalidArgumentException("Invalid Status: {$newStatus}");
        }

        $this->status = $newStatus;
        return $this->save();
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'secondary',
            self::STATUS_SUSPENDED => 'danger',
            self::STATUS_DELETED => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusNameAttribute(): string
    {
        return $this->getStatusName();
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return $this->getStatusBadgeColor();
    }

    public function getIsDeletedAttribute(): bool
    {
        return $this->isDeleted();
    }
}
