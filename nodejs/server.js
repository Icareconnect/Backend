 require('dotenv').config({path:'../.env'});
  var app = require('express')();
  var server = require('http').Server(app);
  var io = require('socket.io')(server);
  var mysql = require('mysql');
  var Redis = require('ioredis');
  var moment = require('moment');
  var FCM = require('fcm-node');
  var serverKey = process.env.SERVER_KEY_ANDRIOD;
  var fcm = new FCM(serverKey);
  var redisClient = Redis.createClient({ host: process.env.REDIS_HOST, port: process.env.REDIS_PORT,db:'0' });
  var db_config = {
      host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    port:process.env.DB_PORT
      };
  var con;
  var connected_user_socket_ids=[];
  var port_number = process.env.SOCKET_IO_PORT || 8080;
  server.listen(port_number, function(){
      console.log("Listening on localhost:"+port_number)
  });
  ///////////////////////////////////////         App Chat    //////////////////////////////////////////////////////////
  redisClient.subscribe('message');
  redisClient.subscribe('laravel_database_message');

  redisClient.on("message", function(channel, data) {
    var messageData = JSON.parse(data);
    connected_user_socket_ids.forEach(function(id){
      try{
          console.log('sending to socket - '+id);
            io.sockets.connected[id].emit('message',messageData);
        }catch(e){
          console.log('Socket Emit Error - '+e);
        }
    });
    io.emit(channel, data);
  });
  redisClient.on("error", function(error) {
    console.error(error);
  })
  io.on('connect', function (socket) {
     var handshake = socket.handshake;
     var db_name = process.env.DB_DATABASE;
     if(handshake.query!==undefined && handshake.query.domain!==undefined){
        db_name = 'db_'+handshake.query.domain;
     }
     var con;
     var db_config = {
        host: process.env.DB_HOST,
        user: process.env.DB_USERNAME,
        password: process.env.DB_PASSWORD,
        database: db_name,
        port:process.env.DB_PORT
      };
      console.log('db_name',db_name);
      console.log('connect fcm first ',fcm.serverKey);
      function handleDisconnect() {
        con = mysql.createConnection(db_config); // Recreate the connection, since
        con.connect(function(err) {              // The server is either down
            if(err){                                     // or restarting (takes a while sometimes).
                console.log('error when connecting to db:', err.message);
                setTimeout(handleDisconnect, 2000); // We introduce a delay before attempting to reconnect,
              }
            else{
                   
                    var user_id = undefined;
                    if(handshake.query!==undefined && handshake.query.user_id!==undefined){
                      user_id = handshake.query.user_id;
                      var sessionID = socket.id;
                      if(user_id==undefined)
                          console.log('user_id is Required');
                        else{
                            var user = con.query("SELECT * FROM users WHERE id= '"+user_id+"'", function(error, results, fields){
                            if(error){
                                console.log(error.message);
                              }else if(results == undefined || results.length == 0){
                                console.log("No User found");
                              }else{
                                console.log('user_id sessionID',user_id,sessionID);
                                var update_device = con.query("UPDATE users SET socket_id='"+sessionID+"' WHERE id="+results[0].id+" ", function(ierror, iresults, ifields){
                                    if(ierror){
                                        console.log(ierror.message);
                                      }else{
                                        io.sockets.connected[sessionID].emit('success',{'message':'connected'});
                                      }

                                });
                              }
                            });
                          }
                    }
                    socket.on('readMessage',function(data,callback){
                      console.log('readMessage',data);
                      if(data.messageId){
                        con.query("UPDATE messages SET `status`='SEEN' WHERE id="+data.messageId+" AND `status`='DELIVERED'", function(ierror, iresults, ifields){
                          con.query("SELECT * FROM users WHERE id= '"+data.receiverId+"'", function(error, results, fields){
                            if(results !== undefined && results.length !== 0){
                              try{
                                io.sockets.connected[results[0].socket_id].emit('readMessage',data);
                              }catch(e){
                                  console.log('Socket Emit Error - '+e);
                                  callback({'status':'ERROR','message':e});
                              }
                            }
                            if(ierror){
                              callback({'status':'ERROR','message':ierror.message});
                            }else if(iresults == undefined || iresults.length == 0){
                              callback({'status':'ALREADY_SEEN'});
                            }else{
                              callback({'status':'SEEN'});
                            }
                          });
                        });
                      }
                    });
                    socket.on("typing", function(data) {
                        if(data && data.receiverId){
                            con.query("SELECT * FROM users WHERE id= '"+data.receiverId+"'", function(error, results, fields){
                              if(results !== undefined && results.length !== 0){
                                try{
                                  io.sockets.connected[results[0].socket_id].emit('typing',data);
                                }catch(e){
                                    console.log('Socket Emit Error - '+e);
                                }
                              }
                            });
                        }
                    });
                    socket.on('deliveredMessage',function(data,callback){
                      console.log('deliveredMessage',data);
                      if(data.messageId){
                        con.query("UPDATE messages SET `status`='DELIVERED' WHERE id="+data.messageId+" AND `status`='SENT'", function(ierror, iresults, ifields){
                          con.query("SELECT * FROM users WHERE id= '"+data.receiverId+"'", function(error, results, fields){
                              if(results !== undefined && results.length !== 0){
                                try{
                                  io.sockets.connected[results[0].socket_id].emit('deliveredMessage',data);
                                }catch(e){
                                  console.log('Socket Emit Error - '+e);
                                  callback({'status':'ERROR','message':e});
                                }
                              }
                              if(ierror){
                                callback({'status':'ERROR','message':ierror.message});
                              }else if(iresults == undefined || iresults.length == 0){
                                callback({'status':'ALREADY_DELIVERED'});
                              }else{
                                callback({'status':'DELIVERED'});
                              }
                          });
                        });
                      }
                    });
                    socket.on('sendlivelocation', async (data,callback) => {
                      let locationData = {};
                      let senderId = '';
                      var tempdata = data;
                      senderId = tempdata.senderId;
                      if(tempdata.request_id){
                          con.query("INSERT INTO last_locations(`request_id`,`user_id`, `lat`, `long`) VALUES(?,?,?,?)", [tempdata.request_id,tempdata.senderId, tempdata.lat, tempdata.long], function (err, result) {
                                if (err) {
                                  callback({'status':'ERROR','message':err});
                                } else {
                                    con.query("SELECT * FROM users WHERE id= '"+data.receiverId+"'", function(error, results, fields){
                                      message = {
                                          'message': 'location updated successfully',
                                          'success': 1
                                      };
                                      locationData.senderId = tempdata.senderId;
                                      locationData.lat = tempdata.lat;
                                      locationData.request_id = tempdata.request_id;
                                      locationData.long = tempdata.long;
                                      locationData.receiverId = tempdata.receiverId;
                                      locationData.created_at = new Date();
                                      try{
                                        io.sockets.connected[results[0].socket_id].emit('sendlivelocation',locationData);
                                        console.log('sent........sendlivelocation');
                                        callback({'status':'success','message':'success'});
                                      }catch(e){
                                        console.log('Socket Emit Error - '+e);
                                        callback({'status':'ERROR','message':e});
                                      }
                                    });
                                }
                            });
                      }
                    });
                    socket.on('sendMessage', function(data,callback){
                      console.log('message',JSON.stringify(data));
                      console.log('fcm',fcm.serverKey);
                      console.log('db_name',db_name);
                      var ret_response = {};
                      data.pushType = 'chat';
                     // data.aps = {"content-available" : 1,"alert":{title:"chat",subtitle:data.message,body:""}}
                      data.dbImageUrl = data.imageUrl;
                      if(data.imageUrl==''){
                        data.imageUrl = null;
                      }
                      if(data){
                        con.query("SELECT * FROM users WHERE id= '"+data.receiverId+"'", function(error, results, fields){
                        if(error){
                            ret_response = {"status":"error","message":error.message};
                            callback(ret_response);
                          }else if(results == undefined || results.length == 0){
                            console.log("No User found");
                            ret_response = {"status":"error","message":"user not found"};
                            callback(ret_response);
                          }else{
                            con.query("SELECT * FROM request_history WHERE request_id= '"+data.request_id+"' AND status='in-progress'", function(error, requestResult, requestResultfields){
                              if(error){
                                console.log(error.message);
                                ret_response = {"status":"error","message":error.message};
                                callback(ret_response);
                              }
                              else if(requestResult==undefined || requestResult.length==0){
                                console.log('request completed or not found');
                                ret_response = {"status":"REQUEST_COMPLETED","message":"request completed or not found"};
                                callback(ret_response);
                              }
                              else{
                                  try{
                                    var dateString = moment.utc(data.sentAt).format("YYYY-MM-DD HH:mm:ss");
                                  }catch(e){
                                    var dateString = moment.utc().format("YYYY-MM-DD HH:mm:ss");
                                  }
                                  var message = { //this may vary according to the message type (single recipient, multicast, topic, et cetera)
                                      to: results[0].fcm_id,
                                      data:data,
                                      notification: {
                                          "title" : data.pushType,
                                          "body": data.message,
                                          "sound": "default",
                                          "badge": 0
                                      },
                                      priority: "high"
                                  };
                                var notification = null;
                                  if(results[0].device_type=='IOS'){
                                      notification = {
                                          "title" : data.pushType,
                                          "body": data.message,
                                          "sound": "default",
                                          "badge": 0
                                      };
                                  }
                                  var message = { //this may vary according to the message type (single recipient, multicast, topic, et cetera)
                                      to: results[0].fcm_id,
                                      data:data,
                                      notification:notification,
                                      priority: "high"
                                  };
                                  console.log('message',message);
                                  fcm.send(message, function(err, response){
                                      if (err) {
                                          console.log("FCM error ",err);
                                      } else {
                                          console.log("FCM Successfully sent with response: ", response);
                                      }
                                  });
                                //  fcm.send(message, function(err, response){
                                //      if (err) {
                                 //         console.log(err);
                                   //   } else {
                                     //     console.log("Successfully sent with response: ", response);
                                     // }
                                  //});
                                  var sql = "INSERT INTO messages (user_id,receiver_id, request_id,message,created_at,updated_at,image_url,message_type) VALUES ('"+data.senderId+"', '"+data.receiverId+"','"+data.request_id+"','"+data.message+"','"+dateString+"','"+dateString+"','"+data.dbImageUrl+"','"+data.messageType+"')";
                                  con.query(sql, function (err, result) {
                                      if (err){
                                        console.log("no record inserted",err.message);
                                      }else{
                                        try{
                                            data.messageId = result.insertId;
                                            console.log('sending to socket - '+results[0].socket_id);
                                            io.sockets.connected[results[0].socket_id].emit('messageFromServer',data);
                                            ret_response = {"status":"MESSAGE_SENT","message":"Message Sent","messageId":data.messageId};
                                            callback(ret_response);
                                          }catch(e){
                                            data.messageId = result.insertId;
                                            console.log('Socket Emit Error - '+e);
                                            ret_response = {"status":"MESSAGE_SENT","message":"Message Not Sent Client not connected","messageId":data.messageId};
                                            callback(ret_response);
                                          }
                                      }
                                    });
                                }  

                              });
                            }
                        });
                      }else{
                        console.log('No data found');
                        ret_response = {"status":"error","message":"No data found"};
                        callback(ret_response);
                      }
                    });
                    socket.on('disconnect', function(error,data){
                          console.log('error',error);
                    });
              }
        })
        con.on('error', function(err) {     
            console.log('db error', err);
            if(err.code === 'PROTOCOL_CONNECTION_LOST'){ 
            // Connection to the MySQL server is usually
                  handleDisconnect(); 
              }
            else{
                  // connnection idle timeout (the wait_timeout
                  console.log("Mysql Error - ",err.message);
                  throw err;
              }
        });
      }
      handleDisconnect();
    
  });
