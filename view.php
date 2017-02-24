<html>
<head>
    <title>Euro Parts - Inventory</title>
    <link rel='stylesheet' href='css/viewer.css'>
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script type="text/javascript" charset="utf8" src="js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        function timer() {
            var t;
            //window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;

            function logout() {
                alert("You are now logged out.")
                //location.href = 'logout.php'
            }

            function resetTimer() {
                clearTimeout(t);
                t = setTimeout(logout, 3000)
                // 1000 milisec = 1 sec
            }
        };
    </script>
    <style  type="text/css">
        #additem  {
            position: relative;
            top: -3px;
            left: -21px;
            box-shadow:
                0px 0px 0px 0.5px rgba(0,0,0,0.6),
                0px 0px 0px 0.5px #fff,
                0px 0px 0px 0.5px rgba(0,0,0,0.2),
                6px 6px 8px 0.5px #555;
            cursor: pointer;
        }
        #reset  {
            position: relative;
            top: -3px;
            left: -13px;
            box-shadow:
                0px 0px 0px 0.5px rgba(0,0,0,0.6),
                0px 0px 0px 0.5px #fff,
                0px 0px 0px 0.5px rgba(0,0,0,0.2),
                6px 6px 8px 0.5px #555;
            cursor: pointer;

        }
        #collate  {
            position: relative;
            top: -3px;
            left: -5px;
            box-shadow:
                0px 0px 0px 0.5px rgba(0,0,0,0.6),
                0px 0px 0px 0.5px #fff,
                0px 0px 0px 0.5px rgba(0,0,0,0.2),
                6px 6px 8px 0.5px #555;
            cursor: pointer;

        }
        .op {
            cursor: pointer;
        }
    </style>
</head>
</html>

<?php
/**
 * Created by PhpStorm.
 * User: MisterCray
 * Date: 11/5/2016
 * Time: 12:46 AM
 */

session_start();

//Check for logged in session
if(isset($_SESSION['user']))    {
    echo "<div class='wrapper'>
            <div class='load'>
                <center>
                    <h1 ><br/>Loading...</h1><br/>
                    <img src='images/hourglass.svg' />
                </center>
            </div>";

    //Include necessary files
    require("includes/settings.php");
    require("includes/functions.php");

    //Query to retrieve rows from database
    $query = "SELECT * FROM ".$_SESSION['table'];
    $fetch_row = mysqli_query($db_connect, $query);

    echo "<div class='header' style='display: none'>
            <div style='font-size: 40px;'><center>Euro Parts Inventory</div>
            <br/><h2><u><center><div>Inventory table Name: ".$_SESSION['user']."</div></u>";

    //Buttons with options available only to users of type admin
    if($_SESSION['user'] == 'admin')
        echo "<img id='collate' title='Collate all inventory tables' align='right' src='images/collate2.png' style='height: 40px; width: 40px;' />
              <img id='reset' title='Reset stock count for all tables' align='right' src='images/reset.png' style='height: 40px; width: 40px;' />";

    //Button to add new item to the database
    echo "<img id='additem' title='Add new item to the Database' align='right' src='images/additem.png' style='height: 40px; width: 40px;' />
          </h2></u>
          </div><br/>";

    //Set up table
    echo "<table border ='1' id='data' style='display: none;' data-page-length='15' >";
    echo "<thead><tr>   <th>S/N</th>
                        <th>Page</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Net Quantity</th>
                        <th>Delivery Quantity</th>
                        <th>Balance Quantity</th>
                        <th>Stock</th>
                        <th>Count</th>
    
                 </tr>
          </thead>
          <tbody>";

    //Print table contents
    while ($row = mysqli_fetch_array($fetch_row)) {
        echo "<tr style='color: black;'>
    
                    <td>" . $row['id'] . "</td>
                    <td>" . $row['page'] . "</td>
                    <td>" . $row['productid'] . "</td>
                    <td>" . $row['productname'] . "</td>
                    <td>" . $row['netqty'] . "</td>
                    <td>" . $row['delqty'] . "</td>
                    <td>" . $row['balqty'] . "</td>  
                    <td id='counts".$row['id']."'>" . $row['count'] . "</td>                             
                    <td>
                        
                           <input type='text' id='".$row['id']."' placeholder='Count' style='height:25px;width:69px;font-size:16px'/>
                           <img class='op' id='=_".$row['id']."' title='Set quantity equal to entered value' src='images/equals.png' style='height:25px;width:25px;' />
                           <img class='op' id='+_".$row['id']."' title='Add the entered value to the existing quantity' src='images/plus.png' style='height:25px;width:25px;' />
                           <img class='op' id='-_".$row['id']."' title='Subtract the entered value from the existing quantity' src='images/minus.png' style='height:25px;width:25px;' />
                           <img class='notes' id='notes_".$row['id']."' title='Add a comment for this item' src='images/notes.png' style='height:25px;width:25px;' />
                           <br/><span id='erralpha".$row['id']."' style='display:none; color: red;'></span>
                           <div id='notepad' style='display:none'></div>
                        
                    </td>        
                               
             </tr>";
    }
    echo "</tbody></table></div>";

    //Once everything loads, disable loader and display table
    echo "<script language='JavaScript' type='text/javascript'>            
            setTimeout(function() {
                $('table').dataTable( {
                    'lengthChange': false,
                    'processing': true,
                });
                $('.header').show();
                $('#data').show();                        
                $('.load').hide();    
                //timer();
            }, 2000);         
         </script>
         <div id='newitems'></div>";
}

else    {
    header("Location: error.html");
    exit();
}

?>

<script src='js/viewer.js'></script>



