<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'phone', 'code', 'status','country_code', 'expired_at'
    ];

    public function store($request) {
        $input = $request->all();
        $start = date('Y-m-d H:i:s');
        $input['expired_at'] = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start)));
        $verification = $this->where(['phone'=>$input['phone'],
            'country_code'=>$input['country_code'],
            'status'=>'pending',
        ])->first();
        if($verification){
            $sms = $verification->update($input);
        }else{
            $this->fill($input);
            $sms = $this->save();
        }
        return response()->json($sms, 200);
    }

    public static function updateModel($request, $id) {
        $inputs['phone'] = $request->mobile;
        $inputs['code'] = $request->code;
        $inputs['status'] = $request->status;
        Self::where('id', $id)->update($inputs);
        return true;
    }
}
