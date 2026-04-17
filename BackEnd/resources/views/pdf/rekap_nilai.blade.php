<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    {{-- Sekarang $class->program_name tidak akan error lagi --}}
    <title>Score Report - {{ $class->program_name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #990000; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #990000; text-transform: uppercase; }
        .subtitle { font-size: 14px; margin-top: 5px; font-weight: bold; }

        .tryout-group { margin-top: 25px; }
        .tryout-header { background-color: #333; color: white; padding: 8px 15px; font-weight: bold; text-transform: uppercase; border-radius: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        td { border: 1px solid #ddd; padding: 10px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Student Score Report</div>
        <div class="subtitle">{{ $class->program_name }}</div>
        <div style="font-size: 9px; color: #666; margin-top: 5px;">Generated on: {{ date('d F Y, H:i') }} WIB</div>
    </div>

    {{-- Looping berdasarkan hasil grouping di Controller --}}
    @foreach($results as $tryoutTitle => $items)
        <div class="tryout-group">
            <div class="tryout-header">Tryout: {{ $tryoutTitle }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="50%">Student Name</th>
                        <th width="25%" class="text-center">Final Score</th>
                        <th width="25%" class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $res)
                    <tr>
                        <td class="font-bold">{{ strtoupper($res->user->name) }}</td>
                        <td class="text-center font-bold" style="color: #990000;">{{ $res->score }}</td>
                        <td class="text-center">{{ $res->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div style="margin-top: 50px; text-align: right; font-size: 10px; italic">
        <p>Spekta Academy Digital Report System</p>
    </div>

</body>
</html>
