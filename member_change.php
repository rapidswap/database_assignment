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

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // 선택된 회원 정보를 가져옵니다.
    $selectedMembers = $_POST['selectedMembers'] ?? [];

    if (!empty($selectedMembers) && isset($_POST['changebutton'])) {
        // 회원 정보를 변경합니다.
        foreach ($selectedMembers as $memberid) {
            $memberName = $_POST["name_{$memberid}"] ?? null;
            $birthDate = $_POST["birthDate_{$memberid}"] ?? null;
            $contact = $_POST["contact_{$memberid}"] ?? null;
            $address = $_POST["address_{$memberid}"] ?? null;
            $joinDate = $_POST["joinDate_{$memberid}"] ?? null;
  
            // 수정 가능한 항목만 업데이트합니다.
            if ($memberName !== null) {
            // 이름 업데이트
                $query = "UPDATE 회원 SET 이름 = '{$memberName}' WHERE 회원ID = {$memberid}";
                $stmt = sqlsrv_query($conn, $query);
  
                if ($stmt === false) {
                    echo "회원 이름 업데이트 실패<br>";
                    
                    die(print_r(sqlsrv_errors(), true));
                }
            }
  
            if ($birthDate !== null) {
                // 대출 기간 업데이트
                $birthDate = date_format(date_create($birthDate), 'Y-m-d'); // datetime2 형식으로 변환
                $query = "UPDATE 회원 SET 생년월일 = CONVERT(datetime2, '{$birthDate}', 23) WHERE 회원ID = {$memberid}";
                $stmt = sqlsrv_query($conn, $query);
            
                if ($stmt === false) {
                    echo "회원 생년월일 업데이트 실패<br>";
                    
                    die(print_r(sqlsrv_errors(), true));
                }
            }

            if ($contact !== null) {
                // 연락처 업데이트
                $query = "UPDATE 회원 SET 연락처 = '{$contact}' WHERE 회원ID = {$memberid}";
                $stmt = sqlsrv_query($conn, $query);
                
  
                if ($stmt === false) {
                    echo "회원 연락처 업데이트 실패<br>";
                    
                    die(print_r(sqlsrv_errors(), true));
                    if ($stmt === false) {
                        echo "회원 연락처 업데이트 실패<br>";
                        
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            }

            if ($address !== null) {
                // 주소 업데이트
                $query = "UPDATE 회원 SET 주소 = '{$address}' WHERE 회원ID = {$memberid}";
                $stmt = sqlsrv_query($conn, $query);
  
                if ($stmt === false) {
                    echo "회원 주소 업데이트 실패<br>";
                    
                    die(print_r(sqlsrv_errors(), true));
                }
            }
            if ($joinDate !== null) {
                // 가입일 업데이트
                $query = "UPDATE 회원 SET 가입일 = '{$joinDate}' WHERE 회원ID = {$memberid}";
                $stmt = sqlsrv_query($conn, $query);
  
                if ($stmt === false) {
                    echo "회원 가입일 업데이트 실패<br>";
                    
                    die(print_r(sqlsrv_errors(), true));
                }
            }
            echo "회원의 정보가 성공적으로 업데이트 되었습니다.<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';

        }
    }

    if (!empty($selectedMembers) && isset($_POST['deletebutton']))
    {
        $selectedLoans = $_POST['selectedMembers'];
  
        // 선택된 대출 번호를 이용하여 삭제합니다.
        foreach ($selectedMembers as $memberid) {
            $deleteQuery = "DELETE FROM 회원 WHERE 회원ID = {$memberid}";
            $deleteResult = sqlsrv_query($conn, $deleteQuery);
  
            if ($deleteResult === false) {
                echo "회원 정보 삭제 실패<br>";
                die(print_r(sqlsrv_errors(), true));
            }
  
            echo "회원 번호 {$memberid} 삭제 완료<br>";
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