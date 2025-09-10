<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class Metrics
{
    public static function ggr(User $user, Carbon $from, Carbon $to): float
    {
        return 0.0;
    }

    public static function revenue(User $user, Carbon $from, Carbon $to): float
    {
        return 0.0;
    }
}
