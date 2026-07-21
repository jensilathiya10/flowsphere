<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FlowSphere — Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --bg: #0f1115;
            --panel: #171a21;
            --panel-2: #1e222b;
            --border: #2a2f3a;
            --text: #e9edf5;
            --muted: #8b93a3;
            --accent: #7c5cff;
            --accent-2: #37e6b3;
            --danger: #ff6b6b;
            --warn: #ffb84d;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, #1b1030 0%, var(--bg) 45%), var(--bg);
            color: var(--text);
            padding: 32px 40px 80px;
        }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .logo { font-weight: 700; font-size: 20px; letter-spacing: -0.02em; }
        .logo span { color: var(--accent); }
        .hero {
            background: linear-gradient(135deg, #2a1f4d, #171a21 70%);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
        }
        .hero h1 { margin: 0 0 6px; font-size: 26px; }
        .hero p { margin: 0; color: var(--muted); }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 18px; margin-bottom: 32px; }
        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: transform .15s ease, border-color .15s ease;
        }
        .card:hover { transform: translateY(-3px); border-color: var(--accent); }
        .card .label { color: var(--muted); font-size: 13px; margin-bottom: 8px; }
        .card .value { font-size: 28px; font-weight: 700; }
        section { margin-bottom: 36px; }
        h2 { font-size: 16px; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 16px; }
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .project-card { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; padding: 20px; }
        .project-card .name { font-weight: 600; font-size: 16px; margin-bottom: 12px; }
        .bar { height: 8px; border-radius: 6px; background: var(--panel-2); overflow: hidden; margin-bottom: 12px; }
        .bar > div { height: 100%; background: linear-gradient(90deg, var(--accent), var(--accent-2)); }
        .meta { display: flex; justify-content: space-between; color: var(--muted); font-size: 13px; }
        .health { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; margin-top: 12px; padding: 4px 10px; border-radius: 999px; background: var(--panel-2); }
        .dot { width: 8px; height: 8px; border-radius: 50%; }
        .healthy { color: var(--accent-2); } .healthy .dot { background: var(--accent-2); }
        .risk { color: var(--warn); } .risk .dot { background: var(--warn); }
        .critical { color: var(--danger); } .critical .dot { background: var(--danger); }
        .workload-row { display: flex; align-items: center; gap: 16px; margin-bottom: 14px; }
        .workload-row .name { width: 110px; color: var(--text); font-size: 14px; }
        .workload-row .bar { flex: 1; margin-bottom: 0; }
        .workload-row .pct { width: 48px; text-align: right; color: var(--muted); font-size: 13px; }
        .recs { color: var(--warn); font-size: 13px; margin-top: 12px; }
        .ai-panel { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; padding: 24px; }
        .ai-panel form { display: flex; gap: 12px; margin-bottom: 20px; }
        .ai-panel select, .ai-panel input[type=text] {
            background: var(--panel-2); border: 1px solid var(--border); color: var(--text);
            border-radius: 10px; padding: 10px 14px; font-size: 14px;
        }
        .ai-panel input[type=text] { flex: 1; }
        .ai-panel button {
            background: var(--accent); color: white; border: none; border-radius: 10px;
            padding: 10px 18px; font-size: 14px; cursor: pointer;
        }
        .ai-answer { background: var(--panel-2); border-radius: 12px; padding: 16px 18px; }
        .ai-answer .q { color: var(--muted); font-size: 13px; margin-bottom: 8px; }
        .ai-answer .a { font-size: 15px; margin-bottom: 10px; }
        .ai-answer ul { margin: 0; padding-left: 18px; color: var(--accent-2); font-size: 14px; }
        .empty { color: var(--muted); text-align: center; padding: 80px 20px; }
        code { background: var(--panel-2); padding: 2px 8px; border-radius: 6px; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="logo">Flow<span>Sphere</span></div>
    @if($seeded ?? false)
        <div style="color: var(--muted); font-size: 14px;">{{ $workspace->name }}</div>
    @endif
</div>

@if(!($seeded ?? false))
    <div class="empty">
        <p>No demo data found yet.</p>
        <p>Seed a demo user/workspace/project (see README) then reload this page.</p>
    </div>
@else

    <div class="hero">
        <h1>Good morning, {{ $user->name }} 🚀</h1>
        <p>{{ $workspace->name }} — {{ $summary['active_projects'] }} active project(s), {{ $summary['members'] }} member(s).</p>
    </div>

    <div class="cards">
        <div class="card"><div class="label">Projects</div><div class="value">{{ $summary['active_projects'] }}</div></div>
        <div class="card"><div class="label">Tasks Completed</div><div class="value">{{ $summary['completed_tasks'] }}</div></div>
        <div class="card"><div class="label">Team</div><div class="value">{{ $summary['members'] }}</div></div>
        <div class="card"><div class="label">Deadlines This Week</div><div class="value">{{ $summary['deadlines_this_week'] }}</div></div>
    </div>

    <section>
        <h2>Projects</h2>
        <div class="project-grid">
            @foreach($projects as $p)
                @php
                    $label = $p['health_score'] >= 80 ? 'healthy' : ($p['health_score'] >= 50 ? 'risk' : 'critical');
                    $labelText = $p['health_score'] >= 80 ? 'Healthy' : ($p['health_score'] >= 50 ? 'At Risk' : 'Critical');
                @endphp
                <div class="project-card">
                    <div class="name">{{ $p['name'] }}</div>
                    <div class="bar"><div style="width: {{ $p['progress'] }}%"></div></div>
                    <div class="meta">
                        <span>👥 {{ $p['members'] }} member(s)</span>
                        <span>📅 {{ $p['days_remaining'] !== null ? $p['days_remaining'].' days left' : 'no deadline' }}</span>
                    </div>
                    <div class="health {{ $label }}"><span class="dot"></span> {{ $p['health_score'] }}% {{ $labelText }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h2>Team Workload</h2>
        <div class="project-card">
            @foreach($workload as $w)
                <div class="workload-row">
                    <div class="name">{{ $w['name'] }}</div>
                    <div class="bar"><div style="width: {{ min(100, $w['utilization']) }}%; {{ $w['utilization'] > 90 ? 'background: var(--danger)' : '' }}"></div></div>
                    <div class="pct">{{ $w['utilization'] }}%</div>
                </div>
            @endforeach
            @if(count($recommendations))
                <div class="recs">
                    @foreach($recommendations as $r)
                        <div>⚠ {{ $r }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section>
        <h2>🤖 Flow AI</h2>
        <div class="ai-panel">
            <form method="POST" action="{{ route('demo.ask-flow-ai') }}">
                @csrf
                <select name="project_id">
                    @foreach($projects as $p)
                        <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                    @endforeach
                </select>
                <input type="text" name="question" placeholder="Why is this project delayed?" required>
                <button type="submit">Ask</button>
            </form>

            @if($flowAiAnswer)
                <div class="ai-answer">
                    <div class="q">"{{ $flowAiAnswer->question }}"</div>
                    <div class="a">{{ $flowAiAnswer->answer }}</div>
                    @if(count($flowAiAnswer->recommendations ?? []))
                        <ul>
                            @foreach($flowAiAnswer->recommendations as $r)
                                <li>{{ $r }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </section>

@endif

</body>
</html>
