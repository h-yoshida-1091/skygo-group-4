
<div class="container mx-auto p-4">
   {{-- サマリーエリアの開始 --}}
   {{-- コントローラーから渡された$summary配列の中身をループで展開し、カードとして表示する箇所 --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
       @foreach([
           ['合計勤務時間', $summary['total_work']],
           ['規定労働時間', $summary['prescribed_work']],
           ['勤務日数', $summary['work_days']],
           ['休憩時間', $summary['total_break']],
           ['遅刻早退時間', $summary['late_early']],
           ['欠勤日数', $summary['absent_days']]
       ] as $item)
           {{-- 各項目のラベル(item[0])と数値(item[1])を個別の枠組みに表示 --}}
<div class="border rounded p-4 text-center bg-white shadow-sm">
<div class="text-xs text-gray-500">{{ $item[0] }}</div>
<div class="text-lg font-bold">{{ $item[1] }}</div>
</div>
       @endforeach
</div>
   {{-- タブ切り替えエリアの開始 --}}
   {{-- 現在のタブ状態($tab)を判定して、選択中のタブに青い下線を引く条件分岐を行っている --}}
<div class="flex border-b mb-4">
<a href="?tab=main&month={{ $currentMonth }}" class="px-6 py-2 {{ $tab == 'main' ? 'border-b-2 border-blue-500 font-bold' : 'text-gray-500' }}">主勤務情報</a>
<a href="?tab=clock&month={{ $currentMonth }}" class="px-6 py-2 {{ $tab == 'clock' ? 'border-b-2 border-blue-500 font-bold' : 'text-gray-500' }}">打刻情報</a>
</div>
   {{-- 月移動ボタンエリアの開始 --}}
   {{-- URLパラメータのmonthを切り替えることで、コントローラー側で該当月のデータを再取得させるリンク --}}
<div class="flex justify-center items-center mb-4">
<a href="?tab={{ $tab }}&month={{ $prevMonth }}" class="px-4 py-2 bg-gray-200 rounded">◀ 先月</a>
<span class="mx-6 text-xl font-bold">{{ \Carbon\Carbon::parse($currentMonth)->format('Y年n月') }}</span>
<a href="?tab={{ $tab }}&month={{ $nextMonth }}" class="px-4 py-2 bg-gray-200 rounded">次月 ▶</a>
</div>
   {{-- テーブル表示エリアの開始 --}}
<div class="overflow-x-auto">
<table class="w-full border-collapse border border-gray-300 bg-white">
<thead class="bg-gray-50">
<tr>
<th class="border p-2">日付</th>
<th class="border p-2">種別</th>
<th class="border p-2">出勤</th>
<th class="border p-2">退勤</th>
<th class="border p-2">休憩</th>
<th class="border p-2">遅刻早退</th>
<th class="border p-2">総勤務</th>
</tr>
</thead>
<tbody>
{{-- コントローラーから渡された$workScheduleコレクションをループし、1日ずつ行を出力 --}}
@foreach($workSchedule as $row)
<tr>
<td class="border p-2 text-center">{{ $row->date->format('n/j') }}</td>
<td class="border p-2 text-center">{{ $row->type }}</td>
<td class="border p-2 text-center">{{ $row->start_time }}</td>
<td class="border p-2 text-center">{{ $row->end_time }}</td>
<td class="border p-2 text-center">{{ $row->break_time }}</td>
<td class="border p-2 text-center">{{ $row->late_early_time }}</td>
<td class="border p-2 text-center font-bold">{{ $row->total_work_time }}</td>
</tr>
               @endforeach
</tbody>
</table>
</div>
</div>
