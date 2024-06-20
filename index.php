<?php

$call_regex = '/^([A-Z0-9]+[\/])?([A-Z][0-9]|[A-Z]{1,2}|[0-9][A-Z])([0-9]|[0-9]+)([A-Z]+)([\/][A-Z0-9]+)?/i';

$loc_regex = '/^([A-R]{2})([0-9]{2})([A-X]{2})$/i';

$db = mysqli_connect(
  'localhost',
  'lyfdqrzlt_fd',
  'lyfdqrzlt_fd',
  'lyfdqrzlt_fd'
);

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LY lauko dienos skelbimai</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    
    <style>
      .map {
        width: 100%;
        height: 100vh;
      }
    </style>
    <script type="module" crossorigin src="/assets/index.php.e3b09573.js"></script>
    <link rel="stylesheet" href="/assets/index.php.5fd94eca.css">
  </head>
  <body>
    <?php 
    
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    if (isset($_POST['confirm']) && (
        $_POST['confirm']=='confirm' || 
        $_POST['confirm']=='change' )
        ) {

      $errors = Array();

      if ( !preg_match($call_regex, $_POST['callsign']) ) {
          $errors[] = "Šaukinys sudarytas neteisingai.";
      }

      if (  !preg_match($loc_regex, $_POST['loc']) ) {
          $errors[] = "Lokatorius turi būti 6 simbolių: KO24ax.";
      }

      if (!$errors) {
        // insert into DB

        $loc_arr = str_split($_POST['loc'],4);
        $loc_arr[0] = strtoupper($loc_arr[0]);
        $loc_arr[1] = strtolower($loc_arr[1]);
        $locator = join('', $loc_arr);

        $insert_query = ($_POST['confirm']=='change'?'REPLACE':'INSERT'). "
           INTO `lyfd_announcements` 
            (`id`, `callsign`, `loc`, 
             `band_50`, `band_144`, `band_432`, `band_shf`, 
             `mode_cw`, `mode_ph`, `mode_digi`, `year`)
        VALUES
            ('', 
              '" . strtoupper($_POST['callsign']) . "', 
              '" . $locator . "', 
              ".(isset($_POST['band_50'])?1:0).", 
              ".(isset($_POST['band_144'])?1:0).", 
              ".(isset($_POST['band_432'])?1:0).", 
              ".(isset($_POST['band_shf'])?1:0).", 
              ".(isset($_POST['mode_cw'])?1:0).", 
              ".(isset($_POST['mode_ph'])?1:0).", 
              ".(isset($_POST['mode_digi'])?1:0).", 
              ". date('Y') . ");
        ";
        $result = mysqli_query($db, $insert_query);
        if (!$result) {
          $errors[] = 'MySQL klaida: ' . mysqli_error($db);
        }
      }

      if ($errors) {
        printf("<h2>Klaidos:</h2>\n");
        foreach ($errors as $error) {
            printf("<li>$error</li>\n");
        }
      }
      else {
        header('Location: /');
      }
    }
    ?>
    <div style="border-color: #cccccc; border: 2px; padding: 5px; background-color: white; float: left; position: absolute; left: 50px; z-index: 100;">
    <form action="/index.php" method="POST">
      <b>LY VHF FD <?php echo date('Y'); ?>:</b>
      <input type="text" name="callsign" id="callsign" placeholder="Callsign">
      <input type="text" name="loc" id="loc" placeholder="KO15ab">
      <br />
      <b>Bands:</b>
      <input name="band_50" id="band_50" type="checkbox">
      <label for="band_50">50MHz,&nbsp;</label>
      
      <input name="band_144" id="band_144" type="checkbox" checked>
      <label for="band_144">144MHz,&nbsp;</label>

      <input name="band_432" id="band_432" type="checkbox">
      <label for="band_432">432MHz,&nbsp;</label>
      
      <input name="band_shf" id="band_shf" type="checkbox">
      <label for="band_shf">1296MHz+&nbsp;</label>
      
      <b>Modes:</b>
      <input name="mode_cw" id="mode_cw" type="checkbox"></label>
      <label for="mode_cw">CW,&nbsp;</label>
      
      <input name="mode_ph" id="mode_ph" type="checkbox"></label>
      <label for="mode_ph">Phone,&nbsp;</label>
      
      <!-- input name="mode_digi" id="mode_digi" type="checkbox"></label>
      <label for="mode_digi">DIGI&nbsp;</label -->
      
      <input type="text" id="confirm" name="confirm" placeholder="įrašyk 'confirm'"></input>
      <input type="submit" value="Announce">
    </form></div>
    <div id="map" class="map"><div id="popup"></div></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/hamgridsquare.js"></script>
    <div class="js-user-location" data-points='{
      "participants": [
    <?php

    $result = mysqli_query($db,
      " SELECT `ly`.*
        FROM `lyfd_announcements` as ly
        WHERE `ly`.`year` = ". date('Y') ." 
        LIMIT 1000
      ",
      MYSQLI_USE_RESULT
    );

    while ($row = $result->fetch_array()) {
      $band_arr = [];
      $mode_arr = [];
      if ($row['band_50']) { $band_arr[] = "\"50\""; }
      if ($row['band_144']) { $band_arr[] = "\"144\""; }
      if ($row['band_432']) { $band_arr[] = "\"432\""; }
      if ($row['band_shf']) { $band_arr[] = "\"1296\""; }
      $bands = join(',', $band_arr );
      
      if ($row['mode_cw']) { $mode_arr[] = "\"CW\""; }
      if ($row['mode_ph']) { $mode_arr[] = "\"PH\""; }
      if ($row['mode_digi']) { $mode_arr[] = "\"DIGI\""; }

      $modes = join(',', $mode_arr );
      
      printf("\t{
          \"callsign\": \"${row['callsign']}\",
          \"loc\": \"${row['loc']}\",
          \"year\": \"${row['year']}\",
          \"bands\": [ ". $bands ." ],
          \"modes\": [ ". $modes ." ]
        },\n");
    }
    
    mysqli_close($db);

    ?>
        {
          "callsign": "LY0AAA",
          "loc": "AA15cl",
          "year": "2024",
          "bands": [
              "144",
              "432",
              "1296"
          ],
          "modes": [
              "CW",
              "SSB",
              "DIGI"
          ]
      }
    ]
}'></div>
  </body>
</html>