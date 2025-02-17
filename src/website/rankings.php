<?php 
    if (isset($_GET['page'])) {
        $pg = $_GET['page'];
        if ($pg == '1') {
            $offset = 0;
        } else {
            $limit = $_GET['count'];
            $offset = ((int)$_GET['page'] - 1)* $limit;
        }
    } else {
        $pg = 1;
        $offset = 0;
        $select = "meta";
        $limit = 25;
        $number = 0;
        $minyear = 1995;
        $maxyear = 2024;
        $searchg= "";
        $searchd= "";
        #$searchp= "";
    }

    if (isset($_GET['scores'])) {
        $select = $_GET['scores'];
        $limit = $_GET['count'];
        $number = $_GET['number'];
        $minyear = $_GET['minyear'];
        $maxyear = $_GET['maxyear'];
        $searchg = $_GET['searchg'];
        $searchd = $_GET['searchd'];
        #$searchp = $_GET['searchp'];
    }
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metacritic Scores</title>
    <link rel="stylesheet" type="text/css" href="style.css??ts=<?=time()?>">
    <script src="script.js??ts=<?=time()?>" defer></script>
</head>
<body>
    <div class="bg-image"></div>
    <div class="header"> 
        <h1>Metacritic Scores</h1>
    </div>
    <form class="filter" name='filter'><span class='filter'>Filter</span>
        <div class='select'>
            <span class='sub' for='filter'>Ranked By:</span>
            <select class='score' name='scores' id='scores'>
                <option class='score' value='meta' <?= ($select == "meta" ? 'selected="selected"' : '') ?>>Metascore</option>
                <option class='score' value='user'<?= ($select == "user" ? 'selected="selected"' : '') ?>>User Score</option>
                <option class='score' value='avg'<?= ($select == "avg" ? 'selected="selected"' : '') ?>>Average Score</option>
            </select>
            <br>
            <span class='sub' for='filter'>Games per Page:</span>
            <select class='count' name='count' id='count'>
                <option class='count' value='25' <?= ($limit == 25 ? 'selected="selected"' : '') ?>>25</option>
                <option class='count' value='50'<?= ($limit == 50 ? 'selected="selected"' : '') ?>>50</option>
                <option class='count' value='100'<?= ($limit == 100 ? 'selected="selected"' : '') ?>>100</option>
                <option class='count' value='250'<?= ($limit == 250 ? 'selected="selected"' : '') ?>>250</option>
                <option class='count' value='500'<?= ($limit == 500 ? 'selected="selected"' : '') ?>>500</option>
                <option class='count' value='1000'<?= ($limit == 1000 ? 'selected="selected"' : '') ?>>1000</option>
            </select>
            <br>
            <span class='sub a' for='filter'>Minimum User Reviews: &nbsp;<span id='minimum'></span></span>
            <div class='range_container'>
                <div class='sliders_control'>  
                    <input id='review' name='number' type='range' step ='25' value=<?=$number?> min='0' max='1000' />         
                </div>
            </div>
            <br>
            <span class='sub' for='filter'>Min Year: &nbsp<span id='demo'></span> &nbsp Max Year: &nbsp<span id='demo2'></span></span>
            <div class='range_container'>
                <div class='sliders_control'>
                    <input id='fromSlider' name='minyear' type='range' value=<?=$minyear?> min='1995' max='2024' />
                    <input id='toSlider' name='maxyear' type='range' value=<?=$maxyear?> min='1995' max='2024' />
                </div>
            </div>
        </div>
        <button class='score'>Submit</button>
        <div class="searchbox">
            <div class="searchg">
                <span class='searchgame'>Search Game</span>
                <input type="text" id="mySearch" name="searchg" value="<?=$searchg?>">
                <button type="submit" onclick="myFunction()"><i class="fa fa-search"></i></button>
            </div>
        </div>
        <div class="searchbox">
            <div class="searchd">
                <span class='searchdev'>Search Developer</span>
                <input type="text" id="input-box" name="searchd" autocomplete="off" value="<?=$searchd?>">
                <button type="submit" onclick="myFunction()"><i class="fa fa-search"></i></button>
            </div> 
            <div class="result-box">
            </div>
        </div>
        <script type="module" src="dev.js?2dev=' + Math.floor(Math.random() * 100) + '"></script>
        <meta name="txt" content="dev"/> 
    </form>
    
    <div class="row">
        <table class="table">
        <thread class="thread-dark">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div>
                <div class="col"></div> 
        </thread>
        
        <tbody>
            <?php
                #Correctly provides the correct color depending on the score of the game
                function colors($score, $length) {
                    $color = [];
                    for ($i = 0; $i < $length; $i++) {
                        if ((float)$score[$i] >= 75) {
                            $color[$i] = "green";
                        }
                        else if((float)$score[$i] == 0) {
                            $color[$i] = "white";
                        }
                        else if ((float)$score[$i] < 50) {
                            $color[$i] = "red";
                        }
                        else {
                            $color[$i] = "yellow";
                        }
                    }
                    return $color;
                }
                
                function color($score) {
                    if ((float)$score >= 75) {
                        $color = "green";
                    }
                    else if((float)$score == 0) {
                        $color = "white";
                    }
                    else if ((float)$score < 50) {
                        $color = "red";
                    }
                    else {
                        $color = "yellow";
                    }
                
                    return $color;
                }
                #Edge case for the last row if the last row has less than 5 games
                function special($links, $names, $images, $metascores, $userscores, $averages) {
                    $mcolor = color($metascores);
                    $ucolor = color(preg_replace("/[^a-zA-Z 0-9]+/", "", $userscores ));
                    $acolor = color($averages);
                    echo "
                        <th>
                            <div class='container'>
                                <a class='link' href='$links' target='_blank'>
                                        <div class='name'>
                                            <span class='name'>$names</span>
                                        </div>
                                        <div class='image'>
                                            <img class='cover blur' src=$images>
                                            <div class='border'></div>    
                                            <img class='back' src=$images>
                                            <div class='scores'>
                                                <span class='meta'> meta </span>
                                                <p class='meta $mcolor'>$metascores</p>
                                                <span class='user'> user </span>
                                                <p class='user $ucolor'>$userscores</p>
                                                <span class='avg'> avg </span>
                                                <p class='avg $acolor'>$averages</p>
                                            </div>
                                        </div>
                                    </a>
                            </div>
                        </th>    
                        ";
                }
                $connect = mysqli_connect('localhost', 'root', 'password', 'database');
                if (!$connect) {
                    die("Connection Error");
                }
                #Filtering options for type of score to display
                if ($select == "user") {
                    $order = "user_score DESC, metascore DESC, id ASC";
                } else if ($select == "avg") {
                    $order = "average_score DESC, metascore DESC, user_score DESC, id ASC";
                } else {
                    $order = "metascore DESC, user_score DESC, id ASC";
                }
                
                $query = "SELECT COUNT(*) FROM metacritic WHERE user_total >= $number and RIGHT(release_date, 4) >= $minyear and RIGHT(release_date, 4) <= $maxyear 
                        and name like '%$searchg%' and developer like '%$searchd%'";
                $result = mysqli_query($connect, $query);
                $num = mysqli_fetch_row($result);
                $pages = ceil($num[0] / $limit);

                $query = "SELECT * FROM metacritic WHERE user_total >= $number and RIGHT(release_date, 4) >= $minyear and RIGHT(release_date, 4) <= $maxyear 
                            and name like '%$searchg%' and developer like '%$searchd%' ORDER by $order LIMIT $limit OFFSET $offset";
                $result = mysqli_query($connect, $query);

                $j = 0;
                $names = $metascores = $userscores = $averages = array();
                #Parses all the data and puts it in an array for keeping
                for ($i = 0; $i < 25; $i++) {
                    while ($j < 5) {
                        $row = mysqli_fetch_array($result);
                        if ($row == null) {
                            $count = count($names);
                            break;
                        }
                        $name = $row['name'];
                        if (strlen($name) > 20) {
                            $fullname[] = "$name";
                            $name = substr($row['name'], 0, 19) . "...";
                        }
                        $metascore = round($row['metascore'], 0);
                        $userscore = $row['user_score'];
                        $average = $row['average_score'];
                        $link = $row['link'];
                        $image = rtrim($row['img'], "/");

                        $names[] = "$name";
                        $metascores[] = "$metascore";
                        $userscores[] = "$userscore";
                        $averages[] = "$average";
                        $links[] = "$link";
                        $images[] = "$image";
                        $j++;
                    }
                    
                    #Edge case for the last row
                    if ($row == null) {
                        for ($i = 0; $i < $count; $i++) {
                                special(array_shift($links), array_shift($names), array_shift($images), 
                                        array_shift($metascores), array_shift($userscores), $averages[$i]);
                        }
                        break;
                    }
                    foreach($userscores as $score) {
                        $temp[] = $score * 10;
                    }
                    $j = 0;
                    $mcolor = colors($metascores, 5);
                    $ucolor = colors($temp, 5);
                    $acolor = colors($averages, 5);            
                    echo "<tr>";
                    for ($i = 0; $i < 5; $i++) { 
                        echo "
                        <th>
                            <div class='container'>
                                <a class='link' href='$links[$i]' target='_blank'>
                                        <div class='name'>
                                            <span class='name'>$names[$i]</span>
                                        </div>
                                        <div class='image'>
                                            <img class='cover blur' src=$images[$i]>
                                            <div class='border'></div>    
                                            <img class='back' src=$images[$i]>
                                            <div class='scores'>
                                                <span class='meta'> meta </span>
                                                <p class='meta $mcolor[$i]'>$metascores[$i]</p>
                                                <span class='user'> user </span>
                                                <p class='user $ucolor[$i]'>$userscores[$i]</p>
                                                <span class='avg'> avg </span>
                                                <p class='avg $acolor[$i]'>$averages[$i]</p>
                                            </div>
                                        </div>
                                    </a>
                            </div>
                        </th>    ";
                        
                    }
                    echo "</tr>";
                    $names = $metascores = $userscores = $averages = $links = $images = array();
                }
            ?>
        </tbody>
        </table>
        </div>
        <div class="pages">
        <center>
            <?php
            #Parses the query in order to retrieve the parameters we want
            function parse() {
                $parsed = parse_url($_SERVER['REQUEST_URI']);
                $query = $parsed['query'];
                
                parse_str($query, $params);
                
                unset($params['page']);
                $select = http_build_query($params);
                
                return $select;
            }
            $query = 'scores=';
            if (isset($_GET['scores']) && parse() != '') {
                $query = parse() . '&';
            } else {
                $query = 'scores=meta&count=25&number=0&minyear=1995&maxyear=2024&searchg=&searchd=&';
            }
            #This part of code is dependent on the query in order to set up the correct number of max pages and pages to display
            $page = 'page=';
            $first_page= $page . '1';
            $last_page = $page . $pages;
            $up = $pg + 1;
            $down = $pg - 1;
            $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            if ($pg != 1) {
                echo " <a class='arrow white left hov' href='$url?$query$page$down'> < </a>";    
            } else {
                echo " <a class='arrow orange left'><</a>";    
            }
            if ($pg < 5) {    
                $down = 1;
                for ($i = 0; $i < $pg - 1; $i++) {
                    echo " <a class='page white' href='$url?$query$page$down'>$down</a>";    
                    $down++;
                }       
                echo "
                    <b class='page black'>$pg</b>";
                if ($pages > 7) {
                    for ($i = $pg; $i < 6; $i++) {
                        echo " <a class='page white' href='$url?$query$page$up'>$up</a>";    
                        $up++;
                    }
                    echo "<span class='ellipsis'>...</span><a class='page white' href='$url?$query$last_page'>$pages</a>";
                } else {
                    for ($i = $pg; $i < $pages; $i++) {
                        echo " <a class='page white' href='$url?$query$page$up'>$up</a>";    
                        $up++;
                    }
                }
            } else if ($pg > 4 && $pg < $pages - 3 && $pages > 7) {
                $down -= 1;
                echo " <a class='page white' href='$url?$query$first_page'>1</a><span class='ellipsis'> ...</span>";
                for ($i = 0; $i < 2; $i++) {
                    echo " <a class='page white' href='$url?$query$page$down'>$down</a>";
                    $down++;
                }
                echo " <b class='page black'>$pg</b>";
                for ($i = 0; $i < 2; $i++) {
                    echo " <a class='page white' href='$url?$query$page$up'>$up</a>";
                    $up++;
                }
                echo "<span class='ellipsis'> ...</span><a class='page white' href='$url?$query$last_page'>$pages</a>";
            } else {
                if ($pages > 7) {
                    echo " <a class='page white' href='$url?$query$first_page'>1</a><span class='ellipsis'> ...</span>";
                    $down = $pages - 5;
                    for ($i = $down; $i < $pg; $i++) {
                        echo " <a class='page white' href='$url?$query$page$down'>$down</a>";    
                        $down++;
                    }       
                } else {
                    $down = 1;
                    for ($i = $down; $i < $pg; $i++) {
                        echo " <a class='page white' href='$url?$query$page$down'>$down</a>";    
                        $down++;
                    }   
                }
                echo "<b class='page black'>$pg</b>";
                $up = $pg + 1;
                for ($i = $pg; $i < $pages; $i++) {
                    echo " <a class='page white' href='$url?$query$page$up'>$up</a>";    
                    $up++;
                }
            }
            if ($pg != $pages) {
                echo " <a class='arrow white right hov' href='$url?$query$page$up'> > </a>";    
            } else {
                echo " <a class='arrow orange right'>></a>";    
            }
            ?>
        </center>
        </div>
        <span class="note"></span>
    </body>
</html>