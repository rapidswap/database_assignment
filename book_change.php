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
    // 선택된 도서 정보를 가져옵니다.
    $selectedbooks = $_POST['selectedbooks']?? null;

    if (isset($_POST['changebutton'])) {
      // 도서 정보를 변경합니다.
      foreach ($selectedbooks as $bookid) {
        $bookname = $_POST["bookName_{$bookid}"] ?? null;
        $writer = $_POST["writer_{$bookid}"] ?? null;
        $publisher = $_POST["publisher_{$bookid}"] ?? null;
        $publishigday = $_POST["publishig_day_{$bookid}"] ?? null;
        $genre = $_POST["genre_{$bookid}"] ?? null;
  
        // 수정 가능한 항목만 업데이트합니다.
        if ($bookname !== null) {
          // 도서제목 업데이트
         $query = "UPDATE 도서 SET 도서제목 = '{$bookname}' WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);
  
          if ($stmt === false) {
            echo "도서제목 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
        }
  
        if ($writer !== null) {
          // 작가 업데이트
          $query = "UPDATE 도서 SET 작가 = '{$writer}' WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);
  
          if ($stmt === false) {
            echo "작가 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
        }

        if ($publisher !== null) {
          // 출판사 업데이트
          $query = "UPDATE 도서 SET 출판사 = '{$publisher}' WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);
  
          if ($stmt === false) {
            echo "출판사 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
        }

        if ($publishigday !== null) {
          // 출판 일자 업데이트
          $birthDate = date_format(date_create($publishigday), 'Y-m-d'); // datetime2 형식으로 변환
          $query = "UPDATE 도서 SET [출판 일자] = CONVERT(datetime2, '{$publishigday}', 23) WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);
    
          if ($stmt === false) {
            echo "출판 일자 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
        }

        if ($genre !== null) {
          // 장르 업데이트
          $query = "UPDATE 도서 SET 장르 = '{$genre}' WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);
  
          if ($stmt === false) {
            echo "장르 업데이트 실패<br>";
            
            die(print_r(sqlsrv_errors(), true));
          }
          $query = "UPDATE [도서 위치] SET 구역 = '{$genre}' WHERE 도서ID = {$bookid}";
          $stmt = sqlsrv_query($conn, $query);

          if($genre === '역사' || $genre === '소설')
          {
            $query = "UPDATE [도서 위치] SET 층수 = '2층' WHERE 도서ID = {$bookid}";
            $stmt = sqlsrv_query($conn, $query);          
          }

          elseif($genre === '판타지')
          {
            $query = "UPDATE [도서 위치] SET 층수 = '3층' WHERE 도서ID = {$bookid}";
            $stmt = sqlsrv_query($conn, $query);  
          }

          elseif($genre === '시' || $genre === '정치' || $genre === '자기계발')
          {
            $query = "UPDATE [도서 위치] SET 층수 = '1층' WHERE 도서ID = {$bookid}";
            $stmt = sqlsrv_query($conn, $query);  
          }
        
          else
          {
            $query = "UPDATE [도서 위치] SET 층수 = '4층' WHERE 도서ID = {$bookid}";
            $stmt = sqlsrv_query($conn, $query);  
          }
        }
      }
      echo"도서 정보가 업데이트 되었습니다.<br>";
      echo '<button onclick="goToHomePage()">돌아가기</button>';
    }
  }

  if (isset($_POST['deletebutton'])) {
    // 삭제 버튼이 클릭된 경우에 실행할 코드를 작성합니다.
    
    // 선택된 대출 번호들을 배열로 가져옵니다.
    $selectedLoans = $_POST['selectedbooks'];
  
    // 선택된 대출 번호를 이용하여 삭제합니다.
    foreach ($selectedbooks as $bookid) {
    
      $deleteQuery = "DELETE FROM [도서 위치] WHERE 도서ID = {$bookid}";
      $deleteResult = sqlsrv_query($conn, $deleteQuery);
      $deleteQuery = "DELETE FROM 도서 WHERE 도서ID = {$bookid}";
      $deleteResult = sqlsrv_query($conn, $deleteQuery);
  
      if ($deleteResult === false) {
        echo "도서 정보 삭제 실패<br>";
        echo '<button onclick="goToHomePage()">돌아가기</button>';
        die(print_r(sqlsrv_errors(), true));
      }
  
      echo "도서ID {$bookid} 삭제 완료<br>";
      echo '<button onclick="goToHomePage()">돌아가기</button>';
    }
  }
  
?>

<script>
    function goToHomePage() {
        window.location.href = "mssqltest.php";
    }
</script>