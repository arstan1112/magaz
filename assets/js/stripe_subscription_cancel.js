var cancelSubscriptionButton = document.getElementsByClassName('cancelSubscriptionButton');
var subscriptionId = document.getElementsByClassName('subscriptionId');
var subscriptionNickName = document.getElementsByClassName('subscriptionNickName');

var counter = function(node) {
    let num = 0;
    for (var i = 0; i < cancelSubscriptionButton.length; i++) {
        if (cancelSubscriptionButton[i] === node) {
            return num;
        }
        num++;
    }
    return -1;
};

var subscribeFunction = function() {
    let indexOfClass = counter(this);
    let currentSubscriptionId = subscriptionId[indexOfClass].value;
    let currentSubscriptionNickName = subscriptionNickName[indexOfClass].innerHTML;

    fetch('/subscription/cancel', {
        method: 'post',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            subscriptionId: currentSubscriptionId,
        }),
    }).then(function(response) {
        return response.json();
    }).then(function(responseJson) {
        alert('Your subscription: '+currentSubscriptionNickName+' is cancelled');
        document.querySelectorAll('.cancelSubscriptionButton')[indexOfClass].closest('.list-group-item').style.display = 'none';
        console.log('Subscription cancel succeeded');
    });
};

for (var i = 0; i < cancelSubscriptionButton.length; i++) {
    cancelSubscriptionButton[i].addEventListener('click', subscribeFunction, false);
}
