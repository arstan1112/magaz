// Script.js
// Set your publishable key: remember to change this to your live publishable key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
var stripe = Stripe('pk_test_zqRoDviJgLasWIWTaF24r7sX008b6oRSq4');
var elements = stripe.elements();

console.log('customlog: stripe_subscription start');


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

console.log('customlog: stripe_subscription card created');

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

console.log('customlog: stripe_subscription after event listener');

//Client.js
var form = document.getElementById('subscription-form');

form.addEventListener('submit', function(event) {
    // We don't want to let default form submission happen here,
    // which would refresh the page.
    event.preventDefault();
    console.log('customlog: subscribe button pressed');

    stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            email: 'jenny.rosen@example.com',
        },
    }).then(stripePaymentMethodHandler);
});


var customerEmail = document.getElementById('email').value;
console.log('customerEmail');
console.log(customerEmail);

// Script.js
function stripePaymentMethodHandler(result, email) {
    if (result.error) {
        console.log(result.error);
        // Show error in payment form
    } else {
        // Otherwise send paymentMethod.id to your server
        console.log('customlog: stripePaymentMethodHandler called');
        console.log(result.paymentMethod.id);

        fetch('/create-customer', {
            method: 'post',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                email: customerEmail,
                payment_method: result.paymentMethod.id
            }),
        }).then(function(result) {
            return result.json();
        }).then(function(customer) {
            console.log('customlog: customer created')
            // The customer has been created
        });
    }
}

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
//                 // Display error message in your UI.
//                 // The card was declined (i.e. insufficient funds, card has expired, etc)
//             } else {
//                 // Show a success message to your customer
//             }
//         });
//     } else {
//         // No additional information was needed
//         // Show a success message to your customer
//     }
// }
