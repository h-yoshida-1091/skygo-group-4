<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
</head>
<body>
    @include('layouts.header')
    <div class="container">
        <div class="punch-area">
            <form action="/attendances/clock-in" method="POST">
                @csrf
                <button type="submit" class="punch-btn">出勤</button>
            </form>
            <form action="/attendances/clock-out" method="POST">
                @csrf
                <button type="submit" class="punch-btn">退勤</button>
            </form>
        </div>

    <div class="history-wrapper" style="flex: 1;">
        <h2 class="history-title" style="text-align: center; font-size: 24px; font-weight: bold; margin-top: 0; margin-bottom: 10px;">勤怠履歴</h2>
    
        <div class="history-area">    
            @foreach($attendances as $attendance)
                <div class="date-group">
                    <div class="date-label">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y-m-d') }}</div>
                    
                    <div class="history-item" data-id="{{ $attendance->id }}" data-date="{{ $attendance->work_date }}" data-time="{{ $attendance->clock_in }}" onclick="openModal(this)">
                        <span class="badge">出勤</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</span>
                    </div>
                    
                    @if($attendance->clock_out)
                    <div class="history-item" data-id="{{ $attendance->id }}" data-date="{{ $attendance->work_date }}" data-time="{{ $attendance->clock_out }}" onclick="openModal(this)">
                        <span class="badge">退勤</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</span>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-overlay" id="editModal" onclick="closeModal(event)">
        <div class="modal-body" onclick="event.stopPropagation()"> <div class="modal-date" id="modalDate">2026年6月29日</div>
            
    <form id="editForm" action="" method="POST">
        @csrf
        <div style="margin-bottom: 10px;">
            <label>希望出勤時間：</label>
            <input type="time" name="requested_clock_in" required style="border: 2px solid black; padding: 5px;">
        </div>

        <div style="margin-bottom: 10px;">
            <label>希望退勤時間：</label>
            <input type="time" name="requested_clock_out" style="border: 2px solid black; padding: 5px;">
        </div>

        <textarea class="modal-textbox" name="reason" placeholder="修正理由を入力してください（必須）" required></textarea>
    
        <button type="submit" class="modal-submit-btn">申請する</button>
    </form>
        </div>
    </div>

    <script>
        function openModal(element) {
            const id = element.getAttribute('data-id');
            const dateStr = element.getAttribute('data-date');
    
            document.getElementById('modalDate').innerText = dateStr;
    
            // 送信先URLを /attendances/{id}/request に変更
            document.getElementById('editForm').action = `/attendances/${id}/request`;
    
            document.getElementById('editModal').classList.add('is-open');
        }

        function closeModal(event) {
            // 背景（オーバーレイ）がクリックされたら閉じる
            document.getElementById('editModal').classList.remove('is-open');
        }
    </script>
</body>
</html>
