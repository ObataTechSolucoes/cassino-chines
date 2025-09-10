<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\AffiliateHistory;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AiInsightsWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static string $view = 'filament.admin.widgets.ai-insights';

    protected static ?int $sort = 4;

    protected function getViewData(): array
    {
        [$start, $end] = $this->getRange();

        // Current period metrics
        $dep = (float) Deposit::where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->sum('amount');
        $wd = (float) Withdrawal::where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->sum('amount');
        $regs = (int) User::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))->count();
        $aff = (float) AffiliateHistory::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))->sum('commission_paid');

        // Previous period for comparison
        $insights = [];
        if ($start && $end) {
            $periodDays = max(1, Carbon::parse($start)->diffInDays($end) + 1);
            $prevStart = Carbon::parse($start)->copy()->subDays($periodDays);
            $prevEnd = Carbon::parse($start)->copy()->subDay();

            $depPrev = (float) Deposit::where('status', 1)
                ->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$prevStart, $prevEnd])
                ->sum('amount');
            $wdPrev = (float) Withdrawal::where('status', 1)
                ->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$prevStart, $prevEnd])
                ->sum('amount');
            $regsPrev = (int) User::whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $affPrev = (float) AffiliateHistory::whereBetween('created_at', [$prevStart, $prevEnd])->sum('commission_paid');

            $insights[] = $this->compare('Depósitos', $dep, $depPrev);
            $insights[] = $this->compare('Saques', $wd, $wdPrev, inverse: true);
            $insights[] = $this->compare('Registros', $regs, $regsPrev);
            $insights[] = $this->compare('Comissões de afiliados', $aff, $affPrev);
        } else {
            $insights[] = ['text' => 'Aplique um período para ver comparativos inteligentes.', 'trend' => 'neutral'];
        }

        // Extra heuristic
        if ($dep > 0) {
            $ratio = $wd / max(1, $dep);
            if ($ratio > 0.6) {
                $insights[] = ['text' => 'Relação Saque/Depósito acima de 60% — revise limites e retenção.', 'trend' => 'down'];
            } elseif ($ratio < 0.3) {
                $insights[] = ['text' => 'Ótima margem: Saque/Depósito abaixo de 30%.', 'trend' => 'up'];
            }
        }

        // Try AI enhancement if API key configured
        $ai = $this->aiInsights([
            'deposits' => $dep,
            'withdrawals' => $wd,
            'registrations' => $regs,
            'affiliates' => $aff,
        ]);

        return [
            'insights' => array_values(array_filter(array_merge($ai ?: [], $insights))),
        ];
    }

    private function compare(string $label, float|int $cur, float|int $prev, bool $inverse = false): array
    {
        if ($prev == 0) {
            return [
                'text' => sprintf('%s %s %s 100%% vs período anterior.', $label, $inverse ? 'reduziu' : 'cresceu', $inverse ? 'para' : 'em'),
                'trend' => $inverse ? 'up' : 'up',
            ];
        }
        $delta = $cur - $prev;
        $pct = round(($delta / max(1e-9, $prev)) * 100, 1);
        $isUp = $delta >= 0;
        $trend = $inverse ? (!$isUp ? 'up' : 'down') : ($isUp ? 'up' : 'down');
        $verb = $isUp ? 'cresceu' : 'caiu';
        return [
            'text' => sprintf('%s %s %s %.1f%% vs período anterior.', $label, $verb, $inverse ? 'para' : 'em', abs($pct)),
            'trend' => $trend,
        ];
    }

    private function getRange(): array
    {
        $range = $this->filters['range'] ?? null;
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        if ($range === 'custom') {
            if ($start && $end) {
                return [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];
            }
        } elseif ($range) {
            $now = Carbon::now();
            [$start, $end] = match ($range) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
                'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
                '7' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
                '30' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
                '90' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
                'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
                'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
                'all' => [null, null],
                default => [null, null],
            };
        }

        if (! $start || ! $end) {
            $start = Carbon::now()->subDays(29)->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        return [Carbon::parse($start), Carbon::parse($end)];
    }

    private function aiInsights(array $metrics): ?array
    {
        $apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        if (! $apiKey) {
            return null; // no AI configured
        }

        $model = config('services.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini'));

        try {
            $prompt = 'Gere 3 insights curtos (máx 20 palavras cada) em português para um painel financeiro de iGaming, '
                . 'com base nesses números atuais: ' . json_encode($metrics) . '. '
                . 'Responda em JSON com {"insights": ["...", "...", "..."]} sem mais texto.';

            $response = Http::withToken($apiKey)
                ->timeout(6)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um analista de dados iGaming. Seja preciso e objetivo.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.2,
                ]);

            if (! $response->successful()) {
                return null;
            }
            $text = data_get($response->json(), 'choices.0.message.content');
            $json = json_decode($text, true);
            if (! is_array($json) || empty($json['insights'])) {
                return null;
            }
            return collect($json['insights'])
                ->take(3)
                ->map(fn ($t) => ['text' => (string) $t, 'trend' => 'neutral'])
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return null; // fail silently
        }
    }
}
