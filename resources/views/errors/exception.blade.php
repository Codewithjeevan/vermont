<!DOCTYPE html>
<html>
<head>
    <title>Something Wrong happened</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link href="{{ asset('fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            color: #B0BEC5;
            font-family: 'Lato', sans-serif;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 72px;
            margin-bottom: 40px;
        }

        .report-link{
            color: #B0BEC5;
            font-family: 'Lato', sans-serif;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div>
                <i class="fa fa-flip-vertical" style="margin-right: -3%;">
                    <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                </i>
                <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
            </div>
            <div style="margin-top: -2%;">
                <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
            </div>
            <div class="title"><i><i class="fa fa-frown-o" aria-hidden="true"></i><span class="sr-only">:(</span></i> Ops! Something went wrong</div>
            <h4>Before reporing please check your internet connection and try to reproduce it. If it is happening more than once report to administrator</h4>
            <h4>Please mention scenario how it happened, add url, date and time.</h4>
            <a title="This will open your mail client" class="report-link" href="mailto:retailsalesac@gmail.com"><h4>Click here to report to administrator</h4></a>
            <a title="This will open your mail client" class="report-link" href="{{ url("/home") }}"><h4>Go back</h4></a>
        </div>
    </div>
</body>
</html>
