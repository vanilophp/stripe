{{-- Success message container --}}
<div id="stripe-success-message-container">
    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 24 24">
        <path d="M 20.292969 5.2929688 L 9 16.585938 L 4.7070312 12.292969 L 3.2929688 13.707031 L 9 19.414062 L 21.707031 6.7070312 L 20.292969 5.2929688 z" fill="#40d521"></path>
    </svg>

    <h2 class="result-message-title">{{ __('Payment successful') }}</h2>
    <p class="result-message-description">{{ __('Thank you for your order!') }}</p>
</div>

{{-- Error message container --}}
<div id="stripe-card-error-message-container">
    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 50 50">
        <path d="M 9.15625 6.3125 L 6.3125 9.15625 L 22.15625 25 L 6.21875 40.96875 L 9.03125 43.78125 L 25 27.84375 L 40.9375 43.78125 L 43.78125 40.9375 L 27.84375 25 L 43.6875 9.15625 L 40.84375 6.3125 L 25 22.15625 Z" fill="#e80000"></path>
    </svg>

    <h2 class="result-message-title">{{ __('Payment error') }}</h2>
    <p id="stripe-card-error" class="result-message-description"></p>
</div>

{{-- Payment form --}}
<form id="stripe-payment-form">
    <h2 class="stripe-payment-title">{{ __('Please complete your payment by entering the payment details') }}</h2>
    <h3 class="stripe-card-title">{{ __('Debit or credit card') }}:</h3>

    <div id="stripe-card-element"></div>

    <p id="stripe-validation-error" role="alert"></p>

    <button id="stripe-form-submit" disabled>
        <div class="spinner hidden" id="stripe-spinner"></div>
        <span id="stripe-button-text">{{ __('Pay now') }}</span>
    </button>
</form>

