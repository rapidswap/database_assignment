<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
date_default_timezone_set('Asia/Seoul');

$serverName = "localhost";
$connectionOptions = array(
    "database" => "Final_Assignment", // 데이터베이스명
    "uid" => "Admin",   // 유저 아이디
    "pwd" => "xxxx",    // 유저 비번
    "CharacterSet" => "UTF-8"
);

// DB커넥션 연결
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn ===false){
    echo "연결실패";
    
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 선택된 스터디 룸 정보를 가져옵니다.
    $selectedStudyroomres = $_POST['selectedStudyroomres'] ?? [];
    if (!empty($selectedStudyroomres) && isset($_POST['changebutton'])) {
        // 스터디 룸 정보를 변경합니다.
        foreach ($selectedStudyroomres as $reservationNum) {
            $selectQuery = "SELECT [스터디 룸ID] FROM [스터디 룸 예약 정보] WHERE [예약 번호] = {$reservationNum}";
            $selectResult = sqlsrv_query($conn, $selectQuery);

            if ($selectResult === false) {
                echo "스터디 룸 아이디 조회 실패";
                die(print_r(sqlsrv_errors(), true));
            }

            $row = sqlsrv_fetch_array($selectResult, SQLSRV_FETCH_ASSOC);

            if ($row === false) {
                echo "예약에 해당하는 스터디 룸 아이디를 찾을 수 없습니다.";
                die();
            }

            $selectQuery = "SELECT [회원ID] FROM [스터디 룸 예약 정보] WHERE [예약 번호] = {$reservationNum}";
            $selectResult = sqlsrv_query($conn, $selectQuery);

            if ($selectResult === false) {
                echo "회원 아이디 조회 실패";
                die(print_r(sqlsrv_errors(), true));
            }

            $row2 = sqlsrv_fetch_array($selectResult, SQLSRV_FETCH_ASSOC);

            $studyroomid = strval($row['스터디 룸ID']);
            $starttime = $_POST["reservationStart_{$reservationNum}"] ?? null;
            $endtime = $_POST["reservationEnd_{$reservationNum}"] ?? null;
            $reservationDate = $_POST["reservationDay_{$reservationNum}"] ?? null;
            $memberid = strval($row2['회원ID']);
            
  
            // 수정 가능한 항목만 업데이트합니다.
            if ($starttime !== null) {
                // 시작 업데이트
                $query = "UPDATE [스터디 룸 예약 정보] SET [예약 시작 시간] = '{$starttime}' WHERE [예약 번호] = {$reservationNum}";
                $stmt = sqlsrv_query($conn, $query);
        
                    if ($stmt === false) {
                        echo "예약 시작 시간 업데이트 실패";
                        die(print_r(sqlsrv_errors(), true));
                    }
            }
            

            if ($endtime !== null) {
                // 종료시간 업데이트
                $query = "UPDATE [스터디 룸 예약 정보] SET [예약 종료 시간] = '{$endtime}' WHERE [예약 번호] = {$reservationNum}";
                $stmt = sqlsrv_query($conn, $query);
                    if ($stmt === false) {
                        echo "예약 종료 시간 업데이트 실패";
                        die(print_r(sqlsrv_errors(), true));
                    }
            }
                
            $originalReservationDate = $_POST["originalReservationDay_{$reservationNum}"] ?? null;

            if ($reservationDate !== null && $reservationDate !== $originalReservationDate) {
                // 예약 일자가 변경되었을 때만 실행
                // 예약 일자 업데이트 전에 해당 날짜에 예약이 있는지 확인
                $query_existing = "SELECT * FROM [스터디 룸 예약 정보] WHERE [스터디 룸ID] = ? AND [예약 일자] = ?";
                $params_existing = array($studyroomid, $reservationDate);
                $stmt_existing = sqlsrv_query($conn, $query_existing, $params_existing);
            
                if ($stmt_existing === false) {
                    echo "예약 정보 조회 실패";
                    die(print_r(sqlsrv_errors(), true));
                }
            
                $isExistingReservation = false;
            
                while ($row_existing = sqlsrv_fetch_array($stmt_existing, SQLSRV_FETCH_ASSOC)) {
                    $existingMemberID = strval($row_existing['회원ID']);
                    $existingMemberNum = $row_existing['예약 번호'];
            
                    // 기존 예약 회원과 변경하려는 회원이 다른 경우 예약 허용하지 않음
                    if ($existingMemberID !== $memberid || $existingMemberNum !== $reservationNum ) {
                        $isExistingReservation = true;
                        break;
                    }
                }
            
                if ($isExistingReservation) {
                    echo "해당 날짜에 이미 예약이 있습니다. 다른 날짜를 선택해주세요. 3초후 홈으로 이동합니다.";
                    echo "<div id='countdown'>3</div>";
                    echo "<script>";
                    echo "var count = 3;";
                    echo "var countdownElement = document.getElementById('countdown');";
                    echo "var countdownInterval = setInterval(function() {";
                    echo "  countdownElement.innerHTML = count;";
                    echo "  count--;";
                    echo "  if (count < 0) {";
                    echo "    clearInterval(countdownInterval);";
                    echo "    window.location.href = 'mssqltest.php';";
                    echo "  }";
                    echo "}, 1000);"; // 1초(1000밀리초)마다 카운트 다운
                    echo "</script>";
                    die();
                }
            
                $reservationDate = date_format(date_create($reservationDate), 'Y-m-d'); // datetime2 형식으로 변환
                $query = "UPDATE [스터디 룸 예약 정보] SET [예약 일자] = CONVERT(datetime2, '{$reservationDate}', 23) WHERE [예약 번호] = {$reservationNum}";
                $stmt = sqlsrv_query($conn, $query);
            
                if ($stmt === false) {
                    echo "예약 일자 업데이트 실패";
                    die(print_r(sqlsrv_errors(), true));
                }
            }
            else{

                $reservationDate = date_format(date_create($reservationDate), 'Y-m-d'); // datetime2 형식으로 변환
                $query = "UPDATE [스터디 룸 예약 정보] SET [예약 일자] = CONVERT(datetime2, '{$reservationDate}', 23) WHERE [예약 번호] = {$reservationNum}";
                $stmt = sqlsrv_query($conn, $query);
            
                if ($stmt === false) {
                    echo "예약 일자 업데이트 실패";
                    die(print_r(sqlsrv_errors(), true));
                }
            }
            
            echo "예약 정보 업데이트 성공<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';
            
        
        }
    }



    if (!empty($selectedStudyroomres) && isset($_POST['deletebutton']))
    {
        $selectedStudyroomres = $_POST['selectedStudyroomres'];
  
        // 선택된 대출 번호를 이용하여 삭제합니다.
        foreach ($selectedStudyroomres as $reservationNum) {
            
            $selectQuery = "SELECT [스터디 룸ID] FROM [스터디 룸 예약 정보] WHERE [예약 번호] = {$reservationNum}";
            $selectResult = sqlsrv_query($conn, $selectQuery);

            if ($selectResult === false) {
                echo "스터디 룸 아이디 조회 실패";
                die(print_r(sqlsrv_errors(), true));
            }

            $row = sqlsrv_fetch_array($selectResult, SQLSRV_FETCH_ASSOC);
            $studyroomid = strval($row['스터디 룸ID']);

            

            $deleteQuery = "DELETE FROM [스터디 룸 예약 정보] WHERE [예약 번호] = {$reservationNum}";
            $deleteResult = sqlsrv_query($conn, $deleteQuery);
      
            if ($deleteResult === false) {
                echo "스터디 룸 예약 정보 삭제 실패";
                die(print_r(sqlsrv_errors(), true));
            }
            else{

                $ifQuery = "SELECT COUNT(*) FROM [스터디 룸 예약 정보] WHERE [스터디 룸ID] = '{$studyroomid}'";
                $ifResult = sqlsrv_query($conn, $ifQuery);
                    
                if ($ifResult === false) {
                    echo "스터디 룸 예약 정보 조회 실패";
                    die(print_r(sqlsrv_errors(), true));
                }
                    
                $row = sqlsrv_fetch_array($ifResult, SQLSRV_FETCH_NUMERIC);
                if (!empty($row) && $row[0] > 0) {
                    // 스터디 룸 예약 정보가 있을 때는 업데이트 수행하지 않음
                    echo "스터디 룸 예약 정보가 이미 존재합니다.";
                } else {
                    // 스터디 룸 예약 정보가 없을 때 업데이트 수행
                    $updateQuery = "UPDATE [스터디 룸] SET [예약 상태] = '없음' WHERE [스터디 룸ID] = '{$studyroomid}'";
                    $updateResult = sqlsrv_query($conn, $updateQuery);
                    
                    if ($updateResult === false) {
                        echo "스터디 룸 예약 상태 업데이트 실패";
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            }
            echo "예약 번호 {$reservationNum} 삭제 완료<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';
        }
    }
}
    
?>


<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>