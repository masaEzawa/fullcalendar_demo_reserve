<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link href='./fullcalendar/lib/main.css' rel='stylesheet' />
    <link href='style.css' rel='stylesheet' />
    <style>
        body {
            margin: 40px 10px;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 1100px;
            margin: 0 auto;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src='./fullcalendar/lib/main.js'></script>
    <script>
        
        /**
         *  休店日の判定
         * @return true:休店日、false：休店日ではない
         */ 
        function isHoliday(date){
            // カレンダーの休日の値を指定
            var calenderDayList = {};
            var targetDate = new Date(date);

            <?php
            $holiday = [
                '1' => ['1', '2', '3', '4'],
                '2' => ['3', '4', '5', '6'],
                '3' => ['1', '2', '3', '4'],
                '4' => ['1', '2', '3', '4'],
                '5' => ['1', '2', '3', '4'],
                '6' => ['1', '2', '3', '4'],
                '7' => ['1', '2', '3', '4'],
                '9' => ['1', '2', '3', '4'],
            ];

            foreach( $holiday as $calKey => $calValue ):
                ?>
                calenderDayList['<?php echo $calKey; ?>'] = {};
                <?php foreach( $calValue as $num => $value ): ?>
                    calenderDayList['<?php echo $calKey; ?>']['<?php echo $num; ?>'] = '<?php echo $value; ?>';
                <?php endforeach;
                endforeach;
            ?>

            // 日曜日は選択不可
            if( targetDate.getDay() === 0 ){
                return true;
            }

            // 指定に休店日に判定
            var year = targetDate.getFullYear();
			var month = targetDate.getMonth() + 1;
			var day = targetDate.getDate();
			var checkDate = month + "-" + day;
			
			for( var calMonth in calenderDayList ){
				for( var calDay in calenderDayList[calMonth] ){
                    console.log(calMonth + "-" + calenderDayList[calMonth][calDay]);
					if( checkDate == calMonth + "-" + calenderDayList[calMonth][calDay] ){
						return true;
					}
				}
            }
            
            return false;
        }
        
        // カレンダーを表示
        function showCalendar() {
            var eventsList = [];
            var eventDateList1 = [
                '2021-01-01',
                '2021-01-18',
                '2021-01-19',
                '2021-01-20',
            ];
            var eventDateList2 = [
                '2021-01-21',
                '2021-01-22',
                '2021-01-23',
                '2021-01-24',
                '2021-01-25',
                '2021-01-26',
                '2021-01-27',
                '2021-01-28',
                '2021-01-29',
                '2021-01-30',
                '2021-01-31',
                '2021-02-01',
                '2021-02-02',
                '2021-02-03',
            ];
            var eventDateList3 = [
                '2021-01-15',
                '2021-01-16',
                '2021-01-17',
            ];
            var now = new Date();
            var input_date = $("#input-cal-date").val();
            if( input_date == ""){
                input_date = now.getFullYear() +'-'+ ( "0"+( now.getMonth()+1 ) ).slice(-2) +'-'+ ( "0"+now.getDate() ).slice(-2);
            }
            $.each(eventDateList1,function(index,val){
                // 休店日の時
                if( isHoliday(val) ){
                    return true;
                }
                eventsList.push(
                    {
                        title: '△',
                        start: val,
                        color: '#FF3333',
                        textColor: '#000000', // an option!
                        overlap: false,
                        className: 'cilck-ok'
                    }
                );
            });

            $.each(eventDateList2,function(index,val){
                // 休店日の時
                if( isHoliday(val) ){
                    return true;
                }

                eventsList.push(
                    {
                        title: '〇',
                        start: val,
                        color: '#87d164',
                        textColor: '#0043a8', // an option!
                        className: 'cilck-ok'
                    }
                );
            });

            $.each(eventDateList3,function(index,val){
                // 休店日の時
                if( isHoliday(val) ){
                    return true;
                }
                eventsList.push(
                    {
                        title: '✕',
                        start: val,
                        color: '#C0C0C0',
                        textColor: '#000000', // an option!
                        overlap: false,
                        className: 'cilck-ng'
                    }
                );
            });
                    
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                dayCellContent: function(e) {
                    e.dayNumberText = e.dayNumberText.replace('日', '');
                },
                timeZone: 'Asia/Tokyo',
                locale: 'ja',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                // コンテンツの高さ(px)
                contentHeight: 400,
                initialDate: input_date,
                navLinks: false, // can click day/week names to navigate views
                businessHours: false, // display business hours
                editable: true,
                // selectable: true,
                eventClick: function(calEvent, jsEvent, view) {
                    // イベントをクリックしたときの処理
                    if (!$(calEvent.el).hasClass('cilck-ng')) {
                        // イベントの日付
                        var start = new Date( calEvent.event._instance.range.start );
                        var eventDate = start.getFullYear() +'-'+ ( "0"+( start.getMonth()+1 ) ).slice(-2) +'-'+ ( "0"+start.getDate() ).slice(-2);
                        // 日付の格納
                        $("#input-cal-date").val(eventDate);
                        // モーダルウィンドウを閉じる
                        $(".modal-body").empty();
                        $("#sampleModal").modal('hide');
                    }
                },
                events: eventsList
            });
            // カレンダーの表示
            calendar.render();
        }

        $(function() {
            // $("#sampleModal").modal('hide');
            $('#input-cal-date').on('click', function() {
                // モーダルの設定を初期化
                $(".modal-body").empty();
                $(".modal-body").append('<div id="calendar"></div>');

                // モーダルを表示
                $("#sampleModal").modal('show');
                // modalを表示した後に0.2秒スリープ
                setTimeout(function(){
                    // カレンダーを表示
                    showCalendar();
                },500);
            });

        });
    </script>

</head>
<body>
    <h2>日付選択カレンダーのデモ画面</h2>
    <div>
        <p>日付選択</p>
        <input type="text" name="" id="input-cal-date">
        <p>日付入力欄をクリックするとカレンダーが表示されます。</p>
    </div>

    <!-- モーダル・ダイアログ -->
    <div class="modal" id="sampleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- 凡例を表示 -->
                    <p style="text-align: right;">
                        <span style="background-color: #87d164; color: #0043a8;">〇</span>空きあり <span style="background-color: #FF3333; color: #000000;">△</span>残り僅か <span style="background-color: #C0C0C0; color: #000000;">✕</span>空きなし
                    </p>
                </div>
                <div class="modal-body">
                    <!-- カレンダーを表示 -->
                </div>
                <!-- <div class="modal-footer">
                </div> -->
            </div>
        </div>
    </div>

</body>

</html>