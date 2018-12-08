<?php
  include("../assets/includes/config.php");
  if (!isLoggedIn()) {
    header('Location: http://yeetr.me/');
    die();
  }
  $user = getUserBySID();
?>
 <html>
  <head>
    <title>Feed - <?php echo $conf_name; ?></title>
    <link type="text/css" rel="stylesheet" href="../assets/css/master.css<?php if ($conf_refresh) { echo "?t=".strval(time()); } ?>">
  </head>
  <body>
    <img src="/assets/img/logo.gif" alt="YEET"> <!-- Source: cooltext -->
    <center>
        <button id = "newYeet" onclick = "goToYeet()"> New Yeet :) </button>
    </center>
    <script>
        function goToYeet() {
            window.location.replace("../yeet");
        }
    </script>
    <p id = "feed">
        <table>
            <tc>
                <tr>
                    <img style="vertical-align:top" src="<?php echo $user['pic']; ?>">
                    <b class = "name"><?php echo $user['name']; ?></b>
                    <label class = "handle"> Handle </label>
                    <label class = "time"> Time </label>
                    <br>
                    <label class = "yeet"> Yeet </label></p>
                </tr>
            </tc>
        </table>
    <script>
        var timeSent = 0;
        var currentTime = 0;
        var timeSince = currentTime - timeSent;
        // for each (var yeet in listOfYeets) {
        //     document.getElementById('feed').write(
        //         <div>
        //             <img style="vertical-align:top" src=" <?php echo $user['pic']; ?>">
        //             <b class = "name"><?php echo $user['name']; ?></b>
        //             <label class = "handle"> Handle </label>
        //         </div>
        //     <label class = "yeet"> Yeet </label>);
        // }
    </script>
  </body>
</html>
