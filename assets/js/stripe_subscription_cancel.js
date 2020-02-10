console.log('stripe cancel begin');

var cancelSubscriptionButton = document.getElementsByClassName('cancelSubscriptionButton');
var subscriptionId = document.getElementsByClassName('subscriptionId');

console.log('stripe cancel begin 2');
console.log(cancelSubscriptionButton.length);

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
    console.log('cancelSubscription button pressed');
    let indexOfClass = counter(this);
    console.log(indexOfClass);
    let currentSubscriptionId = subscriptionId[indexOfClass].value;
    console.log(currentSubscriptionId);

    fetch('/subscription/cancel', {
        method: 'post',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            subscriptionId: currentSubscriptionId,
            // payment_method: 'result.paymentMethod.id',
            // pricing_plan: 'pricingPlan',
        }),
    }).then(function(response) {
        return response.json();
    }).then(function(responseJson) {
        let list = document.getElementById('subscriptionList');
        list.innerHTML = responseJson['content'];
        let addedCancelButtons = document.getElementById( 'subscriptionList' ).getElementsByClassName( 'cancelSubscriptionButton' );
        for (var i = 0; i < addedCancelButtons.length; i++) {
            addedCancelButtons[i].addEventListener('click', subscribeFunction, false);
        }
        // console.log(responseJson['content']);
        console.log('Subscription cancel succeeded');
    });
};

// var loopFunction = function () {
    for (var i = 0; i < cancelSubscriptionButton.length; i++) {
        cancelSubscriptionButton[i].addEventListener('click', subscribeFunction, false);
    }
// };
