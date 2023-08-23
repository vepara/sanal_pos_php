@extends('layouts.app')
<div class="image-container set-full-height">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card">

                        <div class="icon-circle" style="border: 3px solid green; margin-top:10px">
                            <i class="ti-check" style="color: green"></i>
                        </div>
                        <h4 class="info-text"> Tebrikler!</h4>
                        <h5 class="text-center"> Ödeme işleminiz başarıyla tamamlandı.</h5>
                        <div class="col-sm-12 text-center">
                            <div class="col-sm-4 col-sm-offset-4" style="background-color:cadetblue; padding: 20px; border-radius: 8px">
                                <label>İşlem Referans Numaranız</label><br />
                                <label>{{$data['order_no']}} </label>
                            </div>
                        </div>
                        <br />
                        <div class="col-sm-12 text-center">
                            <label>
                                Ödeme işleminin detayı için e-posta adresinizi girerek gönder butonunu tıklayınız.
                            </label>
                            <br />

                            <div class="col-sm-4 col-sm-offset-4">
                                <form action="{{url('send-email')}}" method="post" id="emailForm">
                                    @csrf
                                    <input name="InvoiceId" id="InvoiceId" type="hidden" class="form-control" value="{{$data['invoice_id']}}">
                                    <div class="form-group">
                                        <input name="email" id="email" type="email" class="form-control" required placeholder="E-Posta...">
                                    </div>
                                    <input type='submit' class='btn btn-finish btn-fill btn-primary btn-wd' value='Gönder' />
                                    <br />
                                    <br />
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


