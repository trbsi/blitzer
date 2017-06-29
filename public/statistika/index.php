<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Blitzer Statistika</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/style.css" rel="stylesheet">
    <link href="bootstrap/css/jquery.datetimepicker.css" rel="stylesheet">

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Blitzer</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Home</a></li>

          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
        <p>
            <form action="index.php" class="form-inline" method="POST">
            <div class="row">
                <div class="col-sm-4">
                    <label>Start date</label>
                    <input type="text" value="<?= isset($_POST["start_date"]) ? $_POST["start_date"] : date("Y-m-01 00:00:00")?>" name="start_date" class="form-control datetimepicker" />
                </div>
                <div class="col-sm-4">
                    <label>End date  </label>
                    <input type="text" value="<?=isset($_POST["end_date"]) ? $_POST["end_date"] : date("Y-m-t 23:59:59")?>" name="end_date" class="form-control datetimepicker"  />
                </div>
                <div class="col-sm-4">
                    <label>Option</label>
                    <?php
                    $users=$pins=$messages=$countrycity=NULL;
                    if(isset($_POST["type"]))
                        switch($_POST["type"])
                        {
                            case "users":
                                $users="selected";
                                break;
                            case "pins":
                                $pins="selected";
                                break;
                            case "messages":
                                $messages="selected";
                                break;
                            case "country-city":
                                $countrycity="selected";
                                break;
                        }
                    ?>
                    <select name="type" class="form-control">
                        <option value="users" <?=$users?>>Users</option>
                        <option value="pins" <?=$pins?>>Pins</option>
                        <option value="messages" <?=$messages?>>Messages</option>
                        <option value="country-city" <?=$countrycity?>>Country/Cities</option>
                    </select>
                </div>
            </div>
            <div class="row" style="margin-top:30px;">
                <input type="submit" class="btn btn-primary btn-block" />
            </div>
            </form>
        </p>

        <div class="row">
            <div class="col-sm-12">
            <?php
            if(isset($_POST["type"]))
            {
                switch($_POST["type"])
                {
                    case "users":
                        require "stat/users.php";
                        break;
                    case "pins":
                        require "stat/pins.php";
                        break;
                    case "messages":
                        require "stat/messages.php";
                        break;
                    case "country-city":
                         require "stat/country-city.php";
                        break;
                }
            }
            if(isset($_GET["query"]))
            {
                switch($_GET["query"])
                {
                    case "city":
                        require "stat/city.php";
                        break;
                }
            }
            ?>
            </div>
        </div>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/jquery.datetimepicker.full.min.js"></script>
    <script>
    $(document).ready(function()
    {
       $('.datetimepicker').datetimepicker({
          format:'Y-m-d H:i:s'
        });
    });
    </script>
  </body>
</html>
