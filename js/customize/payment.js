//payment script
//copied from paypal website
//example source https://www.paypal.com/buttons/smart?flowloggingId=8a47de0f82d100
function initPayPalButton() {
	console.log("initPayPalButton invoked");
    var description = document.querySelector('#smart-button-container #description');
    var amount = document.querySelector('#smart-button-container #amount');
    var descriptionError = document.querySelector('#smart-button-container #descriptionError');
    var priceError = document.querySelector('#smart-button-container #priceLabelError');
    var invoiceid = document.querySelector('#smart-button-container #invoiceid');
    var invoiceidError = document.querySelector('#smart-button-container #invoiceidError');
    var invoiceidDiv = document.querySelector('#smart-button-container #invoiceidDiv');

    var elArr = [description, amount];

    if (invoiceidDiv.firstChild.innerHTML.length > 1) {
      invoiceidDiv.style.display = "block";
    }

    var purchase_units = [];
    purchase_units[0] = {};
    purchase_units[0].amount = {};

    function validate(event) {
      return event.value.length > 0;
    }

    paypal.Buttons({
      style: {
        color: 'blue',
        shape: 'pill',
        label: 'pay',
        layout: 'vertical',
        
      },

      onInit: function (data, actions) {
        actions.disable();

        if(invoiceidDiv.style.display === "block") {
          elArr.push(invoiceid);
        }

        elArr.forEach(function (item) {
          item.addEventListener('keyup', function (event) {
            var result = elArr.every(validate);
            if (result) {
              actions.enable();
            } else {
              actions.disable();
            }
          });
        });
      },

      onClick: function () {
        if (description.value.length < 1) {
          descriptionError.style.visibility = "visible";
        } else {
          descriptionError.style.visibility = "hidden";
        }

        if (amount.value.length < 1) {
          priceError.style.visibility = "visible";
        } else {
          priceError.style.visibility = "hidden";
        }

        if (invoiceid.value.length < 1 && invoiceidDiv.style.display === "block") {
          invoiceidError.style.visibility = "visible";
        } else {
          invoiceidError.style.visibility = "hidden";
        }

        purchase_units[0].description = description.value;
        purchase_units[0].amount.value = amount.value;

        if(invoiceid.value !== '') {
          purchase_units[0].invoice_id = invoiceid.value;
        }
      },

      createOrder: function (data, actions) {
        return actions.order.create({
          purchase_units: purchase_units,
        });
      },

      onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
	        //alert('Transaction completed by ' + details.payer.name.given_name + '!');
	          
			// inject the alert to .messages div in our form
	         console.log('Transaction completed by ' + details.payer.name.given_name + '!');
	         var successResponseDescr1 = '<div class="col-lg-2 col-sm-3 col-xs-6 col-centered feature-title"> <h2>';
	         var successResponseDescr2 = 'Payment completed by '+details.payer.name.given_name ;
	         var successResponseDescr3 = '</div> </h2>';
	         var sucessResponseAll=successResponseDescr1 + successResponseDescr2 +successResponseDescr3
	         $('#payment-form')[0].reset(); //reset the payment fields
			 $('#payments').find('.paymentResponse').html(sucessResponseAll);
        });
      },

      onError: function (err) {
        console.log(err);
        $('#payments').find('.paymentResponse').html("<h2> Transaction failed to process for your request "+details.payer.name.given_name + '! </h2' +err);
      }
    }).render('#paypal-button-container');
  }
  initPayPalButton();