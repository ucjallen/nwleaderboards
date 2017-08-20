<?php
    date_default_timezone_set('America/Chicago');  //Set this to the timezone of the server, for midnight when a new log file is created
    set_time_limit(120);
    $date = "server_log_" . date("m_d_y") . ".txt";
    $filename = "/xxx.xxx.xxx.xxx_xxxx/Logs/$date";     //Replace xxx.xxx.xxx.xxx_xxxx with the path inside your ftp server to your log folder

    $ftpServer  = "xxx.xxx.xxx.xxx";    //Change to your ftp server ip address (probably same as game server ip)
    $ftpUser    = "Username";           //Make username of control panel login or ftp account login
    $ftpPass    = "Password";           //Make password of control panel login or ftp acocount
    $localFile  = "gf_server_log.txt";
    $ftpConn    = ftp_connect($ftpServer, 8821);        //Change the port in here to your ftp port
    while(!$ftpConn) {
        sleep(2);
        $ftpConn = ftp_connect($ftpServer, 8821);       //Change the port in here to your ftp port
    }
    sleep(2);
    $login      = ftp_login($ftpConn, $ftpUser, $ftpPass);

    ftp_pasv($ftpConn, true);
    if(!ftp_get($ftpConn, $localFile, $filename, FTP_BINARY)) {
        die();
    }
    ftp_close($ftpConn);

    $host_name  = "xxx.xxx.xxx.xxx";    //Change to your database ip address
    $database   = "statservers";        //Change to your database name
    $user_name  = "Username";           //Change to your database username
    $password   = "Password";           //Change to your database password

    $connect = mysqli_connect($host_name, $user_name, $password, $database);
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $currentDay = date("d");
    $query = "SELECT * FROM GFFILEPOSITION WHERE ID = 1";
    $result = mysqli_query($connect, $query);
    $dbResult = mysqli_fetch_assoc($result);
    $dayInDb = $dbResult['DAY'];

    if ($currentDay != $dayInDb) {
        $query = "UPDATE GFFILEPOSITION SET DAY = '$currentDay', FILEPOS = 0 WHERE ID = 1";
        mysqli_query($connect, $query);
        $dayInDb = $currentDay;
        $positionInDb = 0;
    } else {
        $positionInDb = $dbResult['FILEPOS'];
    }

    $file = fopen("gf_server_log.txt", "r");
    $fersize = filesize("gf_server_log.txt");
    clearstatcache();

    if(!$file) {

    } elseif ($positionInDb == $fersize) {

    } elseif ($fersize < $positionInDb) {
     $positionInDb = $fersize;
    } else if($positionInDb < $fersize) {
        fseek($file, $positionInDb);
        while(!FEOF($file)) {
            $line = fgets($file);
            if($positionInDb == 0) {
                $line = " " . $line;
                $positionInDb = 1;
            }
            if (preg_match("(\S+ - (\S+) <.*)", $line)) {
                //RECORDS KILLS
                $strArray = explode(' ', $line);
                $killer = $strArray[3];
                $victim = $strArray[5];
                $killerID = "SELECT GUID FROM GFONLINE WHERE PNAME = '$killer'";
                $result = mysqli_query($connect, $killerID);
                $killer = mysqli_fetch_assoc($result);
                $victimID = "SELECT GUID FROM GFONLINE WHERE PNAME = '$victim'";
                $result = mysqli_query($connect, $victimID);
                $victim = mysqli_fetch_assoc($result);
                if($killer['GUID'] != 0 && $victim['GUID'] != 0) {
                     $killerQuery = "UPDATE GFSERVER SET KILLS = KILLS + 1 WHERE GUID = '$killer[GUID]'";
                     mysqli_query($connect, $killerQuery);
                     $victimQuery = "UPDATE GFSERVER SET DEATHS = DEATHS + 1 WHERE GUID = '$victim[GUID]'";
                     mysqli_query($connect, $victimQuery);
                 }
             } elseif (preg_match("(\S+ - (\S+) has joined the game with ID: .*)", $line)) {
                //JOINED GAME
                $strArray = explode(' ', $line);
                $pname = $strArray[3];
                $guid = $strArray[10];
                if($guid != 0) {
                     $presetQuery = "SELECT * FROM GFONLINE WHERE PNAME = '$pname'";
                     $result = mysqli_query($connect, $presetQuery);
                     if ($result->num_rows == 0) {
                          //JOINED GAME -> ADDS NEW PLAYER TO PLAYER TABLE
                          $presetQuery = "INSERT INTO GFONLINE (GUID, PNAME) VALUES ('$guid', '$pname')";
                          mysqli_query($connect, $presetQuery);
                     }
                     $presetQuery = "SELECT * FROM GFSERVER WHERE GUID = '$guid'";
                     $result = mysqli_query($connect, $presetQuery);
                     if ($result->num_rows == 0) {
                         if ($result->num_rows == 0) {
                              $presetQuery = "INSERT INTO GFSERVER (GUID, PNAME, KILLS, DEATHS, TKS) VALUES ('$guid', '$pname', 0, 0, 0)";
                              mysqli_query($connect, $presetQuery);
                         }
                         //JOINED GAME -> ADDS PLAYER TO ONLINE PLAYERS TABLE
                         $presetQuery = "UPDATE GFONLINE SET GUID = '$guid' WHERE PNAME = '$pname'";
                         mysqli_query($connect, $presetQuery);
                         $presetQuery = "UPDATE GFSERVER SET PNAME = '$pname' WHERE GUID = '$guid'";
                         mysqli_query($connect, $presetQuery);
                 }
             } elseif (preg_match("(\S+ - (\S+) teamkilled .*)", $line)) {
                 //Teamkill +1 teamkill, -1 kill for killer
                 $strArray = explode(' ', $line);
                 $killer = $strArray[3];
                 $killerID = "SELECT GUID FROM GFONLINE WHERE PNAME = '$killer'";
                 $result = mysqli_query($connect, $killerID);
                 $killer = mysqli_fetch_assoc($result);
                 if($killer['GUID'] != 0) {
                         $killerQuery = "UPDATE GFSERVER SET TKS = TKS + 1 WHERE GUID = '$killer[GUID]'";
                         mysqli_query($connect, $killerQuery);
                         $killerQuery = "UPDATE GFSERVER SET KILLS = KILLS - 1 WHERE GUID = '$killer[GUID]'";
                         mysqli_query($connect, $killerQuery);
                 }
             }
             flush();
         }
         $positionInDb = ftell($file);
     }
     $query = "UPDATE GFFILEPOSITION SET FILEPOS = '$fersize' WHERE DAY = '$dayInDb'";
     mysqli_query($connect, $query);
     flush();
     fclose($file);
     mysqli_close($connect);

   //(\S+ - (\S+) <.*) regex for kills
   //(\S+ - (\S+) has joined the game with ID: .*) regex for joined
   //(\S+ - (\S+) has left the game with ID: .*) regex for left
   //(\S+ - (\S+) teamkilled .*) regex for teamkill
 ?>
