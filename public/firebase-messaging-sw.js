importScripts('https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js');

	firebase.initializeApp({
    apiKey: "AIzaSyD1UjzJSyh1TQDvPIhxOdpNUNfIQeIpWaU",
    authDomain: "joker-services.firebaseapp.com",
    projectId: "joker-services",
    storageBucket: "joker-services.appspot.com",
    messagingSenderId: "788423050987",
    appId: "1:788423050987:web:eeeea2d5fe2741e85a60ae",
    measurementId: "G-KG3DG27G14"
    });

	const messaging = firebase.messaging();
	messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
        
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
    };
  
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
})