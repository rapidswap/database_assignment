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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    // 폼에서 전송된 데이터 가져오기
    $name = $_POST["name"];
    $birthDate = $_POST["birthDate"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];
    $joinDate = $_POST["joinDate"];

    // 데이터 유효성 검사 등 필요한 검증 과정 추가 가능

    // 쿼리 작성
    $query = "INSERT INTO 회원 (이름, 생년월일, 연락처, 주소, 가입일) VALUES (?, ?, ?, ?, ?)";
    $params = array($name, $birthDate, $contact, $address, $joinDate);

    $currentEncoding = mb_detect_encoding($query, mb_detect_order(), true);
    $query = mb_convert_encoding($query, "UTF-8", $currentEncoding);
    // 쿼리 실행 실행시 오류가 나와서 쿼리 추가.
    $stmt = sqlsrv_prepare($conn, $query, $params);

    if(empty($name))
    {
        echo"회원 이름을 입력하시오<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
        die();
    }

    if ($stmt === false) {
        echo "실패<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
        die(print_r(sqlsrv_errors(), true));

    }

    if (sqlsrv_execute($stmt) === false) {
        echo "실패<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "회원이 성공적으로 등록되었습니다.<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
    }

    // 연결 종료
    sqlsrv_close($conn);
}

?>

<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>