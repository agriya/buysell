__slice = [].slice,
__indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; },
_this = this;

var cards = [
    {
      type: 'maestro',
      pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
      length: [12, 13, 14, 15, 16, 17, 18, 19],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'dinersclub',
      pattern: /^(36|38|30[0-5])/,
      length: [14],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'laser',
      pattern: /^(6706|6771|6709)/,
      length: [16, 17, 18, 19],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'jcb',
      pattern: /^35/,
      length: [16],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'unionpay',
      pattern: /^62/,
      length: [16, 17, 18, 19],
      luhn: false
    }, {
      type: 'discover',
      pattern: /^(6011|65|64[4-9]|622)/,
      length: [16],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'mastercard',
      pattern: /^5[1-5]/,
      length: [16],
      cvcLength: [3],
      luhn: true
    }, {
      type: 'amex',
      pattern: /^3[47]/,
      length: [15],
      cvcLength: [3, 4],
      luhn: true
    }, {
      type: 'visa',
      pattern: /^4/,
      length: [13, 14, 15, 16],
      cvcLength: [3],
      luhn: true
    }
];

function cardFromNumber(num) {
    var card, _i, _len;
    num = (num + '').replace(/\D/g, '');
    for (_i = 0, _len = cards.length; _i < _len; _i++) {
      card = cards[_i];
      if (card.pattern.test(num)) {
        return card;
      }
    }
};

function validateCardNumber(num) {
    var card, _ref;
    num = (num + '').replace(/\s+|-/g, '');
    if (!/^\d+$/.test(num)) {
      return false;
    }
    card = cardFromNumber(num);
    if (!card) {
      return false;
    }
    return (_ref = num.length, __indexOf.call(card.length, _ref) >= 0) && (card.luhn === false || luhnCheck(num));
};

luhnCheck = function(num) {
    var digit, digits, odd, sum, _i, _len;
    odd = true;
    sum = 0;
    digits = (num + '').split('').reverse();
    for (_i = 0, _len = digits.length; _i < _len; _i++) {
      digit = digits[_i];
      digit = parseInt(digit, 10);
      if ((odd = !odd)) {
        digit *= 2;
      }
      if (digit > 9) {
        digit -= 9;
      }
      sum += digit;
    }
    return sum % 10 === 0;
};

function validateCardCVC(cvc, type) {
    var _ref, _ref1;
    cvc = $.trim(cvc);
    if (!/^\d+$/.test(cvc)) {
      return false;
    }
    if (type) {
      return _ref = cvc.length, __indexOf.call((_ref1 = cardFromType(type)) != null ? _ref1.cvcLength : void 0, _ref) >= 0;
    } else {
      return cvc.length >= 3 && cvc.length <= 4;
    }
};

function cardFromType(type) {
    var card, _i, _len;
    for (_i = 0, _len = cards.length; _i < _len; _i++) {
      card = cards[_i];
      if (card.type === type) {
        return card;
      }
    }
};

function validateCardExpiry(month, year) {
	console.log(month+'==='+year);
    var currentTime, expiry, _ref;
    if (typeof month === 'object' && 'month' in month) {
      _ref = month, month = _ref.month, year = _ref.year;
    }
    if (!(month && year)) {
      return false;
    }
    month = $.trim(month);
    year = $.trim(year);
    if (!/^\d+$/.test(month)) {
      return false;
    }
    if (!/^\d+$/.test(year)) {
      return false;
    }
    if (!(parseInt(month, 10) <= 12)) {
      return false;
    }
    expiry = new Date(year, month);
    currentTime = new Date;
    expiry.setMonth(expiry.getMonth() - 1);
    expiry.setMonth(expiry.getMonth() + 1, 1);
    return expiry > currentTime;
};

function cardExpiryVal(value) {
	console.log(value);
    var month, prefix, year, _ref;
    value = value.replace(/\s/g, '');
    _ref = value.split('/', 2), month = _ref[0], year = _ref[1];
    month = parseInt(month, 10);
    year = parseInt(year, 10);
    return {
      month: month,
      year: year
    };
};



function cardValidation() {
	$("#card_error").hide();
	var card_number = $("#credit_card_number").val();
	var expiry_date = $("#credit_card_expire").val();
	var cvv = $("#credit_card_code").val();
	var card_number_status = validateCardNumber(card_number);
	if(!card_number_status) {
		$("#card_error").show();
		$("#card_error").html('Your card number is incorrect.');
		return false;
	}
	var card = cardFromNumber(card_number);
	var card_type = card.type;
	$("#card_type").val(card_type);
	var card_expiry = cardExpiryVal(expiry_date);
	var expiry_status = validateCardExpiry(card_expiry);
	if(!expiry_status) {
		$("#card_error").show();
		$("#card_error").html('Your card\'s expiration month is invalid.');
		return false;
	}
	var cvv_status = validateCardCVC(cvv, card_type);
	if(!cvv_status) {
		$("#card_error").show();
		$("#card_error").html('Your card\'s security code is invalid.');
		return false;
	}
	return true;
}