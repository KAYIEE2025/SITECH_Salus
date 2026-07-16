<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        .container {
            max-width: {{ ($orientation ?? 'landscape') === 'portrait' ? '210mm' : '297mm' }};
            margin: 0 auto;
            background: white;
            padding: 15mm 12mm;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 64px;
            height: 64px;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .header-content {
            flex: 1;
        }
        
        .header h1 {
            color: #000;
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header h2 {
            color: #000;
            font-size: 13px;
            font-weight: normal;
            margin: 3px 0 3px 0;
        }
        
        .header h3 {
            color: #000;
            font-size: 15px;
            font-weight: bold;
            margin: 5px 0 0 0;
            text-transform: uppercase;
        }
        
        .report-info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .report-info-row {
            margin: 5px 0;
        }
        
        .report-info-label {
            font-weight: bold;
        }
        
        .filters-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        
        .filters-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            font-size: 10px;
        }
        
        .filter-item {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .filter-value {
            margin-left: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: auto;
        }
        
        thead {
            border: 1px solid #000;
        }
        
        th {
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #000;
            text-transform: uppercase;
            background-color: #f0f0f0;
        }
        
        td {
            padding: 6px 5px;
            border: 1px solid #000;
            vertical-align: middle;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .no-records {
            text-align: center;
            padding: 40px 20px;
            color: #000;
            font-style: italic;
            font-size: 12px;
        }
        
        .summary-section {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 10px;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-value {
            font-weight: bold;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 12mm;
            border-top: 2px solid #000;
            background-color: white;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
        }
        
        .footer-left {
            text-align: left;
            width: 50%;
        }
        
        .footer-right {
            text-align: right;
            width: 50%;
        }
        
        .footer-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer-line {
            border-bottom: 1px solid #000;
            margin-top: 5px;
            width: 200px;
        }
        
        .footer-title {
            font-weight: bold;
            margin-top: 5px;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .container {
                margin: 0;
                padding: 15mm 12mm;
                box-shadow: none;
                max-width: none;
            }
            
            @page {
                margin: 15mm 12mm;
                size: A4 {{ $orientation ?? 'landscape' }};
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
            
            tr {
                page-break-inside: avoid;
            }
            
            table {
                page-break-inside: auto;
            }
            
            .summary-section {
                page-break-inside: avoid;
            }
            
            .filters-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @yield('content')

    @if(isset($showFooter) || !isset($showFooter))
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-label">Prepared by:</div>
                <div class="footer-line"></div>
                <div class="footer-title">{{ $currentUser->name ?? 'System' }}</div>
            </div>
            <div class="footer-right">
                <div>Page <span class="page-number">1</span> of <span class="total-pages">1</span></div>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Calculate page numbers for print
        window.onbeforeprint = function() {
            const totalPages = Math.ceil(document.body.scrollHeight / 794); // Approximate A4 landscape height in pixels
            document.querySelectorAll('.total-pages').forEach(el => {
                el.textContent = totalPages;
            });
            document.querySelectorAll('.page-number').forEach((el, index) => {
                el.textContent = index + 1;
            });
        };
    </script>
</body>
</html>
