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
?>

<!DOCTYPE html>
<html>
<head>
  <title>관리</title>
  <style>
    .books-form {
      display: none;
    }
    .books-form.show {
      display: block;
    }
    body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background-color: #f4f4f4;
}

.container {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h1 {
  font-size: 24px;
  color: #333;
  text-align: center;
  margin-bottom: 20px;
}

p {
  font-size: 16px;
  color: #666;
  line-height: 1.5;
  margin-bottom: 10px;
}

.button {
  display: inline-block;
  text-align: center;
  padding: 10px 20px;
  font-size: 16px;
  color: #fff;
  background-color: #007bff;
  border-radius: 4px;
  text-decoration: none;
}

.button:hover {
  background-color: #0056b3;
}

.footer {
  text-align: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #ccc;
  color: #888;
}

.footer a {
  color: #888;
  text-decoration: none;
}

.footer a:hover {
  text-decoration: underline;
}

  </style>
  <script>
    
    function goToHomePage() {
        window.location.href = "member_change.php";
    }

    function showForm(formId) {
      var forms = document.getElementsByClassName('books-form');
      for (var i = 0; i < forms.length; i++) {
        forms[i].classList.remove('show');
      }
      document.getElementById(formId).classList.add('show');
    }

    function disableOtherForms(formId) {
      var forms = document.getElementsByName('booksType');
      for (var i = 0; i < forms.length; i++) {
        if (forms[i].id !== formId) {
          forms[i].checked = false;
        }
      }
    }
  </script>
</head>

<body>
  <h1>안양 도서관 관리</h1>

  <h2>등록</h2>
  <form>
    <label for="loanRegistration">대출 및 반납 등록</label>
    <input type="radio" id="loanRegistration" name="booksType" value="loanRegistration" onclick="showForm('loanForm'); disableOtherForms('loanRegistration');">

    <label for="memberRegistration">회원 등록</label>
    <input type="radio" id="memberRegistration" name="booksType" value="memberRegistration" onclick="showForm('memberForm'); disableOtherForms('memberRegistration');">

    <label for="bookRegistration">도서 등록</label>
    <input type="radio" id="bookRegistration" name="booksType" value="bookRegistration" onclick="showForm('BookForm'); disableOtherForms('bookRegistration');" >

    <label for="studyRoomReservation">스터디 룸 예약 등록</label>
    <input type="radio" id="studyRoomReservation" name="booksType" value="studyRoomReservation" onclick="showForm('studyRoomForm'); disableOtherForms('studyRoomReservation');" ><br>
  </form>
  <h2>변경 및 삭제</h2>
  <form>
    <label for="loanChange">대출 및 반납 변경 및 삭제</label>
    <input type="radio" id="loanChange" name="booksType" value="loanChange" onclick="showForm('loanchangeForm'); disableOtherForms('loanChange');">

    <label for="memberChange">회원 변경 및 삭제</label>
    <input type="radio" id="memberChange" name="booksType" value="memberChange" onclick="showForm('memberchangeForm'); disableOtherForms('memberChange');">

    <label for="bookChange">도서 변경 및 삭제</label>
    <input type="radio" id="bookChange" name="booksType" value="bookChange" onclick="showForm('bookchangeForm'); disableOtherForms('bookChange');" >

    <label for="studyRoomChange">스터디 룸 예약 변경 및 삭제</label>
    <input type="radio" id="studyRoomChange" name="booksType" value="studyRoomChange" onclick="showForm('studyRoomchangeForm'); disableOtherForms('studyRoomChange');" >
  </form>






  <div id="loanForm" class="books-form">
    <h3>대출 및 반납 등록</h3>
    <form name="loanForm" action="loan_insert.php" method="POST">
      <label for="loanNumber">대출번호:</label>
      <input type="text" id="loanNumber" name="loanNumber"><br>

      <label for="memberid">회원 ID:</label>
      <select name="memberid" id="회원ID">
        <?php
        // SQL 서버에서 회원 ID 가져오기
        $sql = "SELECT 회원ID FROM 회원";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
          die(print_r(sqlsrv_errors(), true));
        }

        // 옵션으로 회원 ID 추가
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $회원ID = $row['회원ID'];
          echo "<option value=\"$회원ID\">$회원ID</option>";
        }

        sqlsrv_free_stmt($stmt);
        ?>
      </select><br>

      <label for="bookid">도서 ID:</label>
      <select name="bookid" id="도서ID">
        <?php
        // SQL 서버에서 도서 ID 가져오기
        $sql = "SELECT 도서ID FROM 도서";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
          die(print_r(sqlsrv_errors(), true));
        }

        // 옵션으로 도서 ID 추가
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $도서ID = $row['도서ID'];
          echo "<option value=\"$도서ID\">$도서ID</option>";
        }

        sqlsrv_free_stmt($stmt);
        ?>
      </select><br>

      <label for="loanDate">대출일자:</label>
      <input type="date" id="loanDate" name="loanDate"><br>

      <label for="loanPeriod">대출 기간:</label>
      <input type="number" id="loanPeriod" name="loanPeriod" min="1"><br>

      <input type="submit" value="등록"><br><br>

    <?php
    // 필요한 DB 연결 설정 등을 수행합니다.

    // 대출 및 반납 정보 조회
    $query = "SELECT * FROM [대출 및 반납 정보]";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
      echo "대출 및 반납 정보 조회 실패";
      die(print_r(sqlsrv_errors(), true));
    }

    // 대출 및 반납 정보를 체크박스로 표시합니다.
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      $대출번호 = $row['대출 번호'];
      $회원ID = $row['회원ID'];
      $도서ID = $row['도서ID'];
      $대출일자 = $row['대출 일자'];
      $대출기간 = $row['대출 기간'];
      $반납일자 = $row['반납 일자'];

      // 대출 및 반납 정보를 체크박스로 표시합니다.
      echo "<input type=\"checkbox\" name=\"selectedLoans[]\" value=\"{$대출번호}\"> 대출 번호: {$대출번호}, 회원ID: {$회원ID}, 도서ID: {$도서ID}";

      if ($대출일자 !== null) {
        echo ", 대출 일자: {$대출일자->format('Y-m-d')}";
      }

      if ($반납일자 !== null) {
        echo ", 대출 기간: {$대출기간}";
        echo ", 반납 일자: {$반납일자->format('Y-m-d')}<br>";
      }
      else{
        echo ", 대출 기간: {$대출기간}<br>";
      }

    }
    ?>
      <label for="returnDate">원하는 대출 도서를 선택 후 반납 일자를 입력 해주세요:</label>
      <input type="date" id="returnDate" name="returnDate"><br>
      <input type="submit" name="returnbutton" value="반납"><br>
    </form>
  </div>









  <div id="loanchangeForm" class="books-form">
    <h3>대출 및 반납 변경 및 삭제</h3>

    <form name="loanchangeForm" action="loan_change.php" method="POST">
      <input type="submit" name="changebutton" value="변경">
      <input type="submit" name="deletebutton" value="삭제" onclick="return confirm('정말로 선택한 대출 및 반납 정보를 삭제하시겠습니까?');"><br>

      <?php
      // 대출 및 반납 정보 조회 및 체크박스로 표시하는 로직을 구현합니다.
      // 필요한 DB 연결 설정 등을 수행합니다.

      // 대출 및 반납 정보 조회
      $query = "SELECT * FROM [대출 및 반납 정보]";
      $stmt = sqlsrv_query($conn, $query);

      if ($stmt === false) {
        echo "대출 및 반납 정보 조회 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      // 대출 및 반납 정보를 체크박스로 표시합니다.
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $대출번호 = $row['대출 번호'];
        $회원ID = $row['회원ID'];
        $도서ID = $row['도서ID'];
        $대출일자 = $row['대출 일자'];
        $대출기간 = $row['대출 기간'];
        $반납일자 = $row['반납 일자'];

        echo "<input type=\"checkbox\" name=\"selectedLoans[]\" value=\"{$대출번호}\"> 대출 번호: {$대출번호}, 회원ID: {$회원ID}, 도서ID: {$도서ID}";

        if ($대출일자 !== null) {
          echo ", 대출 일자: ";
          echo "<input type=\"date\" name=\"loanDate_{$대출번호}\" value=\"{$대출일자->format('Y-m-d')}\"";
          echo ">";
        }

        if ($대출기간 !== null){
          echo ", 대출 기간: ";
          echo "<input type=\"number\" name=\"loanPeriod_{$대출번호}\" min=\"1\" value=\"{$대출기간}\"";
          echo ">";
        }

        if ($반납일자 !== null) {
          echo ", 반납 일자: {$반납일자->format('Y-m-d')}";
        }

        echo "<br>";
      }
      ?>
      </form>
  </div>








  <div id="memberForm" class="books-form">
    <h3>회원 등록</h3>
    <form name="memberForm" action="member_insert.php" method="POST">
      <label for="name">이름:</label>
      <input type="text" id="name" name="name"><br>

      <label for="birthDate">생년월일:</label>
      <input type="date" id="birthDate" name="birthDate"><br>

      <label for="contact">연락처:</label>
      <input type="tel" id="contact" name="contact"><br>

      <label for="address">주소:</label>
      <input type="text" id="address" name="address"><br>

      <label for="joinDate">가입일:</label>
      <input type="date" id="joinDate" name="joinDate"><br>

      <input type="submit" value="등록">
      </form>
  </div>








  <div id="memberchangeForm" class="books-form">
    <h3>회원 변경 및 삭제</h3>
    <form name="memberchangeForm" action="member_change.php" method="POST">
      <input type="submit" name="changebutton" value="변경">
      <input type="submit" name="deletebutton" value="삭제" onclick="return confirm('정말로 선택한 회원 정보를 삭제하시겠습니까?');"><br>
      <?php
    // 필요한 DB 연결 설정 등을 수행합니다.

    // 회원 조회
      $query = "SELECT * FROM 회원";
      $stmt = sqlsrv_query($conn, $query);

      if ($stmt === false) {
        echo "회원 조회 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      // 회원 정보를 체크박스로 표시합니다.
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $회원ID = $row['회원ID'];
        $이름 = $row['이름'];
        $생년월일 = $row['생년월일'];
        $연락처 = $row['연락처'];
        $주소 = $row['주소'];
        $가입일 = $row['가입일'];

        
        echo "<input type=\"checkbox\" name=\"selectedMembers[]\" value=\"{$회원ID}\"> 회원ID: {$회원ID}";

        if($이름 !== null){
          echo ",이름: ";
          echo "<input type=\"text\" name=\"name_{$회원ID}\" value=\"{$이름}\">";
        }

        if($생년월일 !==null){
          echo", 생년월일: ";
          echo "<input type=\"date\" name=\"birthDate_{$회원ID}\" value=\"{$생년월일->format('Y-m-d')}\">";
        }
        
        if($연락처 !== null){
          echo", 연락처: ";
          echo "<input type=\"tel\" name=\"contact_{$회원ID}\" value=\"{$연락처}\">";
        }
        if($주소 !== null){
          echo", 주소: ";
          echo "<input type=\"text\" name=\"address_{$회원ID}\" value=\"{$주소}\">";
        }
        if($가입일 !== null){
          echo", 가입일: ";
          echo "<input type=\"date\" name=\"joinDate_{$회원ID}\" value=\"{$가입일->format('Y-m-d')}\">";
        }

        echo "<br>";
      }
    ?>
    </form>
  </div>





  <div id="BookForm" class="books-form">
    <h3>도서 등록</h3>
    <form name="BookForm" action="book_insert.php" method="POST">
      <label for="bookID">도서ID:</label>
      <input type="text" id="bookID" name="bookID"><br>

      <label for="bookName">도서제목:</label>
      <input type="text" id="bookName" name="bookName"><br>

      <label for="writer">작가:</label>
      <input type="text" id="writer" name="writer"><br>

      <label for="publisher">출판사:</label>
      <input type="text" id="publisher" name="publisher"><br>

      <label for="publishing_day">출판 일자:</label>
      <input type="date" id="publishing_day" name="publishing_day" min="1"><br>

      <label for="genre">장르:</label>
      <input type="text" id="genre" name="genre"><br>

      <input type="submit" value="등록">
    </form>
  </div>




  <div id="bookchangeForm" class="books-form">
    <h3>도서 변경 및 삭제</h3>
    <form name="bookchangeForm" action="book_change.php" method="POST">
      <input type="submit" name="changebutton" value="변경">
      <input type="submit" name="deletebutton" value="삭제" onclick="return confirm('정말로 선택한 도서 정보를 삭제하시겠습니까?');"><br>
      <?php
    // 필요한 DB 연결 설정 등을 수행합니다.

    // 도서 조회
      $query = "SELECT * FROM 도서";
      $stmt = sqlsrv_query($conn, $query);

      if ($stmt === false) {
        echo "도서 조회 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      // 도서 정보를 체크박스로 표시합니다.
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $도서ID = $row['도서ID'];
        $도서제목 = $row['도서제목'];
        $작가 = $row['작가'];
        $출판사 = $row['출판사'];
        $출판일자 = $row['출판 일자'];
        $장르 = $row['장르'];

        
        echo "<input type=\"checkbox\" name=\"selectedbooks[]\" value=\"{$도서ID}\"> 도서ID: {$도서ID}";

        if($도서제목 !== null){
          echo ",도서제목: ";
          echo "<input type=\"text\" name=\"bookName_{$도서ID}\" value=\"{$도서제목}\">";
        }

        if($작가 !== null){
          echo ",작가: ";
          echo "<input type=\"text\" name=\"writer_{$도서ID}\" value=\"{$작가}\">";
        }

        if($출판사 !== null){
          echo ",출판사: ";
          echo "<input type=\"text\" name=\"publisher_{$도서ID}\" value=\"{$출판사}\">";
        }


        if($출판일자 !==null){
          echo", 출판일자: ";
          echo "<input type=\"date\" name=\"publishing_day_{$도서ID}\" value=\"{$출판일자->format('Y-m-d')}\">";
        }
        
        if($장르 !== null){
          echo", 장르: ";
          echo "<input type=\"text\" name=\"genre_{$도서ID}\" value=\"{$장르}\">";
        }

        echo "<br>";
      }
    ?>
    </form>
  </div>







  <div id="studyRoomForm" class="books-form">
    <h3>스터디 룸 예약 등록</h3>
    <form name="studyRoomForm" action="study_room_insert.php" method="POST">
      <label for="reservationNum">예약 번호:</label>
      <input type="text" id="reservationNum" name="reservationNum"><br>

      <label for="memberid">회원 ID:</label>
      <select name="memberid" id="회원ID">
        <?php
        // SQL 서버에서 회원 ID 가져오기
        $sql = "SELECT 회원ID FROM 회원";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
          die(print_r(sqlsrv_errors(), true));
        }

    // 옵션으로 회원 ID 추가
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $회원ID = $row['회원ID'];
          echo "<option value=\"$회원ID\">$회원ID</option>";
        }

        sqlsrv_free_stmt($stmt);
        ?>
      </select><br>

      <label for="Study_RoomID">스터디 룸 ID:</label>
      <select name="Study_RoomID" id="스터디 룸ID">
      <?php
      // SQL 서버에서 스터디 룸 ID 가져오기
      $sql = "SELECT [스터디 룸ID] FROM [스터디 룸]";
      $stmt = sqlsrv_query($conn, $sql);
      if ($stmt === false) {
          die(print_r(sqlsrv_errors(), true));
      }

      // 옵션으로 스터디 룸 ID 추가
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $스터디룸ID = htmlspecialchars($row['스터디 룸ID']);
        echo "<option value=\"$스터디룸ID\">$스터디룸ID</option>";
      }

      sqlsrv_free_stmt($stmt);
      ?>
    </select><br>

      <label for="reservationStart">예약 시작 시간:</label>
      <input type="time" id="reservationStart" name="reservationStart">

      <label for="reservationEnd">예약 종료 시간:</label>
      <input type="time" id="reservationEnd" name="reservationEnd"><br>

      <label for="reservationDay">예약 일자:</label>
      <input type="date" id="reservationDay" name="reservationDay"><br>

      <input type="submit" value="등록">
    </form>
  </div>




  <div id="studyRoomchangeForm" class="books-form">
    <h3>스터디 룸 변경 및 삭제</h3>
    <form name="studyRoomchangeForm" action="study_room_change.php" method="POST">
      <input type="submit" name="changebutton" value="변경">
      <input type="submit" name="deletebutton" value="삭제" onclick="return confirm('정말로 선택한 스터디 룸 예약 정보를 삭제하시겠습니까?');"><br>
      <?php
    // 필요한 DB 연결 설정 등을 수행합니다.

    // 스터디 룸 조회
      $query = "SELECT * FROM [스터디 룸 예약 정보]";
      $stmt = sqlsrv_query($conn, $query);

      if ($stmt === false) {
        echo "스터디 룸 예약 정보 조회 실패";
        die(print_r(sqlsrv_errors(), true));
      }

      // 스터디 룸 정보를 체크박스로 표시합니다.
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $예약번호 = $row['예약 번호'];
        $회원ID = $row['회원ID'];
        $스터디룸ID = $row['스터디 룸ID'];
        $예약시작시간 = $row['예약 시작 시간'];
        $예약종료시간 = $row['예약 종료 시간'];
        $예약일자 = $row['예약 일자'];
        
        echo "<input type=\"checkbox\" name=\"selectedStudyroomres[]\" value=\"{$예약번호}\"> 예약 번호: {$예약번호}, 회원ID: {$회원ID}, 스터디 룸ID: {$스터디룸ID}";


        if($예약시작시간 !== null){
          echo ",예약 시작 시간: ";
          echo "<input type=\"time\" name=\"reservationStart_{$예약번호}\" value=\"{$예약시작시간->format('H:i:s')}\">";
        }


        if($예약종료시간 !==null){
          echo", 예약 종료 시간: ";
          echo "<input type=\"time\" name=\"reservationEnd_{$예약번호}\" value=\"{$예약종료시간->format('H:i:s')}\">";
        }
        
        if($예약일자 !== null){
          echo", 예약 일자: ";
          echo "<input type=\"date\" name=\"reservationDay_{$예약번호}\" value=\"{$예약일자->format('Y-m-d')}\">";
        }

        echo "<br>";
      }
    ?>
    </form>
  </div>


</body>
</html>

