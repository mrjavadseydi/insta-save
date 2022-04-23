<?php
namespace App\helper;


class Gateway
{
    private $apiUrl = 'https://payment.tejaratazad.com/ws';

    private $gatewayUrl =  'https://payment.tejaratazad.com/payment';

    public function paymentRequest($params){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl.'/payments/request.json');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type' => 'application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($response->status) && $response->status == 1 ){
            return array(
                'status'    		=>  true,
                'paymentNumber'     =>  $response->paymentNumber,
            );
        }else{
            return array(
                'status'    =>  false,
                'message'   =>  $response->message,
            );
        }
    }

    public function paymentVerify($params){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl.'/payments/verify.json');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type' => 'application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode( $params ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($response->status) && $response->status == 1){
            return array(
                'status'    =>  true,
            );
        }else{
            return array(
                'status'    =>  false,
                'message'   =>  $response->message,
            );
        }
    }


    public function paymentGateway($paymentNumber){
        $paymentUrl = $this->gatewayUrl.'/'.$paymentNumber;
        if ( ! headers_sent() ) {
            header('Location: ' . $paymentUrl );
            die;
        }
        die('<script type="text/javascript">window.location="' .$paymentUrl. '";</script>');
    }



    public function getCredit($params){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl.'/users/credit.json');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type' => 'application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode( $params ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($response->status) && $response->status == 1){
            return array(
                'status' 	=>	true,
                'credit'    =>  $response->credit,
            );
        }else{
            return array(
                'status'    =>  false,
                'message'   =>  $response->message,
            );
        }
    }
}
