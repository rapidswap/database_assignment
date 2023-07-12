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
  else{
      echo "연결성공";
  }


if (isset($_POST['submit'])) {
  // 변경 또는 삭제 버튼이 클릭된 경우에만 실행합니다.

  if ($_POST['submit'] === '변경') {
    // 변경 버튼이 클릭된 경우에 실행할 코드를 작성합니다.

    // 선택된 대출 번호들을 배열로 가져옵니다.
    $selectedLoans = $_POST['selectedLoans'];

    // 선택된 대출 번호를 이용하여 대출 및 반납 정보를 변경합니다.
    foreach ($selectedLoans as $loanNumber) {
      $loanDate = $_POST['loanDate_' . $loanNumber];
      $loanPeriod = $_POST['loanPeriod_' . $loanNumber];

      // 대출 일자와 대출 기간을 수정하는 쿼리를 실행합니다.
      $updateQuery = "UPDATE [대출 및 반납 정보] SET [대출 일자] = '{$loanDate}', [대출 기간] = '{$loanPeriod}' WHERE 대출번호 = '{$loanNumber}'";
      $updateResult = sqlsrv_query($conn, $updateQuery);

      if ($updateResult === false) {
        echo "대출 및 반납 정보 변경 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      echo "대출 번호 {$loanNumber} 변경 완료<br>";
    }
  } elseif ($_POST['submit'] === '삭제') {
    // 삭제 버튼이 클릭된 경우에 실행할 코드를 작성합니다.

    // 선택된 대출 번호들을 배열로 가져옵니다.
    $selectedLoans = $_POST['selectedLoans'];

    // 선택된 대출 번호를 이용하여 삭제합니다.
    foreach ($selectedLoans as $loanNumber) {
      $deleteQuery = "DELETE FROM [대출 및 반납 정보] WHERE 대출번호 = '{$loanNumber}'";
      $deleteResult = sqlsrv_query($conn, $deleteQuery);

      if ($deleteResult === false) {
        echo "대출 및 반납 정보 삭제 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      echo "대출 번호 {$loanNumber} 삭제 완료<br>";
    }
  }
}

// 필요한 리디렉션 또는 메시지 출력 등을 수행합니다.
?>

