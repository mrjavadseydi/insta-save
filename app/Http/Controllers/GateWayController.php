<?php

namespace App\Http\Controllers;


use App\helper\Gateway;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Sell;
use Illuminate\Http\Request;

class GateWayController extends Controller
{
    private $gateWay;
    public function __construct(){
        $this->gateWay = new GateWay();
    }

    public function init($id){
        $order = Payment::findOrFail($id);
        $user = Member::where('id',$order->member_id)->first();
        $param  = [
            'merchantCode'=>config('gateway.MERCHANT_CODE'),
            'amount'=>$order->price,
            'callBackUrl'=>"https://zarincrypto.com/callback",
            'invoiceNumber'=>$id,
            'payerName' => $user->first_name . $user->last_name,
            'description' =>"خرید درخواست",
        ];


        $request =$this->gateWay->paymentRequest($param);

        if($request['status']){
            $order->update([
                'status'=>'0',
                'token'=>$request['paymentNumber']
            ]);
            $this->gateWay->paymentGateway($request['paymentNumber']);
        }else{
            return "خط در هنگام اتصال به درگاه";
        }
    }
    public function verify(Request $request){
        if(!$request->has('paymentNumber'))
            abort(403);

        $sell = Payment::where([['status',0],['token',$request->paymentNumber]])->first();
        if(!$sell)
            abort(403);

        $result = $this->gateWay->paymentVerify([
            'merchantCode'=>config('gateway.MERCHANT_CODE'),
            'paymentNumber'=>$request->paymentNumber
        ]);
        $member = Member::where('id',$sell->member_id)->first();
        if($result['status']){
            sendMessage([
                'chat_id'=>$member->chat_id,
                'text'=>"پرداخت شما با موفقیت انجام شد \n شماره پرداخت : ".$_POST['paymentNumber']."حساب شما شارژ شد",
                'reply_markup'=>backButton()
            ]);
            $member->update([
                'request_count'=>$member->request_count+Plan::whereId($sell->plan_id)->first()->request_count,
            ]);
            $sell->update([
                'status'=>1
            ]);
            return'پرداخت شما با موفقیت انجام شد <br /> شماره پرداخت : '.$_POST['paymentNumber'];

        }else{
            sendMessage([
                'chat_id'=>$sell->chat_id,
                'text'=>"پرداخت شما موفقیت آمیز نبود ! در صورت کسر وجه تا ۷۲ ساعت وجه به حساب شما بازگشت خواهد کرد",
                'reply_markup'=>backButton()
            ]);
            $sell->update([
                'status'=>-1
            ]);
            return "پرداخت شما موفقیت آمیز نبود ! در صورت کسر وجه تا ۷۲ ساعت وجه به حساب شما بازگشت خواهد کرد";
        }
    }
}
