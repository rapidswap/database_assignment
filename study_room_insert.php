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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reservationNum"])) {
    // 폼에서 전송된 데이터 가져오기
    $reservationnum = $_POST["reservationNum"];
    $memberid = $_POST["memberid"];
    $studyroomid = $_POST["Study_RoomID"];
    $reservationstart = $_POST["reservationStart"];
    $reservationend = $_POST["reservationEnd"];
    $reservationday = $_POST["reservationDay"];

    // 데이터 유효성 검사 등 필요한 검증 과정 추가 가능
    // 회원 ID를 사용하여 실제 회원 정보를 조회
    $query_member = "SELECT * FROM [회원] WHERE 회원ID = ?";
    $params_member = array($memberid);
    $stmt_member = sqlsrv_prepare($conn, $query_member, $params_member);
    $result_member = sqlsrv_execute($stmt_member);

    if ($result_member === false) {
        echo "회원 정보 조회 실패";
        die(print_r(sqlsrv_errors(), true));
    }

    // IDENTITY_INSERT 설정을 사용하여 INSERT 문 실행 준비
    if (sqlsrv_has_rows($stmt_member)) {
        // IDENTITY_INSERT 설정을 ON으로 변경
        $query_identity = "SET IDENTITY_INSERT [스터디 룸 예약 정보] ON";
        $stmt_identity = sqlsrv_query($conn, $query_identity);

        if ($stmt_identity === false) {
            echo "IDENTITY_INSERT 설정 실패";
            die(print_r(sqlsrv_errors(), true));
        }

        // 예약 가능한지 확인하는 로직
        $query = "SELECT * FROM [스터디 룸 예약 정보] WHERE [스터디 룸ID] = ? AND [예약 일자] = ?";
        $params = array($studyroomid, $reservationday);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            echo "예약 정보 조회 실패";
            die(print_r(sqlsrv_errors(), true));
        }

        $isExistingReservation = false;

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $existingReservationDay = $row['예약 일자'];
            $existingReservationDay = $existingReservationDay->format('Y-m-d'); // 예약일자를 DateTime 객체로 변환하여 비교
        
            if ($existingReservationDay == $reservationday) {
                // 같은 예약일자가 이미 존재하는 경우
                $isExistingReservation = true;
                break;
            }
        }

        if ($isExistingReservation) {
            echo "해당 날짜에 이미 예약이 있습니다.";
            die();
        }
        else {
            // INSERT 문 실행
            $query = "INSERT INTO [스터디 룸 예약 정보] ([예약 번호], 회원ID, [스터디 룸ID], [예약 시작 시간], [예약 종료 시간], [예약 일자]) VALUES (?, ?, ?, ?, ?, ?)";
            $params = array($reservationnum, $memberid, $studyroomid, $reservationstart, $reservationend, $reservationday);

            $stmt = sqlsrv_prepare($conn, $query, $params);

            if ($stmt === false) {
                echo "실패";
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_execute($stmt) === false) {
                echo "스터디 룸 예약 정보 등록 실패";
                die(print_r(sqlsrv_errors(), true));
            } else {
                $query_update = "UPDATE [스터디 룸] SET [예약 상태] = '예약' WHERE [스터디 룸ID] = ?";
                $params_update = array($studyroomid);
                $stmt_update = sqlsrv_prepare($conn, $query_update, $params_update);

                if ($stmt_update === false) {
                    echo "스터디 룸 상태 업데이트 실패";
                    die(print_r(sqlsrv_errors(), true));
                }

                if (sqlsrv_execute($stmt_update) === false) {
                    echo "스터디 룸 상태 업데이트 실패";
                    die(print_r(sqlsrv_errors(), true));
                } else {
                    echo "스터디 룸 예약 정보가 등록되었으며, 스터디 룸의 대한 예약 상태를 예약으로 변경하였습니다.<br>";
                    echo '<button onclick="goToHomePage()">돌아가기</button>';
                }
            }
        }
    }

    // IDENTITY_INSERT 설정 해제
    $query = "SET IDENTITY_INSERT [스터디 룸 예약 정보] OFF";
    $stmt = sqlsrv_prepare($conn, $query);

    if ($stmt === false) {
        echo "IDENTITY_INSERT 설정 해제 실패";
        die(print_r(sqlsrv_errors(), true));
    }
}
?>

<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>