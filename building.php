<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Page Not Found</title>
<style type="text/css">
    body {
        background-color: #f5f5f5;
        margin-top: 8%;
        color: #5d5d5d;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        text-shadow: 0px 1px 1px rgba(255,255,255,0.75);
        text-align: center !important;
    }

    h1 {
        font-size: 2.45em;
        font-weight: 700;
        color: #5d5d5d;
        letter-spacing: -0.02em;
        margin-bottom: 30px;
        margin-top: 30px;
    }

    .container {
        width: 100%;
        margin-right: auto;
        margin-left: auto;
    }

    .animated {
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    .fadeIn {
        -webkit-animation-name: fadeIn;
        animation-name: fadeIn;
    }

    .info {
        color:#5594cf;
        fill:#5594cf;
    }

    .error {
        color:#c92127;
        fill:#c92127;
    }

    .warning {
        color:#ffcc33;
        fill:#ffcc33;
    }

    .success {
        color:#5aba47;
        fill:#5aba47;
    }

    .icon-large {
        height: 132px;
        width: 132px;
    }

    .description-text {
        color: #707070;
        letter-spacing: -0.01em;
        font-size: 1.25em;
        line-height: 20px;
    }

    .footer {
        margin-top: 40px;
        font-size: 0.7em;
    }

    .delay-1s {
        -webkit-animation-delay: 1s;
        animation-delay: 1s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

</style>
</head>
<body>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <div class="animated fadeIn">
                <svg class="info icon-large fa-question-circle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M208 64a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM9.8 214.8c5.1-12.2 19.1-18 31.4-12.9L60.7 210l22.9-38.1C99.9 144.6 129.3 128 161 128c51.4 0 97 32.9 113.3 81.7l34.6 103.7 79.3 33.1 34.2-45.6c6.4-8.5 16.6-13.3 27.2-12.8s20.3 6.4 25.8 15.5l96 160c5.9 9.9 6.1 22.2 .4 32.2s-16.3 16.2-27.8 16.2H288c-11.1 0-21.4-5.7-27.2-15.2s-6.4-21.2-1.4-31.1l16-32c5.4-10.8 16.5-17.7 28.6-17.7h32l22.5-30L22.8 246.2c-12.2-5.1-18-19.1-12.9-31.4zm82.8 91.8l112 48c11.8 5 19.4 16.6 19.4 29.4v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V405.1l-60.6-26-37 111c-5.6 16.8-23.7 25.8-40.5 20.2S-3.9 486.6 1.6 469.9l48-144 11-33 32 13.7z"/>
				</svg>
            </div>
            <h1 class="animated fadeIn"></h1>
            <div class="description-text animated fadeIn delay-1s">
                <p>Эта страница еще в работе.</p>
                <p>Свяжитесь с администратором, если она вам нужна.</p>
                <!--<section class="footer"><strong>Error Code:</strong> 404</section>-->
            </div>
        </div>
    </div>
</div>
</body>
</html>
