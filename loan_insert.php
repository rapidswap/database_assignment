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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loanNumber"])) {
    // 폼에서 전송된 데이터 가져오기
    $loanNumber = $_POST["loanNumber"];
    $memberid = $_POST["memberid"];
    $bookid = $_POST["bookid"];
    $loandate = $_POST["loanDate"];
    $loanperiod = $_POST["loanPeriod"];
    $returnDate = $_POST["returnDate"];
  
    // 데이터 유효성 검사 등 필요한 검증 과정 추가 가능
  
    // 회원 ID를 사용하여 실제 회원 정보를 조회
    $query_member = "SELECT * FROM [회원] WHERE 회원ID = ?";
    $params_member = array($memberid);
    $stmt_member = sqlsrv_prepare($conn, $query_member, $params_member);
    $result_member = sqlsrv_execute($stmt_member);
  
    if ($result_member === false) {
      echo "회원 정보 조회 실패<br>";
      echo '<button onclick="goToHomePage()">돌아가기</button>';
      die(print_r(sqlsrv_errors(), true));
    }
    $query_book_ids = "SELECT 도서ID FROM 도서";
    $stmt_book_ids = sqlsrv_query($conn, $query_book_ids);
    if ($stmt_book_ids === false) {
      die(print_r(sqlsrv_errors(), true));
    }
  
    $available_book_ids = array();
    while ($row_book_ids = sqlsrv_fetch_array($stmt_book_ids, SQLSRV_FETCH_ASSOC)) {
      $available_book_ids[] = $row_book_ids['도서ID'];
    }
  
    // 회원 정보가 존재하는 경우에만 쿼리 실행
    if (sqlsrv_has_rows($stmt_member)) {
      // IDENTITY_INSERT 설정을 ON으로 변경
      $query_identity = "SET IDENTITY_INSERT [대출 및 반납 정보] ON";
      $stmt_identity = sqlsrv_query($conn, $query_identity);
  
      if ($stmt_identity === false) {
        echo "IDENTITY_INSERT 설정 실패<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
        die(print_r(sqlsrv_errors(), true));
      }

      // 반납 일자가 입력된 경우에만 반납 일자 업데이트
      if (!empty($returnDate)&& isset($_POST['returnbutton'])) {
        $selectedLoans = $_POST['selectedLoans'];
        foreach ($selectedLoans as $loanNumber){

          $query_update = "UPDATE [대출 및 반납 정보] SET [반납 일자] = ? WHERE [대출 번호] = ?";
          $params_update = array($returnDate, $loanNumber);
          $stmt_update = sqlsrv_prepare($conn, $query_update, $params_update);

          if ($stmt_update === false) {
            echo "반납 일자 업데이트 실패<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';
            die(print_r(sqlsrv_errors(), true));
          }

          if (sqlsrv_execute($stmt_update) === false) {
            echo "반납 일자 업데이트 실패<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';
            die(print_r(sqlsrv_errors(), true));
          }
          else 
          {
            echo "반납 일자가 성공적으로 업데이트되었습니다.<br>";
            echo '<button onclick="goToHomePage()">돌아가기</button>';
            
          }
        }
      }
      else{
        // 쿼리 작성
        $query = "INSERT INTO [대출 및 반납 정보] ([대출 번호], 회원ID, 도서ID, [대출 일자], [대출 기간]) VALUES (?, ?, ?, ?, ?)";
        $params = array($loanNumber, $memberid, $bookid, $loandate, $loanperiod);
  
        // 쿼리 실행
        $stmt = sqlsrv_prepare($conn, $query, $params);
      
        if(empty($loanNumber))
        {
          echo"대출 번호를 입력하십시오<br>";
          die();
        
        }
  
        if ($stmt === false) {
          echo "실패1<br>";
          die();
        
        
        }
  
        if (sqlsrv_execute($stmt) === false) {
          echo "실패2<br>";
          die();
        } 
        else 
        { 
          echo "도서대출 및 반납 정보가 성공적으로 등록되었습니다.<br>";
          echo '<button onclick="goToHomePage()">돌아가기</button>';
        }
      

        // IDENTITY_INSERT 설정을 OFF로 변경
        $query_identity_off = "SET IDENTITY_INSERT [대출 및 반납 정보] OFF";
        $stmt_identity_off = sqlsrv_query($conn, $query_identity_off);
  
        if ($stmt_identity_off === false) {
          echo "IDENTITY_INSERT 설정 해제 실패<br>";
          die(print_r(sqlsrv_errors(), true));
        }
      }
    } 
    else
    {
      echo "유효하지 않은 회원 ID입니다.<br>";
      echo '<button onclick="goToHomePage()">돌아가기</button>';
    }
}

?>
<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>