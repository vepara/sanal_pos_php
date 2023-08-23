<?php

namespace App\Http\Controllers\Payment;

use App\Helpers\RequestHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetPosRequest;
use App\Http\Requests\PaymentRequest;
use App\Mapper\Common\GetTokenMapper;
use App\Mapper\Payment\GetPosMapper;
use App\Mapper\Payment\Payment2dMapper;
use App\Requests\Common\GetTokenRequest;
use App\Requests\Payment\Payment2dRequest;
use App\Requests\Payment\Payment3dRequest;
use App\Requests\Payment\GetPosRequest as PosRequest;
use GuzzleHttp\Client;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    private GetTokenRequest $getTokenRequest;
    private Payment2dRequest $payment2dRequest;
    private Payment3dRequest $payment3dRequest;
    private PosRequest $posRequest;
    public function __construct(GetTokenRequest $getTokenRequest,Payment2dRequest $payment2dRequest,Payment3dRequest $payment3dRequest,PosRequest $posRequest)
    {
        $this->getTokenRequest = $getTokenRequest;
        $this->payment2dRequest = $payment2dRequest;
        $this->payment3dRequest = $payment3dRequest;
        $this->posRequest = $posRequest;
    }
    public function getToken()
    {
        $apiUrl = getenv('BASE_URL').'token';

        $this->getTokenRequest->setAppId(getenv('APP_ID'));
        $this->getTokenRequest->setAppSecret(getenv('APP_SECRET'));

        $body = $this->getTokenRequest->getTokenData();
        $client = new Client();
        if (Session::has('token') ) {
            return Session::get('token');
        }
        else{
            try {
                $response = $client->post($apiUrl, [
                    'body' => $body,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ]
                ]);
                $data = GetTokenMapper::map($response);
                $token = $data->getData()->getToken();
                $dataArray = $data->toArray();
                $statusCode = $data->getStatusCode();
                if ($statusCode === 100) {
                    Log::channel('info')->info('Token has been created.',['Token' => $token]);
                    Session::put('token',$token);
                    Session::save();
                    return $dataArray;
                } else {
                    Log::channel('error')->error('An error occured when creating token. Error code: ',[$statusCode]);
                    return response()->json(['message' => 'An error occured when creating token. Error code: ' . $statusCode]);
                }
            }catch (\Exception $e){
                Log::channel('error')->error('An error occured when creating token. Error code: ',[$e->getTrace()]);
                return response()->json(['message' => 'An error occured when creating token. Error code: ' . $e->getMessage()]);
            }
        }
    }

    /**
     * @param PaymentRequest $request
     * @return \Illuminate\Http\JsonResponse|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPayment3d(PaymentRequest $request)
    {
        $apiUrl = getenv('BASE_URL').'paySmart3D';

        $validatedData = $request->validated();

        RequestHelper::payment3dRequest($this->payment3dRequest,$validatedData);

        $itemRequestData = $this->getItemRequestData($request);

        $this->payment3dRequest->setItems($itemRequestData);

        $body = $this->payment3dRequest->toArray();

        $client = new Client();
        try {
            $response = $client->post($apiUrl, [
                'body' => json_encode($body),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . Session::get('token')
                ]
            ]);
            if ($response->getStatusCode() === 200) {
                Log::channel('info')->info('3D successful.');
                return $response->getBody();
            } else {
                Log::channel('error')->error('3D Payment failed.');
                return response()->json(['message' => 'Ödeme işlemi başarısız.']);
            }
        } catch (\Exception $e) {
            // İstek sırasında bir hata oluşursa
            Log::channel('error')->error('An error occurred during the payment process: ',[$e->getTrace()]);
            return response()->json(['message' => 'An error occurred during the payment process: ' . $e->getMessage()]);
        }
    }

    /**
     * @param PaymentRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPayment2d(PaymentRequest $request)
    {
        $apiUrl = getenv('BASE_URL').'paySmart2D';

        $validatedData = $request->validated();

        RequestHelper::payment2dRequest($this->payment2dRequest,$validatedData);

        $itemRequestData = $this->getItemRequestData($request);
        $this->payment2dRequest->setItems($itemRequestData);

        $body = $this->payment2dRequest->toJson();

        $client = new Client();
        try {
            $response = $client->post($apiUrl, [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . Session::get('token'),
                ]
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            //$data = Payment2dMapper::map($response);
            //$dataArray = $data->toArray();
            Session::put('data2d',$responseData);
            if($responseData['status_code'] === 100) {
                Log::channel('info')->info('2D Successful.',[$responseData]);
                return redirect()->route('success-page-2d');
            } else {
                Log::channel('error')->error('2D Payment failed.',[$responseData['status_code']]);
                return redirect()->route('error-page-2d');
            }
        } catch (\Exception $e) {
            // İstek sırasında bir hata oluşursa
            Log::channel('error')->error('An error occurred during the payment process: ',[$e->getTrace()]);
            return response()->json(['message' => 'An error occurred during the payment process: ' . $e->getMessage()]);
        }
    }

    /**
     * @param GetPosRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPos(GetPosRequest $request)
    {
        $this->getToken();
        $tokenValue = Session::get('token');
        $apiUrl = getenv('BASE_URL').'getpos';
        $client = new Client();
        $validatedData = $request->validated();
        RequestHelper::getPosRequest($this->posRequest,$validatedData);
        $body = $this->posRequest->getData();

        try {
            $response = $client->post($apiUrl,[
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $tokenValue,
                ]
            ]);

            $data = GetPosMapper::map($response);
            $status_code = $data->getStatusCode();
            if($status_code === 100) {
                $responseData = json_decode($response->getBody(),true);
                Log::channel('info')->info('Get Pos successful:',$data->toArray());
                return $responseData;
            }else {
                Log::channel('error')->error('Get Pos process failed.');
                return response()->json(['message' => 'Get Pos process failed.']);
            }
        }catch (\Exception $e){
            Log::channel('error')->error('An error occurred during the process: ',[$e->getTrace()]);
            return response()->json(['message' => 'An error occurred during the process: ' . $e->getMessage()]);
        }
    }

    /**
     * @param PaymentRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processPayment(PaymentRequest $request)
    {
        Session::put('is_visit',false);
        $validatedData = $request->validated();
        $amount = $validatedData['amount'];
        $name = $validatedData['name'];
        $phone = $validatedData['phone'];
        $tckn = $validatedData['tckn'];
        $ccHolderName = $validatedData['cc_holder_name'];
        $ccNo = $validatedData['cc_no'];
        $expiryMonth = $validatedData['expiry_month'];
        $expiryYear = $validatedData['expiry_year'];
        $cvv = $validatedData['cvv'];
        $installmentNumbers = $validatedData['installments_number'];

        Log::channel('info')->info('Payment Request Data:', ['Amount: ' => $amount, 'Phone Number: ' => $phone, 'Name: ' => $name, 'Identity Number: ' => $tckn,'Cc Holder Name: ' => $ccHolderName, 'Card Number: ' => $ccNo, 'Expiry Month: ' => $expiryMonth, 'Expiry Year: ' => $expiryYear , 'CVV' => $cvv, 'Installments Number' => $installmentNumbers]);
        $is3D = $request->has('3d_checkbox');
        Session::put('email_sent','');
        if ($is3D) {
            Session::put('is_3d',true);
            Log::channel('info')->info('3D Selected.');
            return $this->processPayment3d($request);
        } else {
            Session::put('is_3d',false);
            Log::channel('info')->info('2D Selected.');
            return $this->processPayment2d($request);
        }
    }

    public function error3d()
    {
        $requestData = [
            'order_no' => \request()->get('order_no'),
            'order_id' => \request()->get('order_id'),
            'invoice_id' => \request()->get('invoice_id'),
            'status_code' => \request()->get('status_code'),
            'status_description' => \request()->get('status_description'),
            'credit_card_no' => \request()->get('credit_card_no'),
            'transaction_type' => \request()->get('transaction_type'),
            'payment_status' => \request()->get('payment_status'),
            'payment_method' => \request()->get('payment_method'),
            'error_code' => \request()->get('error_code'),
            'error' => \request()->get('error'),
            'auth_code' => \request()->get('auth_code'),
            'hash_key' => \request()->get('hash_key'),
            'original_bank_error_code' => '',
            'original_bank_error_description' => ''
        ];
        Session::put('data3d',$requestData);
        Log::channel('error')->error('Error Page Request Data:',$requestData);

        return redirect()->route('error-page');
    }

    public function errorPage3d()
    {
        $data = Session::get('data3d');
        if(Session::get('is_visit') === false){
            Session::put('is_visit',true);
            return view('error',compact('data'));
        }
        else{
            return redirect()->route('index');
        }
    }

    public function success3d()
    {
        $requestData = [
            'order_no' => \request()->get('order_no'),
            'order_id' => \request()->get('order_id'),
            'invoice_id' => \request()->get('invoice_id'),
            'status_code' => \request()->get('status_code'),
            'status_description' => \request()->get('status_description'),
            'credit_card_no' => \request()->get('credit_card_no'),
            'transaction_type' => \request()->get('transaction_type'),
            'payment_status' => \request()->get('payment_status'),
            'payment_method' => \request()->get('payment_method'),
            'error_code' => \request()->get('error_code'),
            'error' => \request()->get('error'),
            'auth_code' => \request()->get('auth_code'),
            'hash_key' => \request()->get('hash_key'),
            'original_bank_error_code' => '',
            'original_bank_error_description' => ''
        ];
        Session::put('data3d',$requestData);
        Log::channel('info')->info('Success Page Request Data:',$requestData);

        return redirect()->route('success-page');
    }

    public function successPage3d()
    {
        $data = Session::get('data3d');
        if(Session::get('is_visit') === false){
            Session::put('is_visit',true);
            return view('success',compact('data'));
        }
        else{
            return redirect()->route('index');
        }
    }

    public function successPage2d()
    {
        $data2d = Session::get('data2d');
        $data = $data2d['data'];
        if(Session::get('is_visit') === false){
            Session::put('is_visit',true);
            return view('success',compact('data'));
        }
        else{
            return redirect()->route('index');
        }
    }

    public function errorPage2d()
    {
        $data = Session::get('data2d');
        if(Session::get('is_visit') === false){
            Session::put('is_visit',true);
            return view('error',compact('data'));
        }
        else{
            return redirect()->route('index');
        }
    }

    public function index()
    {
        Session::put('email_input',false);
        return view('index');
    }

    /**
     * @param float $total
     * @return array
     */
    public function getItemRequestData(PaymentRequest $request): array
    {

        $this->items = [
            [
                'name' => $request->name,
                'price' => (float)$request->total,
                'quantity' => 1,
                'description' => $request->description
            ]
        ];

        $itemRequestData = [];

        foreach ($this->items as $item) {
            $itemData = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'description' => $item['description']
            ];

            $itemRequestData[] = $itemData;
        }
        return $itemRequestData;
    }

    public function sendMail(Request $request)
    {
        $apiUrl = getenv('BASE_URL') . 'checkstatus';
        $email = $request->input('email');
        $client = new Client();

        $checkStatusRequestData = [
            'invoice_id' => Session::get('invoice_id'),
            'merchant_key' => getenv('merchant_key')
        ];

        $responseBody = $this->sendApiRequest($apiUrl, $checkStatusRequestData, $client);

        $status_code = json_decode($responseBody)->status_code;
        if ($status_code === 100) {
            Session::put('response', json_decode($responseBody));
            try {
                Session::put('email_sent',true);
                Mail::to($email)->send(new \App\Mail\MyTestMail());
            }catch (\Exception $exception){
                Log::channel('error')->error('Mail Error Message:',[$exception->getMessage()]);
                Session::put('email_sent',false);
            }
        } else {
            return response()->json(['message' => 'Error']);
        }
        return redirect()->route('alert');
    }

    private function sendApiRequest($apiUrl, $jsonData, $client)
    {
        try {
            $response = $client->post($apiUrl, [
                'json' => $jsonData,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . Session::get('token'),
                ],
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
            return response()->json(['message' => 'ERROR ' . $exception->getMessage()]);
        }
    }

    public function alert()
    {
        if(Session::get('email_input')===false)
        {
            Session::put('email_input',true);
            return view('alert');
        }
        return redirect()->route('index');
    }

}
