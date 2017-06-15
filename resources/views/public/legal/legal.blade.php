<?php
use yii\helpers\Url;
use yii\helpers\Html;

?>

        <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
    <link href='/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Legal</title>
    <style>


        body {
            font-family: 'Calibri';
            margin-top: 15px;
        }

        .list-item {
            display: list-item;
            list-style: disc;
            margin-left: 2em;
        }

        .bold {
            font-weight: bold;
        }

    </style>
    <script>
        $(document).ready(function () {
            var tos = $("#tos");
            var pp = $("#privacy");
            var eula = $("#eula");

            var btn_tos = $(".btn-tos");
            var btn_pp = $(".btn-pp");
            var btn_eula = $(".btn-eula");

            btn_tos.click(function () {
                tos.show();
                pp.hide();
                eula.hide();
            });

            btn_pp.click(function () {
                tos.hide();
                pp.show();
                eula.hide();
            });

            btn_eula.click(function () {
                tos.hide();
                pp.hide();
                eula.show();
            });
        });
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group btn-group-justified" role="group" aria-label="Justified button group">
                <a href="javascript:;" class="btn btn-default btn-tos"
                   role="button">Terms Of Use</a>
                <a href="javascript:;" class="btn btn-default btn-pp"
                   role="button">Privacy Police</a>
                <a href="javascript:;" class="btn btn-default btn-eula" role="button">EULA</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" id="tos">
            <?php echo view("public.legal.terms-of-use-content")->render() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" id="privacy" style="display:none">
            <?php echo view("public.legal.privacy-policy-content")->render() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" id="eula" style="display:none">
            <?php echo view("public.legal.eula-content")->render() ?>
        </div>
    </div>
</div>
</body>
</html>