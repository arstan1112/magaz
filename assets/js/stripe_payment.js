// Create a Stripe client.
var stripe = Stripe('pk_test_zqRoDviJgLasWIWTaF24r7sX008b6oRSq4');
// var stripe = Stripe('pk_test_TYooMQauvdEDq54NiTphI7jx');

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var styleNum = {
    base: {
        color: '#32325d',
        backgroundColor: 'white',
        // color: 'green',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
            // color: 'green'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: styleNum});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

console.log('inside webpack stripe');

card.addEventListener('change', ({error}) => {
    const displayError = document.getElementById('card-errors');
    if (error) {
        displayError.textContent = error.message;
    } else {
        displayError.textContent = '';
    }
});

var submitButton = document.getElementById('submit');
var clientSecret = document.getElementById('clientSecret').value;
console.log(clientSecret);

submitButton.addEventListener('click', function(ev) {
    stripe.confirmCardPayment(clientSecret, {
        payment_method: {
            card: card,
            billing_details: {
                name: 'Magaz Client'
            }
        }
    }).then(function(result) {
        if (result.error) {
            // Show error to your customer (e.g., insufficient funds)
            alert(result.error.message);
            console.log(result.error);
        } else {
            // The payment has been processed!
            if (result.paymentIntent.status === 'succeeded') {
                console.log(result.paymentIntent);
                alert('Payment accepted');

                // const Http = new XMLHttpRequest();
                // const url='/webhook';
                // Http.open("POST", url);
                // Http.send();

                // Http.onreadystatechange = (e) => {
                //     console.log(Http.responseText)
                // }

                // Show a success message to your customer
                // There's a risk of the customer closing the window before callback
                // execution. Set up a webhook or plugin to listen for the
                // payment_intent.succeeded event that handles any business critical
                // post-payment actions.
            }
        }
    });
});