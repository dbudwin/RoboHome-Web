window.onAmazonLoginReady = function() {
    amazon.Login.setClientId("amzn1.application-oa2-client.ade0e0064cbc422db1c991d7c8a76884");
};

(function(d) {
    var a = d.createElement("script");
    a.type = "text/javascript";
    a.async = true;
    a.id = "amazon-login-sdk";
    a.src = "https://api-cdn.amazon.com/sdk/login1.js";
    d.getElementById("amazon-root").appendChild(a);
} (document));