@extends('voyager::master')


@section('content')

<h1></h1>
<div class="container">
<div class="row">
hi

</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<!-- firebase integration started -->

<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase.js"></script>
<!-- Firebase App is always required and must be first -->
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-app.js"></script>

<!-- Add additional services that you want to use -->
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-messaging.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-functions.js"></script>

<!-- firebase integration end -->

<!-- Comment out (or don't include) services that you don't want to use -->
<!-- <script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-storage.js"></script> -->

<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.8.0/firebase-analytics.js"></script>

<script type="text/javascript">
// Your web app's Firebase configuration
var firebaseConfig = {
    apiKey: "AIzaSyD1UjzJSyh1TQDvPIhxOdpNUNfIQeIpWaU",
    authDomain: "joker-services.firebaseapp.com",
    projectId: "joker-services",
    storageBucket: "joker-services.appspot.com",
    messagingSenderId: "788423050987",
    appId: "1:788423050987:web:eeeea2d5fe2741e85a60ae",
    measurementId: "G-KG3DG27G14"
  };
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
//firebase.analytics();
const messaging = firebase.messaging();
	messaging
.requestPermission()
.then(function () {
//MsgElem.innerHTML = "Notification permission granted." 
	console.log("Notification permission granted.");

     // get the token in the form of promise
	return messaging.getToken()
})
.then(function(token) {
 // print the token on the HTML page     
  console.log(token);
  
  
  
})
.catch(function (err) {
	console.log("Unable to get permission to notify.", err);
});

messaging.onMessage(function(payload) {
    console.log(payload);
    var notify;
    notify = new Notification(payload.notification.title,{
        body: payload.notification.body,
        icon: payload.notification.icon,
        tag: "Dummy"
    });
    console.log(payload.notification);
});

    //firebase.initializeApp(config);
var database = firebase.database().ref().child("/users/");
   
database.on('value', function(snapshot) {
    renderUI(snapshot.val());
});

// On child added to db
database.on('child_added', function(data) {
	console.log("Comming");
    if(Notification.permission!=='default'){
        var notify;
         
        notify= new Notification('CodeWife - '+data.val().username,{
            'body': data.val().message,
            'icon': 'bell.png',
            'tag': data.getKey()
        });
        notify.onclick = function(){
            alert(this.tag);
        }
    }else{
        alert('Please allow the notification first');
    }
});

self.addEventListener('notificationclick', function(event) {       
    event.notification.close();
});






  $('body').on('click', '.approval', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('approval_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done Approval");
});
   });
   $('body').on('click', '.rejection', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('block_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done Rejection");
});
   });
   $('body').on('click', '.un_block', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('un_block_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done"); 
});
   });
</script>
  @endsection