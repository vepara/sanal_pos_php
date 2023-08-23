<!DOCTYPE html>
<html>
<head>
    <title>Mail</title>
</head>
<body>
<tr style="background-color:#fff;">
    <td style="font-family:Arial;font-weight:300;padding:25px 10px">
        <span style="font-size:16px;color:#4cbc89;"><b>Merhaba </b></span>
{{--        <p style="font-family:Arial;text-align:justify;color:#525252">{{$data['payment_date']}} tarihinde yapmış olduğunuz ödeme işleminin detayları aşağıdaki gibidir;</p>--}}
        <p style="font-family:Arial;text-align:justify;color:#525252"><b>İşlem Referans No:</b>{{$data->order_id}}</p>
{{--        <p style="font-family:Arial;text-align:justify;color:#525252"><b>İşlem Tutarı:</b> {{$data['transaction_amount']}} ₺</p>--}}
        <p style="font-family:Arial;text-align:justify;color:#525252"><b>Invoice ID:</b> {{$data->invoice_id}}</p>
    </td>
</tr>
</body>
</html>
