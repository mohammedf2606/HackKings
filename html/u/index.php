<?php
  include("../assets/includes/config.php");
  $load = '';
  $authed = isLoggedIn();
  if (isset($_GET['u'])) {
    if (userExists($_GET['u'])) {
      $load = $db->real_escape_string($_GET['u']);
    } else {
      header('Location: http://yeetr.me/error/');
      die();
    }
  } else {
    if ($authed) {
      $user = getUserBySID();
      header('Location: http://yeetr.me/u/'.$user['id']);
      die();
    } else {
      header('Location: http://yeetr.me/error/');
      die();
    }
  }
  $follows = false;
  $user = getUserByUID($load);
  $currentUser = "";
  if ($authed) {
    $u = getUserBySID();
    $currentUser = $u['id'];
    $follows = follows($currentUser, $load);
  }
?>
<html>
  <head>
    <title>User - <?php echo $conf_name; ?></title>
    <link type="text/css" rel="stylesheet" href="../assets/css/master.css<?php if ($conf_refresh) { echo "?t=".strval(time()); } ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
    <meta name="google-signin-client_id" content="<?php echo $client_id; ?>">
  </head>
  <body>
    <marquee>Welcome to YEeTr; (Y)esterday's (E)l(e)ctronic (T)witte(r)</marquee>
    <a href="http://yeetr.me/feed"><img src="/assets/img/logo.gif" alt="YEET"></a> <!-- Source: cooltext -->
    <center>
      <a href="http://yeetr.me/yeet/"><button> New Yeet :) </button></a>
    </center>
    <p></p>

    <script>
      $(function() {
        loadYeets();
      });
      function init() {
        gapi.load('auth2', function() { });
        gapi.auth2.init({clientId: "<?php echo $client_id; ?>"});
      }
      setInterval(function() {
        loadYeets();
      }, 2000);
      function fToggle() {
        $.ajax({
          type: "GET",
          url: "../endpoints/follow.php",
          data: "target=<?php echo $load; ?>",
          success: function(data) {
            var obj = JSON.parse(data);
            if (obj.status == 1) {
              if (obj.content == "Followed") {
                $('#follow').text("Unfollow");
              } else {
                $('#follow').text("Follow");
              }
            }
          }
        });
      }
      function loadYeets() {
        $.ajax({
          type: "GET",
          url: "../endpoints/singlefeed.php",
          data: "user=<?php echo $load; ?>",
          success: function(data) {
            var obj = JSON.parse(data);
            if (obj.status == 1) {
              $("#yeets tr").remove();
              $.each(obj.content, function(index, value) {
                var yeetHtml = "<tr><td width=\"96px\">";
                yeetHtml += "<img style=\"vertical-align:top\" src=\"" + value.user.pic + "\"></td>";
                yeetHtml += "<td width=\"20%\"><b class=\"name\">" + value.user.name + "</b><br>";
                yeetHtml += "<label class=\"handle\">" + value.user.id + "</label><br>";
                yeetHtml += "<label class=\"time\">Posted " + value.time + " seconds ago</label></td><td>";
                yeetHtml += "<label class=\"yeet\">" + value.body + "</label></td></tr>";
                $("#yeets").append(yeetHtml);
              });
            }
          }
        });
      }
    </script>
    <table width="100%">
      <tr>
        <td valign="top" width="15%">
          <img width="100%" src="<?php echo $user['pic']; ?>">
          <br>
          <b>Followers: </b><em><?php echo strval(followerCount($load)); ?></em>
          <br>
          <b>Following: </b><em><?php echo strval(followingCount($load)); ?></em>
          <br>
<?php
  if ($currentUser == $load) {
?>
          <h3>Name:</h3>
          <p></p>
          <input id="name" width="100%" type="text" id="name" value="<?php echo $user['name']; ?>">
          <br>
          <h3>Bio:</h3>
          <p></p>
          <textarea id="bio" rows=5 maxlength=512 style="width: 100%; resize: vertical;"><?php echo $user['bio']?></textarea>
          <p></p>
          <button onclick="update()">Update</button>
          <p></p>
          <button id="logout" onclick="logOut()">Log out</button>

          <script>
            function update(){
              var newName = $('#name').val();
              var newBio = $('#bio').val();
              $.ajax({
                type: "POST",
                url: "../endpoints/profile.php",
                data: "name=" + escape(newName) + " &bio=" + escape(newBio),
                success: function(data) {
                alert(JSON.parse(data).content);
                }
              });
            }

            function logOut() {
              var auth2 = gapi.auth2.init({clientId: "<?php echo $client_id; ?>"});
                auth2.signOut().then(function () {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'http://yeetr.me/endpoints/deauth.php');
                xhr.send();
                xhr.onreadystatechange = function() {
                  location.reload(true);  
                };
              });
              document.getElementById("logout").innerText= "Confirm";
            }
          </script>
<?php
  } else {
?>
          <h3><?php echo htmlspecialchars($user['name']); ?></h3>
          <br>
          <em><?php echo htmlspecialchars($user['bio']); ?></em>
<?php
  }
?>
          <br>
<?php
  if ($currentUser == $load) {
?>
          
<?php
  } else if ($authed) {
?>
          <button id="follow" onclick="fToggle()"><?php if ($follows) { echo "Unfollow"; } else { echo "Follow"; } ?></button>
<?php
  }
?>
        </td>
        <td valign="top">
          <table border="1px" width="100%" id="yeets">
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
