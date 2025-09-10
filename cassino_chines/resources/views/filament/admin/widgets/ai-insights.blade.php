<div class="ai-insights-card">
    <div class="ai-insights-header">
        <span class="ai-badge">Insights</span>
        <h3>Recomendações Inteligentes</h3>
    </div>
    <ul class="ai-insights-list">
        @forelse(($insights ?? []) as $item)
            <li class="ai-{{ $item['trend'] ?? 'neutral' }}">
                <span class="dot"></span>
                <span class="text">{{ $item['text'] }}</span>
            </li>
        @empty
            <li class="ai-neutral">
                <span class="dot"></span>
                <span class="text">Nenhum insight disponível para o período selecionado.</span>
            </li>
        @endforelse
    </ul>
</div>

<style>
.ai-insights-card {
  background: linear-gradient(180deg, rgba(18,26,49,1) 0%, rgba(14,21,39,1) 100%);
  border: 1px solid #1f2a44;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(2, 8, 23, 0.35);
  padding: 1.1rem 1.2rem;
}
.ai-insights-header {
  display: flex; align-items: center; gap: .6rem; margin-bottom: .6rem;
}
.ai-insights-header h3 { margin: 0; font-weight: 600; color: #e5e7eb; font-size: 1.05rem; }
.ai-badge {
  font-size: .7rem; text-transform: uppercase; letter-spacing: .06em;
  background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.35);
  color: #c7d2fe; padding: .2rem .4rem; border-radius: .4rem;
}
.ai-insights-list { list-style: none; margin: 0; padding: 0; display: grid; gap: .5rem; }
.ai-insights-list li { display: flex; gap: .5rem; align-items: center; color: #cbd5e1; }
.ai-insights-list .dot { width: .5rem; height: .5rem; border-radius: 9999px; background: #94a3b8; }
.ai-up .dot { background: #22c55e; }
.ai-down .dot { background: #ef4444; }
.ai-neutral .dot { background: #94a3b8; }
.ai-up .text { color: #bbf7d0; }
.ai-down .text { color: #fecaca; }
.ai-neutral .text { color: #cbd5e1; }
</style>

