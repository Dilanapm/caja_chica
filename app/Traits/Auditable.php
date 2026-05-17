<?php

namespace App\Traits;

use App\Models\Audit;

trait Auditable
{
    private array $auditOldValues = [];

    private static array $auditExclude = ['created_at', 'updated_at', 'remember_token', 'password'];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            Audit::record('created', $model, [], self::auditFilter($model->getAttributes()));
        });

        static::updating(function ($model) {
            $model->auditOldValues = $model->getOriginal();
        });

        static::updated(function ($model) {
            $changed = array_keys($model->getChanges());
            if (empty($changed)) {
                return;
            }
            $old = self::auditFilter(array_intersect_key($model->auditOldValues, array_flip($changed)));
            $new = self::auditFilter(array_intersect_key($model->getChanges(), array_flip($changed)));
            Audit::record('updated', $model, $old, $new);
        });

        static::deleted(function ($model) {
            Audit::record('deleted', $model, self::auditFilter($model->getAttributes()), []);
        });
    }

    private static function auditFilter(array $fields): array
    {
        return array_diff_key($fields, array_flip(self::$auditExclude));
    }
}
