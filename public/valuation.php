<!DOCTYPE html>
<html>
   <head>
      <title>Hello!</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
      <!-- Latest compiled and minified CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
      <style>
      .row
      {
        margin-bottom:20px;
      }

      </style>
   </head>
   <body>
      <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <a href="" class="btn btn-primary btn-block">Reset</a>
            </div>
        </div>

         <form action="valuation.php" method="post">
         <div class="row">
             <div class="col-sm-12">
                <h2>Calculate percentage investor will get</h2>
                <?php
                 if(isset($_POST["investor_percentage"]))
                 {
                    $postmoney=$_POST["amountraised"]+$_POST["premoney"];
                    $percentage=$_POST["amountraised"]/$postmoney;
                    $percentage=round($percentage*100,2);
                    echo "<h1>$percentage%</h1>";
                 }
                 ?>
                <!--<br>Formula:<br>
                Post Money=Pre-money+Amount Raised<br>
                Percentage=Amount Raised/Post Money<br>-->
                <div class="form-group">
                   <input type="text" class="form-control" name="premoney" placeholder="Pre-money valuation" value="<?=isset($_POST["premoney"])?$_POST["premoney"]:NULL;?>" required>
                </div>
                <div class="form-group">
                   <input type="text" class="form-control" name="amountraised" placeholder="Amount Raised" value="<?=isset($_POST["amountraised"])?$_POST["amountraised"]:NULL;?>" required>
                </div>

                <button type="submit" class="btn btn-success" name="investor_percentage">Submit</button>
             </div>
         </div>
         </form>

         <form action="valuation.php" method="post">
         <div class="row">
             <div class="col-sm-12">
                <h2>Post money valuation</h2>
                <?php
                 if(isset($_POST["post_money_val"]))
                 {
                    $valuation=$_POST["amountraised"]/($_POST["percentage"]/100);
                    $valuation=round($valuation,2);
                    echo "<h1>".number_format($valuation,2)."</h1>";
                 }
                 ?>
                <!--<br>Formula:<br>
                Valuation=Amount Raised / Percentage<br>-->
                <div class="form-group">
                   <input type="text" class="form-control" name="amountraised" placeholder="Amount Raised" value="<?=isset($_POST["amountraised"])?$_POST["amountraised"]:NULL;?>" required>
                </div>
                <div class="form-group">
                   <input type="text" class="form-control" name="percentage" placeholder="Percentage" value="<?=isset($_POST["percentage"])?$_POST["percentage"]:NULL;?>" required>
                </div>

                <button type="submit" class="btn btn-success" name="post_money_val">Submit</button>
             </div>
         </div>
         </form>


        <h2>Percentage taking from shareholders</h2>
        <?php
        $end=19;
        echo '<form action="valuation.php" method="post">';
        $percentage_to_give=isset($_POST["percentage_to_give"])?$_POST["percentage_to_give"]:NULL;
        echo '<input type="text" class="form-control" name="percentage_to_give" value="'.$percentage_to_give.'" placeholder="Percentage To Give">';
        echo '<br>';
        for($i=0;$i<=$end;$i++)
        {
            $shareholder_precentage="";
            if(isset($_POST["share_percentage"]) && isset($_POST["percentage_to_give"]))
            {
                $shareholder_precentage=$_POST["share_percentage"][$i];
                $calculation=$percentage_to_give*($shareholder_precentage/100);
                echo  "<b>You give: ".round($calculation,2)."%</b>";
            }

            echo '<input type="text" class="form-control" name="share_percentage[]" value="'.$shareholder_precentage.'" placeholder="Percentage" >';
            echo "<br>";
        }
        echo '<button type="submit" class="btn btn-success" name="calculate_taking">Submit</button>';
        echo '</form>';
        ?>
      </div>
   </body>
</html>
