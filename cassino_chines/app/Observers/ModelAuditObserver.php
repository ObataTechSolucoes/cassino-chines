<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class ModelAuditObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model, [], $this->safeAttributes($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();
        if (empty($dirty)) {
            return;
        }
        $before = [];
        $after = [];
        foreach ($dirty as $key => $value) {
            $before[$key] = $model->getOriginal($key);
            $after[$key] = $value;
        }
        $this->log('updated', $model, $this->safeAttributes($before), $this->safeAttributes($after));
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $this->safeAttributes($model->getOriginal()), []);
    }

    private function log(string $event, Model $model, array $before, array $after): void
    {
        try {
            $user = auth()->user();
        } catch (\Throwable $e) {
            $user = null;
        }

        $req = request();
        $routeName = $req?->route()?->getName();

        AuditLog::create([
            'user_id' => $user?->id,
            'event' => $event,
            'module' => $this->detectModule($model),
            'target_type' => get_class($model),
            'target_id' => (string) ($model->getKey() ?? ''),
            'route' => $routeName,
            'method' => $req?->method(),
            'ip' => $req?->ip(),
            'user_agent' => substr((string) $req?->userAgent(), 0, 500),
            'request' => $this->safeRequest(),
            'before' => $before,
            'after' => $after,
            'message' => $this->composeMessage($event, $model, $before, $after),
        ]);
    }

    private function safeRequest(): array
    {
        try {
            $data = request()->all();
        } catch (\Throwable $e) {
            return [];
        }
        return $this->sanitize($data);
    }

    private function safeAttributes(array $attrs): array
    {
        return $this->sanitize($attrs);
    }

    private function sanitize(array $data): array
    {
        $blocked = ['password', 'secret', 'token', 'key', 'api_key', 'private_key', 'cnpay_secret_key'];
        $clean = [];
        foreach ($data as $k => $v) {
            $lower = strtolower((string) $k);
            if (in_array($lower, $blocked) || str_contains($lower, 'password') || str_contains($lower, 'secret') || str_contains($lower, 'token')) {
                $clean[$k] = '***';
            } else {
                $clean[$k] = is_scalar($v) || $v === null ? $v : json_decode(json_encode($v), true);
            }
        }
        return $clean;
    }

    private function detectModule(Model $model): string
    {
        $class = class_basename($model);
        return match ($class) {
            'ConfigPlayFiver' => 'games.rtp_limits',
            'Game' => 'games.management',
            'SpinConfigs' => 'games.spin_config',
            'Role', 'Permission' => 'auth.roles_permissions',
            'Gateway' => 'payments.gateway',
            'GamesKey' => 'games.keys',
            'Setting', 'SettingMail' => 'system.settings',
            default => 'model.' . $class,
        };
    }

    private function composeMessage(string $event, Model $model, array $before, array $after): string
    {
        $user = auth()->user();
        $actor = $user?->name ? ($user->name . ' (#' . $user->id . ')') : 'sistema';
        $label = class_basename($model) . ' #' . ($model->getKey() ?? '');
        if ($event === 'updated' && !empty($after)) {
            $changes = implode(', ', array_map(fn ($k) => "$k", array_keys($after)));
            return "$actor atualizou $label (campos: $changes)";
        }
        return "$actor $event $label";
    }
}

