MortgageCenter = (function() {
	var $ = jQuery;
	var returnObj;
	
	function getRates() {
		$.ajax({
			url: 'http://www.zillow.com/webservice/GetRateSummary.htm',
			type: 'GET',
			dataType: 'script',
			data: {
				'zws-id': MortgageCenter.zillowApiKey,
				'state': MortgageCenter.state,
				'output': 'json',
				'callback': 'MortgageCenter.getRatesCallback'
			}
		});
	}
	function calculatePayment() {
		$.ajax({
			url: 'http://www.zillow.com/webservice/GetMonthlyPayments.htm',
			type: 'GET',
			dataType: 'script',
			data: {
				'zws-id': MortgageCenter.zillowApiKey,
				'price': $('#mortgage-center-calc-hp').val(),
				'down': $('#mortgage-center-calc-pd').val(),
				'zip': $('#mortgage-center-calc-zip').val(),
				'output': 'json',
				'callback': 'MortgageCenter.calculatePaymentCallback'
			}
		});
	}
	
	returnObj = {
		getRatesCallback: function(data) {
			var rates = data.response;
			
			$('#mortgage-center-rates-cur-30yf').html(rates.today.thirtyYearFixed + '%');
			$('#mortgage-center-rates-cur-15yf').html(rates.today.fifteenYearFixed + '%');
			$('#mortgage-center-rates-cur-51arm').html(rates.today.fiveOneARM + '%');
			$('#mortgage-center-rates-last-30yf').html(rates.lastWeek.thirtyYearFixed + '%');
			$('#mortgage-center-rates-last-15yf').html(rates.lastWeek.fifteenYearFixed + '%');
			$('#mortgage-center-rates-last-51arm').html(rates.lastWeek.fiveOneARM + '%');
		},
		calculatePaymentCallback: function(data) {
			var paymentInfo = data.response;
			
			//$('#mortgage-center-calc-30yf-mp').html();
			$('#mortgage-center-calc-30yf-r').html(paymentInfo.thirtyYearFixed.rate + '%');
			$('#mortgage-center-calc-30yf-pi').html('$' + paymentInfo.thirtyYearFixed.monthlyPrincipalAndInterest);
			$('#mortgage-center-calc-30yf-i').html('$' + paymentInfo.thirtyYearFixed.monthlyMortgageInsurance);
			$('#mortgage-center-calc-30yf-t').html('$' + paymentInfo.monthlyPropertyTaxes);
			
			//$('#mortgage-center-calc-15yf-mp').html();
			$('#mortgage-center-calc-15yf-r').html(paymentInfo.thirtyYearFixed.rate + '%');
			$('#mortgage-center-calc-15yf-pi').html('$' + paymentInfo.fifteenYearFixed.monthlyPrincipalAndInterest);
			$('#mortgage-center-calc-15yf-i').html('$' + paymentInfo.fifteenYearFixed.monthlyMortgageInsurance);
			$('#mortgage-center-calc-15yf-t').html('$' + paymentInfo.monthlyPropertyTaxes);
			
			//$('#mortgage-center-calc-51arm-mp').html();
			$('#mortgage-center-calc-51arm-r').html(paymentInfo.fiveOneARM.rate + '%');
			$('#mortgage-center-calc-51arm-pi').html('$' + paymentInfo.fiveOneARM.monthlyPrincipalAndInterest);
			$('#mortgage-center-calc-51arm-i').html('$' + paymentInfo.fiveOneARM.monthlyMortgageInsurance);
			$('#mortgage-center-calc-51arm-t').html('$' + paymentInfo.monthlyPropertyTaxes);
		}
	};
	
	$(getRates);
	$(function() {
		$('#mortgage-center-calc-submit').click(calculatePayment);
	});
	return returnObj;
})();