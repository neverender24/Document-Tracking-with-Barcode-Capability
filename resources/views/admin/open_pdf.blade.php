<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
        <div>
            <?php
            if(isset($_GET['id'])){
                echo DNS1D::getBarcodeSVG( $_GET['id'], "C128",2,75);
            }
            ?>
        </div>
    </div>
    <div class="col-lg-5 col-md-6 col-sm-6 text-right">
        <div class="row">
        <div class="col-4">
            <p>Scan the QR code using smartphone if you have mobile data.</p>
        </div>
        <div class="col-6 text-left">
            <?php
                if(isset($_GET['id'])){
                    echo DNS2D::getBarcodeSVG("http://122.54.19.170:8081/track/".$_GET['id'], "QRCODE",3,3);
                }
            ?>
        </div>
        </div>
    </div>
</div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
            <span style="position:fixed; margin:7px 0px 0px 570px; font-size:1.3em;"><b>{{ $_GET['id'] }}</b></span>
            <span style="position:fixed; margin:85px 0px 0px 75px; width: 700px; text-align: left;">{{ $_GET['title'] }}</span>
            <span style="position:fixed; margin:35px 0px 0px 500px;">{{ $_GET['office'] }}</span>
            <span style="position:fixed; margin:35px 0px 0px 80px;">{{ $_GET['name'] }}</span>
            <img src="{{asset('form.jpg')}}" class="img-fluid" />
        <div class="col-lg-3 col-md-6 col-sm-6 text-right">
    </div>
