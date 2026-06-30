<header style="display: table; width: 100%; border-bottom: 2px solid #1a5c1a; padding-bottom: 12px; margin-bottom: 18px;">
    <div style="display: table-cell; width: 72px; vertical-align: middle;">
        <img src="{{ public_path('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" style="width: 64px; height: 64px;">
    </div>
    <div style="display: table-cell; vertical-align: middle;">
        <div style="font-size: 18px; font-weight: 700; color: #1a5c1a;">Salus Institute of Technology</div>
        <div style="font-size: 12px; color: #4b5563;">SITech Student Information System</div>
        @isset($reportTitle)
            <div style="font-size: 14px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $reportTitle }}</div>
        @endisset
    </div>
</header>
