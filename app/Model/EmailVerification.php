<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'email', 'status', 'code', 'expired_at'
    ];

    public function store($request) {
        $input = $request->all();
        $start = date('Y-m-d H:i:s');
        $input['expired_at'] = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start)));
        $verification = $this->where(['email'=>$input['email'],
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
        $inputs['email'] = $request->email;
        $inputs['code'] = $request->code;
        $inputs['status'] = $request->status;
        Self::where('id', $id)->update($inputs);
        return true;
    }
}
