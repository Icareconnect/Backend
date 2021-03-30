<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Coupon;
use Illuminate\Http\Request;
use App\Http\Traits\CategoriesTrait;
use Illuminate\Support\Str;
use Exception;
class CouponController extends Controller
{
    use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $coupons = Coupon::orderBy('id','DESC')->get();
         foreach ($coupons as $key => $coupon) {
            $coupon->discount_type = '';
            $coupon->discount_value = '';
            if($coupon->percent_off){
                $coupon->discount_value = $coupon->percent_off;
                $coupon->discount_type = 'percentage';
            }else{
                $coupon->discount_value = $coupon->value_off;
                $coupon->discount_type = 'currency';
            }
         }
        return view('admin.coupon.index',compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->parentCategories();
        $services = $this->services();
        return view('admin.coupon.add',compact('categories','services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['coupon_code' => strtoupper(Str::slug($request->coupon_code,'_'))]);
        $validator = \Validator::make($request->all(), [
                'date_range' => 'required',
                'coupon_code' => 'required|unique:coupons,coupon_code',
                'discount_type'      => 'required|string',
                'discount_value' => 'required|integer|min:1',
                'minimum_value' => 'required|integer|min:1',
                'maximum_value' => 'required|integer|min:1',
                'limit' => 'required|integer|min:1',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $category_id = null;
          $service_id = null;
          $percent_off = '';
          $value_off = '';
          if(isset($input['category']) && $input['category']){
            $category_id = $input['category'];
          }
          if(isset($input['service']) && $input['service']){
            $service_id = $input['service'];
          }
          if($input['discount_type']=='percentage'){
            $percent_off = $input['discount_value'];
          }
          if($input['discount_type']=='currency'){
            $value_off = $input['discount_value'];
          }
          $date_range = explode(' to ', $input['date_range']);
          $start_date =  date('Y-m-d', strtotime($date_range[0]));
          $end_date =  date('Y-m-d', strtotime($date_range[1]));
          $coupon = new Coupon();
          $coupon->category_id = $category_id;
          $coupon->service_id = $service_id;
          $coupon->percent_off = $percent_off;
          $coupon->value_off = $value_off;
          $coupon->minimum_value = $input['minimum_value'];
          $coupon->maximum_discount_amount = $input['maximum_value'];
          $coupon->start_date = $start_date;
          $coupon->end_date = $end_date;
          $coupon->limit = $input['limit'];
          $coupon->coupon_code = $input['coupon_code'];
          if($coupon->save()){

          }
          return redirect('admin/coupon');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $categories = $this->parentCategories();
        $services = $this->services();
        $coupon->discount_type = '';
        $coupon->discount_value = '';
        if($coupon->percent_off){
            $coupon->discount_value = $coupon->percent_off;
            $coupon->discount_type = 'percentage';
        }else{
            $coupon->discount_value = $coupon->value_off;
            $coupon->discount_type = 'currency';
        }
        $start_date =  date('Y-m-d', strtotime($coupon->start_date));
        $end_date =  date('Y-m-d', strtotime($coupon->end_date));
        $coupon->date_range = $start_date.' to '.$end_date;
        return view('admin.coupon.edit',compact('categories','services','coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = \Validator::make($request->all(), [
                'date_range' => 'required',
                'discount_type'      => 'required|string',
                'discount_value' => 'required|integer|min:1',
                'minimum_value' => 'required|integer|min:1',
                'maximum_value' => 'required|integer|min:1',
                'limit' => 'required|integer|min:1',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $category_id = null;
          $service_id = null;
          $percent_off = '';
          $value_off = '';
          if(isset($input['category']) && $input['category']){
            $category_id = $input['category'];
          }
          if(isset($input['service']) && $input['service']){
            $service_id = $input['service'];
          }
          if($input['discount_type']=='percentage'){
            $percent_off = $input['discount_value'];
          }
          if($input['discount_type']=='currency'){
            $value_off = $input['discount_value'];
          }
          $date_range = explode(' to ', $input['date_range']);
          $start_date =  date('Y-m-d', strtotime($date_range[0]));
          $end_date =  date('Y-m-d', strtotime($date_range[1]));
          $coupon->category_id = $category_id;
          $coupon->service_id = $service_id;
          $coupon->percent_off = $percent_off;
          $coupon->value_off = $value_off;
          $coupon->minimum_value = $input['minimum_value'];
          $coupon->maximum_discount_amount = $input['maximum_value'];
          $coupon->start_date = $start_date;
          $coupon->end_date = $end_date;
          $coupon->limit = $input['limit'];
          if($coupon->save()){

          }
          return redirect('admin/coupon');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
      if($coupon->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
