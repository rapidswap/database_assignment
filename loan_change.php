<?php
  // 데이터베이스 연결 설정
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
    // 선택된 대출 및 반납 정보를 가져옵니다.
    $selectedLoans = $_POST['selectedLoans'] ?? [] ;

    if (isset($_POST['changebutton'])) {
      // 대출 및 반납 정보를 변경합니다.
      foreach ($selectedLoans as $loanNumber) {
        $loanDate = $_POST["loanDate_{$loanNumber}"]?? null;
        $loanPeriod = $_POST["loanPeriod_{$loanNumber}"]?? null;
  
        // 수정 가능한 항목만 업데이트합니다.
        if ($loanDate !== null) {
        // 대출 일자 업데이트
          $query = "UPDATE [대출 및 반납 정보] SET [대출 일자] = '{$loanDate}' WHERE [대출 번호] = {$loanNumber}";
          $stmt = sqlsrv_query($conn, $query);
      
          if ($stmt === false) {
            echo "대출 일자 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          } 
        }
  
        if ($loanPeriod !== null) {
          // 대출 기간 업데이트
          $query = "UPDATE [대출 및 반납 정보] SET [대출 기간] = {$loanPeriod} WHERE [대출 번호] = {$loanNumber}";
          $stmt = sqlsrv_query($conn, $query);
  
          if ($stmt === false) {
            echo "대출 기간 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
        }
      }

      echo "대출 정보 업데이트가 완료 되었습니다.<br>";
      echo '<button onclick="goToHomePage()">돌아가기</button>';

    }
  }

  if (isset($_POST['deletebutton'])) {
    // 삭제 버튼이 클릭된 경우에 실행할 코드를 작성합니다.
    
    // 선택된 대출 번호들을 배열로 가져옵니다.
    $selectedLoans = $_POST['selectedLoans'];
  
    // 선택된 대출 번호를 이용하여 삭제합니다.
    foreach ($selectedLoans as $loanNumber) {
      $deleteQuery = "DELETE FROM [대출 및 반납 정보] WHERE [대출 번호] = {$loanNumber}";
      $deleteResult = sqlsrv_query($conn, $deleteQuery);
  
      if ($deleteResult === false) {
        echo "대출 및 반납 정보 삭제 실패<br>";
        die(print_r(sqlsrv_errors(), true));
      }
  
      echo "대출 번호 {$loanNumber} 삭제 완료<br>";
      echo '<script>window.onload = function() { document.getElementsByTagName("button")[0].style.display = "none"; }</script>';
    }
  }
// 필요한 리디렉션 또는 메시지 출력 등을 수행합니다.
?>

<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
    window.onload = function() {
        var selectedLoans = <?php echo json_encode($_POST['selectedLoans'] ?? []) ?>;
        if (selectedLoans.length === 0) {
            document.getElementById("goToHomeButton").style.display = "none";
        }
    }
</script>