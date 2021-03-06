<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>Napoleonic Wars Leader Boards</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body>
<div class="wrapper row1">
  <header id="header" class="clear">
    <div id="hgroup">
      <h1><a href="/">Napoleonic Wars Leader Boards</a></h1>
    </div>
    <nav>
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="siege.php">Siege</a></li>
        <li><a href="groupfighting.php">Groupfighting</a></li>
        <li><a href="duel.php">Duel</a></li>
        <li class="last"><a href="#">About</a></li>
      </ul>
    </nav>
  </header>
</div>
<!-- content -->
<div class="wrapper row2">
  <div id="container" class="clear">
    <!-- Slider -->
    <section id="slider"><center><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- banner -->
    <ins class="adsbygoogle"
         style="display:inline-block;width:728px;height:90px"
         data-ad-client="ca-pub-5219599772407006"
         data-ad-slot="2424859507"></ins>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
    </script></center></section>
    <!-- main content -->
    <div id="homepage">
      <!-- Services -->
      <section id="boards" class="clear">
          <table>
              <div id="sort">
                  <div id="killbutton" class="lbbuttons">Sort by Kills</div>
                  <div id="deathbutton" class="lbbuttons">Sort by Deaths</div>
                  <div id="tkbutton" class="lbbuttons">Sort by Teamkills</div>
                  <div class="lbbuttons">
                      <form  method="post" action="" id="searchform">
	                  <input  type="text" name="name" placeholder="Enter Name or GUID">
                      <input  type="submit" name="submit" value="Search">
	                  </form>
                  </div>
              </div>
              <?php
                  $host_name  = "192.243.109.223";
                  $database   = "statservers";
                  $user_name  = "statadmin";
                  $password   = "Xboxorps3";

                  $connect = mysqli_connect($host_name, $user_name, $password, $database);
                  if (mysqli_connect_errno())
                  {
                      echo "Failed to connect to MySQL: " . mysqli_connect_error();
                  }
                  if (!empty($_REQUEST['name'])) {
                      $searchTerm = mysqli_real_escape_string($connect, $_REQUEST['name']);
                      echo "<table class='lbresult' id='killresults'>
                        <th>Rank</th><th>Player</th><th>Kills &darr;</th><th>Deaths</th><th>Teamkills</th><th>Kill Death Ratio</th>";
                      $query = "SET @rownum := 0";
                      mysqli_query($connect, $query);
                      $query = "SELECT RANK, PNAME, KILLS, DEATHS, TKS FROM (SELECT @rownum := @rownum + 1 AS RANK, PNAME, KILLS, DEATHS, TKS, GUID FROM GFSERVER ORDER BY KILLS DESC) AS RESULT WHERE GUID = '$searchTerm'";
                      $result = mysqli_query($connect, $query);
                      if($result->num_rows != 0) {
                          $i = 1;
                          while($row = mysqli_fetch_array($result)) {
                              $kd = round($row[KILLS] / $row[DEATHS], 2);
                              echo "<tr>
                                <td>$row[RANK]</td>
                                <td>$row[PNAME]</td>
                                <td>$row[KILLS]</td>
                                <td>$row[DEATHS]</td>
                                <td>$row[TKS]</td>
                                <td>$kd</td>
                                </tr>";
                                $i = $i + 1;
                            }
                      } else {
                          $query = "SET @rownum := 0";
                          mysqli_query($connect, $query);
                          $query = "SELECT RANK, PNAME, KILLS, DEATHS, TKS FROM (SELECT @rownum := @rownum + 1 AS RANK, PNAME, KILLS, DEATHS, TKS, GUID FROM GFSERVER ORDER BY KILLS DESC) AS RESULT WHERE PNAME = '$searchTerm'";
                          $result = mysqli_query($connect, $query);
                          $i = 1;
                          while($row = mysqli_fetch_array($result)) {
                              $kd = round($row[KILLS] / $row[DEATHS], 2);
                              echo "<tr>
                                <td>$row[RANK]</td>
                                <td>$row[PNAME]</td>
                                <td>$row[KILLS]</td>
                                <td>$row[DEATHS]</td>
                                <td>$row[TKS]</td>
                                <td>$kd</td>
                                </tr>";
                                $i = $i + 1;
                            }
                      }
                  } else {
                  echo "<table class='lbresult' id='killresults'>
                    <th>Rank</th><th>Player</th><th>Kills &darr;</th><th>Deaths</th><th>Teamkills</th><th>Kill Death Ratio</th>";
                  $query = "SELECT * FROM GFSERVER ORDER BY KILLS DESC LIMIT 50";
                  $result = mysqli_query($connect, $query);
                  $i = 1;
                  while($row = mysqli_fetch_array($result)) {
                      $kd = round($row[KILLS] / $row[DEATHS], 2);
                      echo "<tr>
                          <td>$i.</td>
                          <td>$row[PNAME]</td>
                          <td>$row[KILLS]</td>
                          <td>$row[DEATHS]</td>
                          <td>$row[TKS]</td>
                          <td>$kd</td>
                          </tr>";
                          $i = $i + 1;
                  }
                  echo "<table class='lbresult' id='deathresults'>
                    <th>Rank</th><th>Player</th><th>Kills</th><th>Deaths &darr;</th><th>Teamkills</th><th>Kill Death Ratio</th>";
                  $query = "SELECT * FROM GFSERVER ORDER BY DEATHS DESC LIMIT 50";
                  $result = mysqli_query($connect, $query);
                  $i = 1;
                  while($row = mysqli_fetch_array($result)) {
                      $kd = round($row[KILLS] / $row[DEATHS], 2);
                      echo "<tr>
                          <td>$i.</td>
                          <td>$row[PNAME]</td>
                          <td>$row[KILLS]</td>
                          <td>$row[DEATHS]</td>
                          <td>$row[TKS]</td>
                          <td>$kd</td>
                          </tr>";
                          $i = $i + 1;
                  }
                  echo "<table class='lbresult' id='tkresults'>
                    <th>Rank</th><th>Player</th><th>Kills</th><th>Deaths</th><th>Teamkills &darr;</th><th>Kill Death Ratio</th>";
                  $query = "SELECT * FROM GFSERVER ORDER BY TKS DESC LIMIT 50";
                  $result = mysqli_query($connect, $query);
                  $i = 1;
                  while($row = mysqli_fetch_array($result)) {
                      $kd = round($row[KILLS] / $row[DEATHS], 2);
                      echo "<tr>
                          <td>$i.</td>
                          <td>$row[PNAME]</td>
                          <td>$row[KILLS]</td>
                          <td>$row[DEATHS]</td>
                          <td>$row[TKS]</td>
                          <td>$kd</td>
                          </tr>";
                          $i = $i + 1;
                  }
                }
                  mysqli_close($connect);
              ?>
          </table>
      </section>
      <!-- / Services -->
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      <!-- Introduction -->
      <section id="intro" class="last clear">
        <article>
          <figure><img src="" width="450" height="250" alt="">
            <figcaption>
              <h2>Servers Provided by:</h2>
              <img width="450" src="images/qig.png">
            </figcaption>
          </figure>
        </article>
      </section>
      <!-- / Introduction -->
    </div>
    <!-- / content body -->
  </div>
</div>
<!-- Footer -->
<div class="wrapper row3">
  <div id="footer" class="clear">
    <!-- Section One -->
    <section class="one_quarter">
      <h2 class="title">Our Servers</h2>
      <nav>
        <ul>
          <li><a href="siege.php">Siege</a></li>
          <li><a href="groupfighting.php">Groupfighting</a></li>
          <li class="last"><a href="duel.php">Duel</a></li>
        </ul>
      </nav>
    </section>
  </div>
</div>
<!-- Copyright -->
<div class="wrapper row4">
  <footer id="copyright" class="clear">
    <p class="fl_left">Copyright &copy; 2016 - All Rights Reserved - <a href="#">NWLeaderBoards.com</a></p>
    <p class="fl_right">Template by <a href="http://www.os-templates.com/" title="Free Website Templates">OS Templates</a></p>
  </footer>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script type="text/javascript" src="scripts/lbbuttons.js" charset="utf-8"></script>
</html>
