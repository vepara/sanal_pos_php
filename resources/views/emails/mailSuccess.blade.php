<!DOCTYPE html>
<html>
<head>
    <title>Mail</title>
</head>
<body>

<table style="max-width:600px;width:600px;margin-left:auto;margin-right:auto;font-family:Arial;background-color:#f9f9f9;padding:15px">
    <tr style="background-color:#fff;">
        <td style="color:#4cbc89;font-family:Arial;font-weight:300;padding:25px 10px"><br /><span style="font-size:25px"><b>TEŞEKKÜRLER !</b><span></td>
    </tr>
    <tr><td><br /></td></tr>
    <tr style="background-color:#fff;">
        <td style="font-family:Arial;font-weight:300;padding:25px 10px">
            <span style="font-size:16px;color:#4cbc89;"><b>Merhaba </b></span>
            <p style="font-family:Arial;text-align:justify;color:#525252"> {{\Illuminate\Support\Carbon::now()->addHours(3)}} tarihinde yapmış olduğunuz ödeme işleminin detayları aşağıdaki gibidir;</p>
            <p style="font-family:Arial;text-align:justify;color:#525252"><b>İşlem Referans No:</b>{{$data->order_id}}</p>
            <p style="font-family:Arial;text-align:justify;color:#525252"><b>İşlem Tutarı:</b> {{$data->transaction_amount}} ₺</p>
            <p style="font-family:Arial;text-align:justify;color:#525252"><b>Invoice ID:</b> {{$data->invoice_id}}</p>
            <p style="font-family:Arial;text-align:justify;color:#525252"><b>Satıcı Komisyon:</b> {{$data->merchant_commission}}</p>
            <p style="font-family:Arial;text-align:justify;color:#525252"><b>Kullanıcı Komisyon:</b> {{$data->user_commission}}</p>
        </td>
    </tr>
    <tr>
        <td>
            <br />
            <h2 style="text-align:center;font-family:Arial;font-weight:600;line-height:0"><a href="tel:08502418297" style="color:#4cbc89;text-decoration:none">0850 241 82 97</a></h2>
            <h4 style="text-align:center;font-family:Arial;font-weight:300;line-height:0"><a href="https://www.vepara.com.tr/" target="_blank" style="color:#4cbc89;text-decoration:none">www.vepara.com.tr</a></h4>
        </td>
    </tr>
    <tr style="text-align:center">
        <td>
            <p style="font-family:Arial;text-align:justify;color:#969696;text-align:center;font-size:13px">
                Kısıklı Mah. Ferah Cd.No:1/3 Üsküdar
                <br />İstanbul
            </p>
        </td>
    </tr>
</table>
</body>
</html>
