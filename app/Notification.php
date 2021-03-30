<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Config;
class Notification extends Model
{
   public function push_notification($user_ids,$data){
   		$others = [];
      $ios_types = [];
   		foreach ($user_ids as $key => $user_id) {
   			$user_data = User::find($user_id);
   			if($user_data && $user_data->fcm_id){
          if($user_data->device_type=='IOS'){
            $ios_types[] =  $user_data->fcm_id;
          }else{
   				   $others[] = $user_data->fcm_id;
          }
   			}
   		}
      $priority = "normal";
      $timeToLive = null;
      $pushTypes = ["CALL","CALL_RINGING","CALL_ACCEPTED","CALL_CANCELED","REQUEST_COMPLETED"];
      if(in_array($data['pushType'],$pushTypes)){
        $priority = "high";
      }
      if($data['pushType']=="CALL" || $data['pushType']=="CALL_RINGING" || $data['pushType']=="CALL_ACCEPTED" || $data['pushType']=="CALL_CANCELED"){
        $timeToLive = 20;
      }
      $notification = [];
      if(count($others)>0){
          $fields = array (
              'registration_ids' =>$others,
              'data' =>$data,
              'notification'=>null,
              "sound"=> "default",
              "priority"=>$priority
          );
          if($timeToLive){
            $fields["time_to_live"] = $timeToLive;
          }
          \Log::channel('custom')->info('Android Notification', ['device_ids'=>$others,'fields' => $fields]);
          $this->sendNotification($fields);

      }
      if(count($ios_types)>0){
          if(isset($data['pushType'])){
            $notification = [
                "title" => $data["pushType"],
                "body"=> $data["message"],
                "sound"=> "default",
                "badge"=>0
            ];
          }
          $fields = array (
              'registration_ids' =>$ios_types,
              'data' =>$data,
              'notification'=>$notification,
              "priority"=>$priority,
          );
          if($timeToLive){
            $fields["time_to_live"] = $timeToLive;
          }
          \Log::channel('custom')->info('IOS Notification', ['device_ids'=>$ios_types,'fields' => $fields]);
          $this->sendNotification($fields);
      }
      return;


   }

   public function push_test_notification($fcm_id,$data,$request){
      $others = [];
      $ios_types = [];
      if($request->device_type=='IOS'){
        $ios_types[] =  $fcm_id;
      }else{
         $others[] = $fcm_id;
      }
      $priority = "normal";
      $timeToLive = null;
      $notification = [];
      if(count($others)>0){
          $fields = array (
              'registration_ids' =>$others,
              'data' =>$data,
              'notification'=>null,
              "priority"=>$priority
          );
          if($timeToLive){
            $fields["time_to_live"] = $timeToLive;
          }
          return $this->sendTestNotification($fields,$request->fcm_server_key);

      }
      if(count($ios_types)>0){
          if(isset($data['pushType'])){
            $notification = [
                "title" => $data["pushType"],
                "body"=> $data["message"],
                "sound"=> "default",
                "badge"=>0
            ];
          }
          $fields = array (
              'registration_ids' =>$ios_types,
              'data' =>$data,
              'notification'=>$notification,
              "priority"=>$priority,
          );
          if($timeToLive){
            $fields["time_to_live"] = $timeToLive;
          }
          return $this->sendTestNotification($fields,$request->fcm_server_key);
      }

   }

   public function sendTestNotification($fields,$api_key){
   		$url = 'https://fcm.googleapis.com/fcm/send';
      $headers = array(
          'Content-Type:application/json',
          'Authorization:key='.$api_key
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
   }


   public function sendNotification($fields){
      $url = 'https://fcm.googleapis.com/fcm/send';
      //header includes Content type and api key
      /*api_key available in:
      Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/
      $api_key = env('SERVER_KEY_ANDRIOD');
      $headers = array(
          'Content-Type:application/json',
          'Authorization:key='.$api_key
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
      \Log::channel('custom')->info('sendNotification==========', ['domain' => Config::get("client_data")->domain_name,'apikey'=>$api_key]);
      \Log::channel('custom')->info('sendNotification==========', ['result' => $result]);
      return $result;
   }

   public static function markAsRead($receiver_id){
    	self::where(['read_status'=>'unread','receiver_id'=>$receiver_id])->update(['read_status' =>'read']);
    	return true;
    }
}
