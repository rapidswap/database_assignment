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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["bookID"])) {
    // 폼에서 전송된 데이터 가져오기
    $id = $_POST["bookID"];
    $name = $_POST["bookName"];
    $writer = $_POST["writer"];
    $publisher = $_POST["publisher"];
    $publishing_day = $_POST["publishing_day"];
    $genre = $_POST["genre"];
  
    // 데이터 유효성 검사 등 필요한 검증 과정 추가 가능
  
    // 쿼리 작성
    $query = "INSERT INTO 도서 (도서ID, 도서제목, 작가, 출판사, [출판 일자], 장르) VALUES (?, ?, ?, ?, ?, ?)";
    $params = array($id, $name, $writer, $publisher, $publishing_day, $genre);
  
    // 쿼리 실행
    $stmt = sqlsrv_prepare($conn, $query, $params);
  
    if ($stmt === false) {
        echo "실패<br>";
        
        die(print_r(sqlsrv_errors(), true));
  
    }
  
    if (sqlsrv_execute($stmt) === false) {
        echo "실패<br>";
        
        die(print_r(sqlsrv_errors(), true));
    } 

    else 
    {
        if($genre === '역사' || $genre === '소설')
        {
            $query = "INSERT INTO [도서 위치] (도서ID, 층수, 구역) VALUES (?, '2층', ?)";
            $params = array($id, $genre);
            $stmt = sqlsrv_prepare($conn, $query, $params);
            if ($stmt === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
          
            }

            if (sqlsrv_execute($stmt) === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
            }

        }

        elseif($genre === '판타지')
        {
            $query = "INSERT INTO [도서 위치] (도서ID, 층수, 구역) VALUES (?, '3층', ?)";
            $params = array($id, $genre);
            $stmt = sqlsrv_prepare($conn, $query, $params);
            if ($stmt === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
          
            }

            if (sqlsrv_execute($stmt) === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
            }

        }

        elseif($genre === '시' || $genre === '정치' || $genre === '자기계발')
        {
            $query = "INSERT INTO [도서 위치] (도서ID, 층수, 구역) VALUES (?, '1층', ?)";
            $params = array($id, $genre);
            $stmt = sqlsrv_prepare($conn, $query, $params);
            if ($stmt === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_execute($stmt) === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
            }

          
        }
        
        else
        {
            $query = "INSERT INTO [도서 위치] (도서ID, 층수, 구역) VALUES (?, '4층', ?)";
            $params = array($id, $genre);
            $stmt = sqlsrv_prepare($conn, $query, $params);
            if ($stmt === false) {
                echo "실패";
                die(print_r(sqlsrv_errors(), true));
            }
          
        

            if (sqlsrv_execute($stmt) === false) {
                echo "실패<br>";
                
                die(print_r(sqlsrv_errors(), true));
            }

        }
        echo "도서가 성공적으로 등록되었습니다.<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
    }
  }

  ?>

<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>