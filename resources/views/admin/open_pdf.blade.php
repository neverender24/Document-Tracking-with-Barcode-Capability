<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<style>
    body {
        background-color: #fff;
    }

    .nopadding {
        padding: 0 !important;
        margin: 0 !important;
    }
</style>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 text-right nopadding">
        <div>
            <?php
            if(isset($_GET['id'])){
                echo DNS1D::getBarcodeSVG( $_GET['id'], "C128",2,90);
            }
            ?>
        </div>
    </div>
    <div class="col-lg-5 col-md-6 col-sm-6 text-right nopadding">
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
        <span style="position:fixed; margin:15px 0px 0px 800px; font-size:1.3em;"><b>{{ $_GET['id'] }}</b></span>
        <span
        {{-- subject --}}
            style="position:fixed; margin:115px 0px 0px 110px; width: 700px; text-align: left;font-size:1.1em;">{{ $_GET['title'] }}</span> 
        <span style="position:fixed; margin:50px 0px 0px 600px;font-size:1.1em;">{{ $_GET['office'] }}</span>
        {{-- from --}}
        <span style="position:fixed; margin:50px 0px 0px 110px;font-size:1.1em;">{{ $_GET['name'] }}</span>
        <img src="{{asset('form.jpg')}}" style="width: 1024px;"/>
        <div class="col-lg-3 col-md-6 col-sm-6 text-right">
        </div>
    </div>