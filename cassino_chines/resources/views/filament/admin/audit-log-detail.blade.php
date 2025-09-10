<div class="space-y-3 text-sm">
    <div class="grid grid-cols-2 gap-2">
        <div>
            <div class="text-slate-400">Data</div>
            <div>{{ $record->created_at }}</div>
        </div>
        <div>
            <div class="text-slate-400">Usuário</div>
            <div>{{ optional($record->user)->name ?? 'sistema' }} (ID: {{ $record->user_id ?? '—' }})</div>
        </div>
        <div>
            <div class="text-slate-400">Evento</div>
            <div>{{ $record->event }}</div>
        </div>
        <div>
            <div class="text-slate-400">Módulo</div>
            <div>{{ $record->module }}</div>
        </div>
        <div>
            <div class="text-slate-400">Rota</div>
            <div>{{ $record->route ?? '—' }} ({{ $record->method ?? '—' }})</div>
        </div>
        <div>
            <div class="text-slate-400">IP</div>
            <div>{{ $record->ip ?? '—' }}</div>
        </div>
    </div>

    <div>
        <div class="text-slate-400">Mensagem</div>
        <div class="whitespace-pre-line">{{ $record->message }}</div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="font-semibold text-slate-300 mb-1">Antes</div>
            <pre class="bg-slate-900/40 border border-slate-700/50 rounded p-2 overflow-auto max-h-72">{{ json_encode($record->before ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        <div>
            <div class="font-semibold text-slate-300 mb-1">Depois</div>
            <pre class="bg-slate-900/40 border border-slate-700/50 rounded p-2 overflow-auto max-h-72">{{ json_encode($record->after ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <div>
        <div class="font-semibold text-slate-300 mb-1">Request</div>
        <pre class="bg-slate-900/40 border border-slate-700/50 rounded p-2 overflow-auto max-h-72">{{ json_encode($record->request ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>

