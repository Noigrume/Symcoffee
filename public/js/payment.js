const stripe = Stripe(stripePublicKey);

const elements = stripe.elements();

const style = {
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

//formulaire de carte bleue
const card = elements.create("card", {style: style});

// Stripe injects an iframe into the DOM
card.mount("#card-element");

//À la soumission, on reçoit un évènement
card.on("change", function (event) {
    // Disable the Pay button if there are no card details in the Element
    document.querySelector("button").disabled = event.empty;
    document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
});

//Récupération du formulaire
const form = document.getElementById("payment-form");
form.addEventListener("submit", function (event) {
    event.preventDefault();
    // payer avec stripe à la soumission
    stripe
        //confirmation du paiement par carte
        .confirmCardPayment(clientSecret, {
            payment_method: {
                card: card
            }
        })
        .then(function (result) {
            if (result.error) {
                // Show error to your customer
                console.log(result.error.message);
            } else {
                // si le paiement a réussi, on redirige
                window.location.href = successRedirection;
            }
        });
});