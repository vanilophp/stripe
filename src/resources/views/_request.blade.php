<form id="stripe-payment-form">
    <div id="stripe-card-element"></div>
    <button id="stripe-form-submit">
        <div class="spinner hidden" id="stripe-spinner"></div>
        <span id="stripe-button-text">{{ __('Pay now') }}</span>
    </button>
    <p id="stripe-card-error" role="alert"></p>
    <p class="result-message hidden">{{ __('Your payment was successful') }}</p>
</form>

<style>
    #stripe-payment-form {
        width: 30vw;
        min-width: 500px;
        align-self: center;
        box-shadow: 0 0 0 0.5px rgba(50, 50, 93, 0.1),
        0 2px 5px 0 rgba(50, 50, 93, 0.1), 0 1px 1.5px 0 rgba(0, 0, 0, 0.07);
        border-radius: 7px;
        padding: 40px;
    }

    #stripe-payment-form input {
        border-radius: 6px;
        margin-bottom: 6px;
        padding: 12px;
        border: 1px solid rgba(50, 50, 93, 0.1);
        height: 44px;
        font-size: 16px;
        width: 100%;
        background: white;
    }

    #stripe-payment-form .result-message {
        line-height: 22px;
        font-size: 16px;
    }

    #stripe-payment-form .result-message a {
        color: rgb(89, 111, 214);
        font-weight: 600;
        text-decoration: none;
    }

    #stripe-payment-form .hidden {
        display: none;
    }

    #stripe-payment-form #stripe-card-error {
        color: rgb(105, 115, 134);
        text-align: left;
        font-size: 13px;
        line-height: 17px;
        margin-top: 12px;
    }

    #stripe-payment-form #stripe-card-element {
        border-radius: 4px 4px 0 0;
        padding: 12px;
        border: 1px solid rgba(50, 50, 93, 0.1);
        height: 44px;
        width: 100%;
        background: white;
    }

    #stripe-payment-form #payment-request-button {
        margin-bottom: 32px;
    }

    #stripe-payment-form button {
        background: #5469d4;
        color: #ffffff;
        font-family: Arial, sans-serif;
        border-radius: 0 0 4px 4px;
        border: 0;
        padding: 12px 16px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: block;
        transition: all 0.2s ease;
        box-shadow: 0 4px 5.5px 0 rgba(0, 0, 0, 0.07);
        width: 100%;
    }

    #stripe-payment-form button:hover {
        filter: contrast(115%);
    }

    #stripe-payment-form button:disabled {
        opacity: 0.5;
        cursor: default;
    }

    /* spinner/processing state, errors */
    #stripe-payment-form .spinner,
    #stripe-payment-form .spinner:before,
    #stripe-payment-form .spinner:after {
        border-radius: 50%;
    }

    #stripe-payment-form .spinner {
        color: #ffffff;
        font-size: 22px;
        text-indent: -99999px;
        margin: 0 auto;
        position: relative;
        width: 20px;
        height: 20px;
        box-shadow: inset 0 0 0 2px;
        -ms-transform: translateZ(0);
        transform: translateZ(0);
    }

    #stripe-payment-form .spinner:before,
    #stripe-payment-form .spinner:after {
        position: absolute;
        content: "";
    }

    #stripe-payment-form .spinner:before {
        width: 10.4px;
        height: 20.4px;
        background: #5469d4;
        border-radius: 20.4px 0 0 20.4px;
        top: -0.2px;
        left: -0.2px;
        -webkit-transform-origin: 10.4px 10.2px;
        transform-origin: 10.4px 10.2px;
        -webkit-animation: loading 2s infinite ease 1.5s;
        animation: loading 2s infinite ease 1.5s;
    }

    #stripe-payment-form .spinner:after {
        width: 10.4px;
        height: 10.2px;
        background: #5469d4;
        border-radius: 0 10.2px 10.2px 0;
        top: -0.1px;
        left: 10.2px;
        -webkit-transform-origin: 0 10.2px;
        transform-origin: 0 10.2px;
        -webkit-animation: loading 2s infinite ease;
        animation: loading 2s infinite ease;
    }

    @-webkit-keyframes loading {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes loading {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    @media only screen and (max-width: 600px) {
        #stripe-payment-form form {
            width: 80vw;
        }
    }
</style>

<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe = Stripe(@json($publicKey));
    var elements = stripe.elements();

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
            color: "#fa755a",
            iconColor: "#fa755a"
        }
    };

    var card = elements.create("card", {style: style});

    // Stripe injects an iframe into the DOM
    card.mount("#stripe-payment-form #stripe-card-element");
    card.on("change", function (event) {
        // Disable the Pay button if there are no card details in the Element
        document.querySelector("#stripe-payment-form button").disabled = event.empty;
        document.querySelector("#stripe-payment-form #stripe-card-error").textContent = event.error ? event.error.message : "";
    });

    var form = document.getElementById("stripe-payment-form");

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        // Complete payment when the submit button is clicked
        payWithCard(stripe, card);
    });

    // Calls stripe.confirmCardPayment
    // If the card requires authentication Stripe shows a pop-up modal to
    // prompt the user to enter authentication details without leaving your page.
    var payWithCard = function (stripe, card) {
        loading(true);
        stripe.confirmCardPayment(@json($intentSecret), {
            payment_method: {
                card: card
            }
        })
            .then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    showError(result.error.message);
                } else {
                    orderComplete(result.paymentIntent.id);
                }
            });
    };


    /* ------- UI helpers ------- */

    // Shows a success message when the payment is complete
    var orderComplete = function (paymentIntentId) {
        const returnUrl = @json($returnUrl)

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

        document.querySelector("#stripe-payment-form .result-message").classList.remove("hidden");
        document.querySelector("#stripe-payment-form button").disabled = true;
        loading(false);
    }

    // Show the customer the error from Stripe if their card fails to charge
    var showError = function (errorMsgText) {
        loading(false);
        var errorMsg = document.querySelector("#stripe-payment-form #stripe-card-error");
        errorMsg.textContent = errorMsgText;
        setTimeout(function () {
            errorMsg.textContent = "";
        }, 4000);
    };

    // Show a spinner on payment submission
    var loading = function (isLoading) {
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
