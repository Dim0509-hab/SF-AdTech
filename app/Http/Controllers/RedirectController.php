<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Click;
use Illuminate\Support\Facades\DB;

class RedirectController extends Controller {
    public function redirect(Request $request, $token){
        $click = Click::where('click_token', $token)->first();
        if (!$click) {
            if (preg_match('/^offer_(\d+)_wm_(\d+)$/', $token, $m)){
                $click = Click::create(['offer_id'=>$m[1],'webmaster_id'=>$m[2],'click_token'=>$token,'referer'=>$request->headers->get('referer'),'ip'=>$request->ip(),'user_agent'=>$request->userAgent(),'cost'=>0,'redirected'=>false]);
            } else abort(404);
        }
        $sub = DB::table('offer_webmaster')->where('offer_id',$click->offer_id)->where('webmaster_id',$click->webmaster_id)->first();
        if (!$sub){ DB::table('rejections')->insert(['offer_id'=>$click->offer_id,'webmaster_id'=>$click->webmaster_id,'ip'=>$request->ip(),'referer'=>$request->headers->get('referer'),'attempted_at'=>now()]); abort(404); }
        $offer = $click->offer;
        $agreed = DB::table('offer_webmaster')->where('offer_id',$offer->id)->where('webmaster_id',$click->webmaster_id)->value('agreed_price');
        $click->cost = $agreed ? $agreed : $offer->price;
        $click->redirected = true; $click->redirected_at = now(); $click->save();
        return redirect()->away($offer->target_url);
    }
}
