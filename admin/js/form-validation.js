/**
 *-----------------------------------------------------------------
 * Additional validation patterns
 *-----------------------------------------------------------------
 **/
$(function () {
   // alert(_system_script);
    $.validator.addMethod("validate_facebook_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)facebook\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("validate_twitter_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)twitter\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("validate_googleplus_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)plus.google\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("validate_linkedin_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)linkedin\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("validate_youtube_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)youtube\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("validate_pinterest_url", function (value, element) {
        return this.optional(element) || /^(https?:\/\/)?((w{3}\.)?)pinterest\.com\/(#!\/)?[a-z0-9_/+&%.?=]+$/i.test(value);
    });
    $.validator.addMethod("phonevalidate", function (value, element) {
        var value = value.split(" ").join("");
        value = value.replace(/\(|\)|\s+|-/g, '');
        return this.optional(element) || /^(?:[0-9] ?){6,14}[0-9]$/.test(value);
    });
    $.validator.addMethod("validate_prefix_code", function (value, element) {
        return this.optional(element) || /^\+(([2-9]{1}([0-9]{0,2})$)|([1]{1}?(\s)?([1-9]{1}[0-9]{2})$)|([1-9]{1}$))/i.test(value);
    });
    $.validator.addMethod("validate_name", function (value, element) {
        return this.optional(element) || /^[a-zA-Z\s\(\)\_\-\"\'\,\:\`\\\/\.\{\}\[\]]+$/i.test(value);
    });
    $.validator.addMethod("validate_date", function (value, element) {
        return this.optional(element) || /^\d\d?-\d\d-\d\d\d\d/.test(value);
    });
    $.validator.addMethod("validate_zipcode", function (value, element) {
        var value = value.split(" ").join("");
        return this.optional(element) || /^[0-9a-zA-Z\s{0,1}]{5,6}$/.test(value);
    });
	$.validator.addMethod('maxStrict', function (value, el, param) {
		return value <= param;
	});
});

$(function () {

    //Admin Start
    if (_system_script == 'Admin') {
        
        if ($('#_admin_form').length !== 0) {
            $('#_admin_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vFirstName: {required: true, minlength: 2},
                    vLastName: {required: true, minlength: 2},
                    vEmail: {required: true, email: true,
                        remote: {
                            url: _system_admin_url + 'ajax_validate_email.php',
                            type: "post",
                            data: {iAdminId: function () {
                                    return $("#iAdminId").val();
                                }},
                            async: false
                        }
                    },
                    vPassword: {required: function () {
                                    return $("#actionOf").val() == "Add";
                                }, minlength: 6},
                    vPhone: {required: true, phonevalidate: true},
                    iGroupId: {required: true}
                },
                messages: {
                    vFirstName: {
                        required: 'First Name is required.',
                        minlength: 'First Name at least 2 characters long.'
                    },
                    vLastName: {
                        required: 'Last Name is required.',
                        minlength: 'Last Name at least 2 characters long.'
                    },
                    vEmail: {
                        required: 'Email Address is required.',
                        remote: 'Email Address is already exist.'
                    },
                    vPassword: {
                        required: 'Password is required.',
                        minlength: 'Password at least 6 characters long.'
                    },
                    vPhone: {
                        required: 'Phone is required.',
                        phonevalidate: 'Please enter valid Phone Number.'
                    },
                    iGroupId: {
                        required: 'Group is required.'
                    }
                }
            });
        }
    }
    //Admin End
	

     //vehicles Start
    if (_system_script == 'Vehicle') {
        if ($('#_vehicle_form').length !== 0) {
            $('#_vehicle_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    iMakeId: {required: true},
                    iModelId: {required: true},
                    iYear: {required: true},
                    vLicencePlate: {required: true},
                    iCompanyId: {required: true},
                    iDriverId: {required: true},
                    'vCarType[]': {required: true}
                },
                messages: {
                    iMakeId: {
                        required: 'Make is required.'
                    },
                    iModelId: {
                        required: 'Model is required.'
                    },
                    iYear: {
                        required: 'Year is required.'
                    },
                    vLicencePlate: {
                        required: 'Licence Plate is required.'
                    },
                    iCompanyId: {
                        required: 'Company is required.'
                    },
                    iDriverId: {
                        required: 'Driver is required.'
                    },
                    'vCarType[]': {
                        required: 'Taxi Type is required.',
                    }
                }
            });
        }
    }
    //vehiclesp End
    
    //Coupon Start
    if (_system_script == 'Coupon') {
        if ($('#_coupon_form').length !== 0) {
            $('#_coupon_form').validate({

                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vCouponCode: {required: true},
                    tDescription: {required: true, minlength: 2},
                    fDiscount: {required: true, number: true, maxStrict: function () {
                                    if($("#eType").val() == "percentage"){
										return 100;
									}else {
										return 3000;
									}
                                }},
                    iUsageLimit: {required: true, number: true},
                    dActiveDate: {required: function () {
                                    return $("input[name='eValidityType']:checked").val() == "Defined";
                                }},
                    dExpiryDate: {required: function () {
                                    return $("input[name='eValidityType']:checked").val() == "Defined";
                                }}
                },
                messages: {
                    vCouponCode: {
                        required: 'Coupon Code is required.',
                    },
                    tDescription: {
                        required: 'Description is required.',
                        minlength: 'Description at least 2 characters long.'
                    },
                    fDiscount: {
                        required: 'Discount is required.',
						maxStrict: function () {
                                    if($("#eType").val() == "percentage"){
										return 'Please enter between 1 to 100 only.';
									}else {
										return 'Please enter between 1 to 3000 only.';
									}
                                }
                    },
                    iUsageLimit: {
                        required: 'Usage Limit is required.'
                    },
                    dActiveDate: {
                        required: 'Activation Date is required.'
                    },
                    dExpiryDate: {
                        required: 'Expiry Date is required.'
                    }
                }
            });
        }
    }
     //Coupon End
     


    //Company Start
    if (_system_script == 'Company') {
        if ($('#_company_form').length !== 0) {
            $('#_company_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vCompany: {required: true, minlength: 2},
                    vEmail: {required: true, email: true,
                        remote: {
                            url: _system_admin_url + 'ajax_validate_email.php',
                            type: "post",
                            data: {iCompanyId: function () {
                                    return $("#iCompanyId").val();
                                }},
                            async: false
                        }
                    },
                    vPassword: {required: function () {
                                    return $("#actionOf").val() == "Add";
                                }, minlength: 6},
                    vPhone: {required: true, phonevalidate: true},
                    vCaddress: {required: true, minlength: 2},
                   // vCity: {required: true},
                   // vState: {required: true},
                    vVatNum: {required: true, minlength: 2},
                    vCountry: {required: true}
                },
                messages: {
                    vCompany: {
                        required: 'Company Name is required.',
                        minlength: 'Company Name at least 2 characters long.'
                    },
                    vEmail: {
                        required: 'Email Address is required.',
                        remote: 'Email Address is already exist.'
                    },
                    vPassword: {
                        required: 'Password is required.',
                        minlength: 'Password at least 6 characters long.'
                    },
                    vPhone: {
                        required: 'Phone is required.',
                        phonevalidate: 'Please enter valid Phone Number.'
                    },
                    vCaddress: {
                        required: 'Address Line 1  is required.'
                    },
                    // vCity: {
                        // required: 'City is required.'
                    // },
                    // vState: {
                        // required: 'State is required.'
                    // },
                    vVatNum: {
                        required: 'Vat Number is required.'
                    },
                    vCountry: {
                        required: 'Country is required.'
                    }
                }
            });
        }
    }
    //Company End

    //Rider Start
    if (_system_script == 'Rider') {
        if ($('#_rider_form').length !== 0) {
            $('#_rider_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vName: {required: true, minlength: 2},
                    vLastName: {required: true, minlength: 2},
                    vEmail: {required: true, email: true,
                        remote: {
                            url: _system_admin_url + 'ajax_validate_email.php',
                            type: "post",
                            data: {iUserId: function () {
                                    return $("#iUserId").val();
                                }},
                            async: false
                        }
                    },
                    vImgName: {required: false, accept: "image/*"},
                    vPassword: {required: function () {
                                    return $("#actionOf").val() == "Add";
                                }, minlength: 6},
                    vCountry: {required: true},
                    // eGender: {required: true},
                    vPhone: {required: true, phonevalidate: true},
                    vLang: {required: true},
                    vCurrencyPassenger: {required: true}
                },
                messages: {
                    vName: {
                        required: 'First Name is required.',
                        minlength: 'First Name at least 2 characters long.'
                    },
                    vLastName: {
                        required: 'Last Name is required.',
                        minlength: 'Last Name at least 2 characters long.'
                    },
                    vEmail: {
                        required: 'Email Address is required.',
                        remote: 'Email Address is already exist.'
                    },
                    vPassword: {
                        required: 'Password is required.',
                        minlength: 'Password at least 6 characters long.'
                    },
                    vCountry: {
                        required: 'Country is required.'
                    },
                    vImgName: {accept: "Please select only image file."},
                    vPhone: {
                        required: 'Phone is required.',
                        phonevalidate: 'Please enter valid Phone Number.'
                    },
                    vLang: {
                        required: 'Language is required.'
                    },
                    vCurrencyPassenger: {
                        required: 'Currency is required.'
                    }
                }
            });
        }
    }
    //rider End

    //make Start
    if (_system_script == 'Make') {
        if ($('#_make_form').length !== 0) {
            $('#_make_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vMake: {required: true, minlength: 2}
                },
                messages: {
                    vMake: {
                        required: 'Make Name is required.',
                        minlength: 'Make Name at least 2 characters long.'
                    }
                }
            });
        }
    }
    //make End

    //model Start
    if (_system_script == 'Model') {
        if ($('#_model_form').length !== 0) {
            $('#_model_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vTitle: {required: true, minlength: 2}
                },
                messages: {
                    vTitle: {
                        required: 'Model Name is required.',
                        minlength: 'Model Name at least 2 characters long.'
                    }
                }
            });
        }
    }
    //model End

    //country Start
    if (_system_script == 'Country') {

        if ($('#_country_form').length !== 0) {
            $('#_country_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vCountry: {required: true, minlength: 2},
                    vCountryCode: {required: true},
                    // vCountryCodeISO_3: {required: true},
                    vPhoneCode: {required: true}
                },
                messages: {
                    vCountry: {
                        required: 'Country Name is required.',
                        minlength: 'Country Name at least 2 characters long.'
                    },
                    vCountryCode: {
                        required: 'Country Code is required.',
                        minlength: 'Country Code Name at least 2 characters long.'
                    },
                    // vCountryCodeISO_3: {
                        // required: 'CountryCodeISO is required.'
                    // },
                    vPhoneCode: {
                        required: 'Phone Code is required.'
                    }
                }
            });
        }
    }
    //country End
	
	
	//State Start
    if (_system_script == 'State') {
        if ($('#_state_form').length !== 0) {
            $('#_state_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vCountry: {required: true},
                    vState: {required: true},
                    vStateCode: {required: true},
                },
                messages: {
                    vCountry: {
                        required: 'Country is required.',
                    },
                    vState: {
                        required: 'State Name is required.'
                    },
					vStateCode: {
                        required: 'State Code is required.'
                    }
                }
            });
        }
    }
    //State End
	
	
	//State Start
    if (_system_script == 'city') {
        if ($('#_city_form').length !== 0) {
            $('#_city_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vCountry: {required: true},
                    vState: {required: true},
                    vCity: {required: true},
                },
                messages: {
                    vCountry: {
                        required: 'Country is required.',
                    },
                    vState: {
                        required: 'State is required.'
                    },
					vCity: {
                        required: 'City Name is required.'
                    }
                }
            });
        }
    }
    //State End

    //faq Start
    if (_system_script == 'FAQ') {
        //alert('hi');
        if ($('#_faq_form').length !== 0) {
            $('#_faq_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vTitle_EN: {required: true, minlength: 2}
                },
                messages: {
                    vTitle_EN: {
                        required: 'English Question is required.',
                        minlength: 'English Question at least 2 characters long.'
                    }
                }
            });
        }
    }
    //faq End



    //FAQ_CAT Start
    if (_system_script == 'FAQ_CAT') {
        if ($('#_faq_cat_form').length !== 0) {
            $('#_faq_cat_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vTitle_EN: {required: true, minlength: 2}
                },
                messages: {
                    vTitle_EN: {
                        required: 'English label is required.',
                        minlength: 'English label at least 2 characters long.'
                    }
                }
            });
        }
    }
    //FAQ_CAT End

    //Pages Start
    if (_system_script == 'Pages') {
        if ($('#_page_form').length !== 0) {
            $('#_page_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vPageTitle_EN: {required: true, minlength: 2}
                },
                messages: {
                    vPageTitle_EN: {
                        required: 'PageTitle Value is required.',
                        minlength: 'PageTitle Value at least 2 characters long.'
                    }
                }
            });
        }
    }
    //Pages End


    //Languages Start
    if (_system_script == 'languages') {
        //alert('1111');
        if ($('#_languages_form').length !== 0) {
            $('#_languages_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vLabel: {required: true, minlength: 2},
                    vValue_EN: {required: true, minlength: 2}
                },
                messages: {
                    vLabel: {
                        required: 'Language Label is required.',
                        minlength: 'Language Label at least 2 characters long.'
                    },
                    vValue_EN: {
                        required: 'English Value is required.',
                        minlength: 'English Value at least 2 characters long.'
                    }
                }
            });
        }
    }
    //Languages End

    //Languages Other Label
    if (_system_script == 'language_label_other') {
        //alert('1111');
        if ($('#_language_label_other_form').length !== 0) {
            $('#_language_label_other_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vLabel: {required: true, minlength: 2},
                    vValue_EN: {required: true, minlength: 2}
                },
                messages: {
                    vLabel: {
                        required: 'Language Label is required.',
                        minlength: 'Language Label at least 2 characters long.'
                    },

                    vValue_EN: {
                        required: 'English Value is required.',
                        minlength: 'English Value at least 2 characters long.'
                    }
                }
            });
        }
    }
    //Languages Other Label


    //Driver Start
    if (_system_script == 'Driver') {
        if ($('#_driver_form').length !== 0) {
            $('#_driver_form').validate({
                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vName: {required: true, minlength: 2},
                    vLastName: {required: true, minlength: 2},
                    vEmail: {required: true, email: true,
                        remote: {
                            url: _system_admin_url + 'ajax_validate_email.php',
                            type: "post",
                            data: {iDriverId: function () {
                                    return $("#iDriverId").val();
                                }},
                            async: false
                        }
                    },
                    vPassword: {required: function () {
                                    return $("#actionOf").val() == "Add";
                                }, minlength: 6},
                    vPhone: {required: true, phonevalidate: true},
                    vImage: {required: false, accept: 'image/*'}, //, accept: 'image/*'
                    vCountry: {required: true},
                    iCompanyId: {required: true},
                    // eGender: {required: true},
                   // dBirthDate: {required: true},
				    vDay: {required: true},
                    vMonth: {required: true},
                    vYear: {required: true},
                    vLang: {required: true},
                    vCurrencyDriver: {required: true},
                    vPaymentEmail: {required: false, email: true}
                },
                messages: {
                    vName: {
                        required: 'First Name is required.',
                        minlength: 'First Name at least 2 characters long.'
                    },
                    vLastName: {
                        required: 'Last Name is required.',
                        minlength: 'Last Name at least 2 characters long.'
                    },
                    vEmail: {
                        required: 'Email Address is required.',
                        remote: 'Email Address is already exist.'
                    },
                    vPassword: {
                        required: 'Password is required.',
                        minlength: 'Password at least 6 characters long.'
                    },
                    vPhone: {
                        required: 'Phone number is required.',
                        phonevalidate: 'Please enter valid Phone Number.'
                    },
                    vCountry: {
                        required: 'Country is required.'
                    },
                    iCompanyId: {
                        required: 'Company is required.'
                    },
                    /* dBirthDate: {
                        required: 'Birth Date is required.'
                    }, */
					vDay: {
                        required: 'Birth Date is required.'
                    },
					
					vMonth: {
                        required: 'Birth Month is required.'
                    },
					vYear: {
                        required: 'Birth Year is required.'
                    },
                    vLang: {
                        required: 'Language is required.'
                    },
                    vCurrencyDriver: {
                        required: 'Currency is required.'
                    }
                }
            });
        }
    }
    //Driver End

    //Vehicle Type Start
    if (_system_script == 'VehicleType') {
        if ($('#_vehicleType_form').length !== 0) {
            $('#_vehicleType_form').validate({

                ignore: 'input[type=hidden]',
                errorClass: 'help-block',
                errorElement: 'span',
                errorPlacement: function (error, e) {
                    e.parents('.row > div').append(error);
                },
                highlight: function (e) {
                    $(e).closest('.row').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function (e) {
                    e.closest('.row').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                    e.closest('.help-inline').remove();
                },
                rules: {
                    vVehicleType: {required: true},
                    vVehicleType_EN: {required: true},
                    fVisitFee: {required: true, number: true},
                    fPricePerKM: {required: true, number: true},
                    fPricePerMin: {required: true, number: true},
                    fPricePerHour: {required: true, number: true},
                },
                messages: {
                    vVehicleType: {
                        required: 'Vehicle type is required.'
                    },
                    vVehicleType_EN: {
                        required: 'Vehicle type (English) is required.'
                    },
                    fVisitFee: {
                        required: 'Visit fee is required.'
                    },
                    fPricePerKM: {
                        required: 'Price per KM is required.'
                    },
                    fPricePerMin: {
                        required: 'Price per Minute is required.'
                    },
                    fPricePerHour: {
                        required: 'Price per Hour is required.',
                        minlength: 'dExpiryDate at least 2 characters long.'
                    },
                }
            });
        }
    }
    //Vehicle Type End

});