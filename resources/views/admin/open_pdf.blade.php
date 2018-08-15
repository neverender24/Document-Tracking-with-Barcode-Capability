<?php
/*  $filename = public_path()."/routing_slip_form.pdf";
    header("Content-type: application/pdf");
    header("Content-Length: " . filesize($filename));
    readfile($filename);
    exit; */

    if(isset($_GET['id'])){
        echo DNS1D::getBarcodeSVG( $_GET['id'], "C128",2,75);
        echo $_GET['id'];
    }

?> 


<div class="container" style="width:700px">
    <span style="position:fixed; margin:85px 0px 0px 75px;">{{ $_GET['title'] }}</span>
    <span style="position:fixed; margin:40px 0px 0px 500px;">{{ $_GET['office'] }}</span>
    <span style="position:fixed; margin:40px 0px 0px 80px;">{{ $_GET['name'] }}</span>
   <img src="{{asset('form.jpg')}}"/>
</div>