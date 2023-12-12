<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<link rel="stylesheet" href="{{ asset('backend/pdf/pdf_print.css') }}">
<body class="skin-blue sidebar-mini">
    <div class="mainBody">
        <?php
        // $company_data = $this->super->getRow('company_information', ['id' => 1]);
        // $image_name = $company_data->image ? $company_data->image : 'demo_logo.png';
        // $url = base_url('assets/logo/' . $image_name);
        ?>
        <div class="header_print">
            <div class="header_logo">
                {{-- <img src="{{ asset(@$store->logo)}}" class="header_logo_img"> --}}
            </div>
            <div class="header_address">
                <span class="header_address_text_1">company_name</span> <br>
                <span class="header_address_text_2">location</span><br>
                <span class="header_address_text_3">Mobile:  </span> <br>
                <span class="header_address_text_4">Email: </span><br>
                <span class="header_address_text_4">Website: </span><br>
                <span class="header_address_text_4">Address :  </span>
            </div>
        </div>
