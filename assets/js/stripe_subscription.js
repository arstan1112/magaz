// Script.js
// Set your publishable key: remember to change this to your live publishable key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
var stripe = Stripe('pk_test_zqRoDviJgLasWIWTaF24r7sX008b6oRSq4');
var elements = stripe.elements();

// Client.js
// Set up Stripe.js and Elements to use in checkout form
var style = {
    base: {
        color: "#32325d",
        // width: "100px",
        // height: "20px",
        // backgroundColor: 'white',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: "antialiased",
        fontSize: "16px",
        "::placeholder": {
            color: "#aab7c4"
        }
    },
    invalid: {
        color: "#fa755a",
        iconColor: "#fa755a"
    }
};

var cardElement = elements.create("card", { style: style });
cardElement.mount("#card-element");

// Javascript
// card.addEventListener('change', ({error}) => {
cardElement.addEventListener('change', ({error}) => {
    const displayError = document.getElementById('card-errors');
    if (error) {
        displayError.textContent = error.message;
    } else {
        displayError.textContent = '';
    }
});

//Client.js
var form = document.getElementById('subscription-form');

form.addEventListener('submit', function(event) {
    // We don't want to let default form submission happen here,
    // which would refresh the page.
    event.preventDefault();
    var pricingPlan   = document.getElementById('pricingPlan').value;
    if (!pricingPlan) {
        alert('No pricing plan found. Can\'t subscribe');
    } else {
        stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                email: 'jenny.rosen@example.com',
            },
        }).then(stripePaymentMethodHandler);
    }
});

// Script.js
function stripePaymentMethodHandler(result, email) {
    var customerEmail = document.getElementById('email').value;
    var pricingPlan   = document.getElementById('pricingPlan').value;
    if (result.error) {
        console.log(result.error);
        // Show error in payment form
    } else {
        // Otherwise send paymentMethod.id to your server
        console.log('customlog: stripePaymentMethodHandler called');
        console.log(result.paymentMethod.id);

        fetch('/subscribe', {
            method: 'post',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                email: customerEmail,
                payment_method: result.paymentMethod.id,
                pricing_plan: pricingPlan,
            }),
        }).then(function(result) {
            return result.json();
        }).then(function(responseJson) {
            console.log(responseJson);
            let status = responseJson['status'];
            if (status === 'error') {
                console.log('failure');
                let message = responseJson['message'];
                window.location.href = '/failure/'+message;
            } else {
                console.log('success');
                window.location.href = '/success';
            }
            // The customer has been created
            // window.location.href = '/success';
        }).catch(function (error) {
            console.log(error);
            // window.location.href = '/failure/'+error;
        });
        // window.location.href = '/success';
    }
}

// $('.cancel').on('click', function () {
//     let link = $(this).attr('href');
// });

// var cancelSubscription = document.getElementById('cancelSubscription');
// var subscriptionId = document.getElementById('subscriptionId').value;
// cancelSubscription.on('click', function (e) {
//     console.log('cancelSubscription button pressed');
//     $.post('/subscription/cancel/'+subscriptionId)
//         .then(function (response) {
//             $('.subscriptionList').prepend(response['content']);
//             console.log('Subscription cancel succeeded');
//         })
//         .fail(function (xhr) {
//             let response = JSON.parse(xhr.responseText);
//             alert(response);
//         })
//     ;
// });


// cancelSubscription.addEventListener('click', function (event) {
//     console.log('cancelSubscription button pressed');
//     $.post('/subscription/cancel/'+subscriptionId)
//         .then(function (response) {
//             $('.subscriptionList').prepend(response['content']);
//             console.log('Subscription cancel succeeded');
//         })
//         .fail(function (xhr) {
//             let response = JSON.parse(xhr.responseText);
//             alert(response);
//         })
//     ;
// });

// Client.js
// var subscription = document.getElementById('subscription').value;

// const { latest_invoice } = subscription;
// const { payment_intent } = latest_invoice;
//
// if (payment_intent) {
//     const { client_secret, status } = payment_intent;
//
//     if (status === 'requires_action') {
//         stripe.confirmCardPayment(client_secret).then(function(result) {
//             if (result.error) {
//                 console.log(result.error);
//                 console.log(result.error.message);
//                 // Display error message in your UI.
//                 // The card was declined (i.e. insufficient funds, card has expired, etc)
//             } else {
//                 alert('Subscription succeeded');
//                 // Show a success message to your customer
//             }
//         });
//     } else {
//         // No additional information was needed
//         // Show a success message to your customer
//     }
// }
