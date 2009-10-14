MortgageCenter = (function() {
	var $ = jQuery;
	var returnObj;
	
	function getRates() {
		if ($('#mortgage-center-rates-cur-30yf').length == 0)
			return;
		
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
		$('.mortgage-center-calc-value').html('...');
		$.ajax({
			url: 'http://www.zillow.com/webservice/GetMonthlyPayments.htm',
			type: 'GET',
			dataType: 'script',
			data: {
				'zws-id': MortgageCenter.zillowApiKey,
				'price': $('#mortgage-center-calc-hp').val().replace(/,/g, ''),
				'down': $('#mortgage-center-calc-pd').val(),
				'zip': $('#mortgage-center-calc-zip').val(),
				'output': 'json',
				'callback': 'MortgageCenter.calculatePaymentCallback'
			}
		});
	}
	// Keith Jenci's, didn't feel like making my own
	function humanReadableNumber(nStr) {
		nStr += '';
		var x = nStr.split('.');
		var x1 = x[0];
		var x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
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
			
			$('#mortgage-center-calc-30yf-mp').html('$' + humanReadableNumber(
				parseInt(paymentInfo.thirtyYearFixed.monthlyPrincipalAndInterest)
				+ parseInt(paymentInfo.thirtyYearFixed.monthlyMortgageInsurance ? paymentInfo.thirtyYearFixed.monthlyMortgageInsurance : 0)
				+ parseInt(paymentInfo.monthlyPropertyTaxes)
			));
			$('#mortgage-center-calc-30yf-r').html(paymentInfo.thirtyYearFixed.rate + '%');
			$('#mortgage-center-calc-30yf-pi').html('$' + humanReadableNumber(paymentInfo.thirtyYearFixed.monthlyPrincipalAndInterest));
			$('#mortgage-center-calc-30yf-i').html('$' + humanReadableNumber((paymentInfo.thirtyYearFixed.monthlyMortgageInsurance ? paymentInfo.thirtyYearFixed.monthlyMortgageInsurance : 0)));
			$('#mortgage-center-calc-30yf-t').html('$' + humanReadableNumber(paymentInfo.monthlyPropertyTaxes));
			
			$('#mortgage-center-calc-15yf-mp').html('$' + humanReadableNumber(
				parseInt(paymentInfo.fifteenYearFixed.monthlyPrincipalAndInterest)
				+ parseInt(paymentInfo.fifteenYearFixed.monthlyMortgageInsurance ? paymentInfo.fifteenYearFixed.monthlyMortgageInsurance : 0)
				+ parseInt(paymentInfo.monthlyPropertyTaxes)
			));
			$('#mortgage-center-calc-15yf-r').html(paymentInfo.fifteenYearFixed.rate + '%');
			$('#mortgage-center-calc-15yf-pi').html('$' + humanReadableNumber(paymentInfo.fifteenYearFixed.monthlyPrincipalAndInterest));
			$('#mortgage-center-calc-15yf-i').html('$' + humanReadableNumber(paymentInfo.fifteenYearFixed.monthlyMortgageInsurance ? paymentInfo.fifteenYearFixed.monthlyMortgageInsurance : 0));
			$('#mortgage-center-calc-15yf-t').html('$' + humanReadableNumber(paymentInfo.monthlyPropertyTaxes));
			
			$('#mortgage-center-calc-51arm-mp').html('$' + humanReadableNumber(
				parseInt(paymentInfo.fiveOneARM.monthlyPrincipalAndInterest)
				+ parseInt(paymentInfo.fiveOneARM.monthlyMortgageInsurance ? paymentInfo.fiveOneARM.monthlyMortgageInsurance : 0)
				+ parseInt(paymentInfo.monthlyPropertyTaxes)
			));
			$('#mortgage-center-calc-51arm-r').html(paymentInfo.fiveOneARM.rate + '%');
			$('#mortgage-center-calc-51arm-pi').html('$' + humanReadableNumber(paymentInfo.fiveOneARM.monthlyPrincipalAndInterest));
			$('#mortgage-center-calc-51arm-i').html('$' + humanReadableNumber(paymentInfo.fiveOneARM.monthlyMortgageInsurance ? paymentInfo.fiveOneARM.monthlyMortgageInsurance : 0));
			$('#mortgage-center-calc-51arm-t').html('$' + humanReadableNumber(paymentInfo.monthlyPropertyTaxes));
			
			$('#mortgage-center-calculator-table').css('display', '');
		}
	};
	
	$(getRates);
	$(function() {
		$('#mortgage-center-calc-submit').click(calculatePayment);
		$('#mortgage-center-calc-submit').click();
	});
	return returnObj;
})();