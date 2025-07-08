<?php
namespace App\Helpers;

class Helper
{
    /**
     * Generate unique cache key based on filters
     */
    public static function generateProductsCacheKey($model, array $params): string
    {
        ksort($params); // Sort parameters for consistent key generation
        return "{$model}_" . md5(json_encode($params));
    }
}