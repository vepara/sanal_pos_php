@extends('layouts.app')
<div class="image-container set-full-height">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card">

                        <div class="icon-circle" style="border: 3px solid red; margin-top:10px">
                            <i class="ti-close" style="color: red"></i>
                        </div>
                        <h4 class="info-text"> Hay aksi! Hata kodu:  {{$data['status_code']}}</h4>
                        <h5 class="text-center"> Ödeme işleminde bir hata oluştu. {{$data['status_description']}}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
