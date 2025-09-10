<?php

namespace App\Services;

use App\Models\AffiliateCommissionLog;
use App\Models\AffiliatePlan;
use App\Models\User;
use App\Models\UserAffiliateSetting;
use Carbon\Carbon;

class AffiliateCommissionService
{
    public function planFor(User $affiliate): array
    {
        $settings = $affiliate->affiliateSetting;

        if ($settings && $settings->override_type) {
            return [
                'type' => $settings->override_type,
                'ggr'  => $settings->override_ggr_share,
                'rev'  => $settings->override_rev_share,
                'cpa'  => $settings->override_cpa_amount,
            ];
        }

        if ($settings && $settings->affiliate_plan_id) {
            if ($plan = AffiliatePlan::active()->find($settings->affiliate_plan_id)) {
                return $this->fromPlan($plan);
            }
        }

        $defaultId = setting('affiliate.default_plan_id');
        $plan = AffiliatePlan::active()->find($defaultId);
        return $this->fromPlan($plan);
    }

    protected function fromPlan(?AffiliatePlan $plan): array
    {
        if (!$plan) {
            return ['type' => 'GGR', 'ggr' => 0, 'rev' => 0, 'cpa' => 0];
        }

        return [
            'type' => $plan->type,
            'ggr'  => $plan->ggr_share,
            'rev'  => $plan->rev_share,
            'cpa'  => $plan->cpa_amount,
        ];
    }

    public function processPeriod(Carbon $from, Carbon $to): void
    {
        User::affiliates()->with('referredUsers')->chunk(500, function ($affiliates) use ($from, $to) {
            foreach ($affiliates as $aff) {
                $plan = $this->planFor($aff);
                foreach ($aff->referredUsers as $ref) {
                    $this->calcForReferral($aff, $ref, $plan, $from, $to);
                }
            }
        });
    }

    protected function calcForReferral(User $aff, User $ref, array $plan, Carbon $from, Carbon $to): void
    {
        if ($plan['type'] === 'GGR') {
            $ggr = Metrics::ggr($ref, $from, $to);
            $this->upsertLog($aff, $ref, $from, 'GGR', $ggr, $ggr * $plan['ggr']);
        } else {
            $revenue = Metrics::revenue($ref, $from, $to);
            $this->upsertLog($aff, $ref, $from, 'REV', $revenue, $revenue * $plan['rev']);
            if ($this->qualifiesCPA($ref, $plan)) {
                $this->upsertLog($aff, $ref, $from, 'CPA', 1, $plan['cpa']);
            }
        }
    }

    protected function qualifiesCPA(User $ref, array $plan): bool
    {
        if (!$plan['cpa']) {
            return false;
        }
        $ftd = $ref->deposits()->where('status', 1)->count();
        return $ftd >= ($plan['cpa_ftd_min'] ?? 1);
    }

    protected function upsertLog(User $aff, User $ref, Carbon $from, string $type, float $base, float $commission): void
    {
        $period = $from->toDateString();
        $key = "$period:{$aff->id}:{$ref->id}:{$type}";
        AffiliateCommissionLog::updateOrCreate(
            ['idempotency_key' => $key],
            [
                'affiliate_user_id' => $aff->id,
                'referred_user_id'  => $ref->id,
                'period'            => $period,
                'calc_type'         => $type,
                'base_amount'       => $base,
                'commission_amount' => $commission,
                'status'            => 'processed',
            ]
        );
    }
}