<style>
    #stripe-payment-form,
    #stripe-success-message-container,
    #stripe-card-error-message-container {
        background: white;
        border-radius: .5rem;
        box-shadow: 0 0 0 0.5px rgba(50, 50, 93, 0.1), 0 2px 5px 0 rgba(50, 50, 93, 0.1), 0 1px 1.5px 0 rgba(0, 0, 0, 0.07);
        margin: 2rem 1rem;
        min-width: 250px;
        max-width: 600px;
        padding: 1rem;
    }

    @media screen and (min-width: 520px) {
        #stripe-payment-form,
        #stripe-success-message-container,
        #stripe-card-error-message-container {
            padding: 2.5rem;
        }
    }

    #stripe-card-error-message-container svg,
    #stripe-success-message-container svg {
        border-radius: 50%;
        margin-bottom: 1rem;
        margin-left: auto;
        margin-right: auto;
        padding: .5rem;
    }

    #stripe-success-message-container svg {
        border: 3px solid #40d521;
    }

    #stripe-card-error-message-container svg {
        border: 3px solid #e80000;
    }

    .StripeElement {
        border: 1px solid lightgray;
        border-radius: .5rem;
        padding: 1rem;
    }

    .StripeElement--focus {
        border: 1px solid gray;
    }

    .stripe-payment-title,
    .result-message-title {
        font-size: 1.5rem;
        text-align: center;
    }

    .result-message-title {
        margin-bottom: 1rem;
    }

    .result-message-description {
        text-align: center;
    }

    .stripe-payment-title {
        margin-bottom: 3rem;
    }

    .stripe-card-title {
        font-weight: 500;
        padding-bottom: .5rem;
    }

    #stripe-payment-form iframe {
        outline: none;
    }

    #stripe-payment-form #stripe-validation-error {
        color: #e80000;
        font-size: 12px;
        margin-bottom: 1.5rem;
        margin-top: .2rem;
    }

    #stripe-payment-form button {
        background: #5469d4;
        border: 0;
        border-radius: .5rem;
        box-shadow: 0 4px 5.5px 0 rgba(0, 0, 0, 0.07);
        color: #ffffff;
        cursor: pointer;
        display: block;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: 600;
        padding: 1rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    #stripe-payment-form button:hover {
        opacity: .85;
    }

    #stripe-payment-form button:disabled {
        background: lightgray;
        cursor: not-allowed;
        opacity: 1;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 1s ease infinite;
        margin: auto;
    }

    .spinner.hidden {
        display: none;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script src="https://js.stripe.com/v3/"></script>
<script>
    let successPaymentContainer = document.querySelector("#stripe-success-message-container");
    let errorPaymentContainer = document.querySelector("#stripe-card-error-message-container");
    let paymentButton = document.querySelector("#stripe-form-submit");
    let form = document.getElementById("stripe-payment-form");

    let stripe = Stripe(@json($publicKey));
    let elements = stripe.elements();

    let style = {
        base: {
            color: "#32325d",
            fontFamily: 'Arial, sans-serif',
            fontSmoothing: "antialiased",
            fontSize: "16px",
            "::placeholder": {
                color: "#32325d"
            }
        },
        invalid: {
            fontFamily: 'Arial, sans-serif',
            color: "#e80000",
            iconColor: "#e80000"
        }
    };

    let card = elements.create("card", {style: style});

    document.addEventListener("DOMContentLoaded", function () {
        // Disable the pay button on page load
        paymentButton.disabled = true;

        // Hide the success container on page load
        successPaymentContainer.style.display = "none";

        // Hide the error container on page load
        errorPaymentContainer.style.display = "none";
    })

    // Stripe injects an iframe into the DOM
    card.mount("#stripe-payment-form #stripe-card-element");

    card.on("change", function (event) {
        // Disable the Pay button if there are no card details in the Element
        document.querySelector("#stripe-form-submit").disabled = event.empty;
        document.querySelector("#stripe-payment-form #stripe-validation-error").textContent = event.error ? event.error.message : "";
    });


    form.addEventListener("submit", function (event) {
        event.preventDefault();

        // Complete payment when the submit button is clicked
        payWithCard(stripe, card);
    });

    // Calls stripe.confirmCardPayment
    // If the card requires authentication Stripe shows a pop-up modal to
    // prompt the user to enter authentication details without leaving your page.
    let payWithCard = function (stripe, card) {
        loading(true);
        stripe.confirmCardPayment(@json($intentSecret), {
            payment_method: {
                card: card
            }
        })
            .then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    if (result.error.type === 'card_error') {
                        showCardError(result.error.message)
                    } else {
                        showValidationError(result.error.message);
                    }
                } else {
                    orderComplete(result.paymentIntent.id);
                }
            });
    };


    /* ------- UI helpers ------- */

    // Shows a success message when the payment is complete
    let orderComplete = function (paymentIntentId) {
        const returnUrl = @json($returnUrl ?? null);

        if (returnUrl) {
            const options = {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json;charset=UTF-8",
                },
                body: JSON.stringify({"payment_intent": paymentIntentId})
            };

            fetch(returnUrl, options)
        }

        form.style.display = "none";
        successPaymentContainer.style.display = "block";
    }

    // Show the validation errors to the customer
    let showValidationError = function (errorMsgText) {
        loading(false);

        let errorMsg = document.querySelector("#stripe-payment-form #stripe-validation-error");
        errorMsg.textContent = errorMsgText;

        setTimeout(function () {
            errorMsg.textContent = "";
        }, 4000);
    };

    // Show the validation errors to the customer
    let showCardError = function (errorMsgText) {
        loading(false);
        errorPaymentContainer.style.display = "block"
        form.style.display = "none"

        let errorMsg = document.querySelector("#stripe-card-error");
        errorMsg.textContent = errorMsgText;

        setTimeout(function () {
            errorPaymentContainer.style.display = "none"
            form.style.display = "block"
        }, 4000);
    };

    // Show a spinner on payment submission
    let loading = function (isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#stripe-payment-form button").disabled = true;
            document.querySelector("#stripe-payment-form #stripe-spinner").classList.remove("hidden");
            document.querySelector("#stripe-payment-form #stripe-button-text").classList.add("hidden");
        } else {
            document.querySelector("#stripe-payment-form button").disabled = false;
            document.querySelector("#stripe-payment-form #stripe-spinner").classList.add("hidden");
            document.querySelector("#stripe-payment-form #stripe-button-text").classList.remove("hidden");
        }
    };
</script>
