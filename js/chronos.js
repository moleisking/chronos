jQuery(document).ready(function ($) { 
    
    $('.frmAppointmentCreate').ajaxForm({
        type: 'post',       
        success: function (response) {
            console.log(response);
        },
        error: function (response) {
            console.log(response);
        },
        beforeSubmit: function(arr, $form, options) { 
            //console.log(arr);                     
        }
    });   

    $('.frmAppointmentDelete').ajaxForm({
        type: 'post',       
        success: function (response) {
            console.log(response);
        },
        error: function (response) {
            console.log(response);
        },
        beforeSubmit: function(arr, $form, options) { 
            //console.log(arr);                     
        }
    });   

    $('.frmTimeslotCreate').ajaxForm({
        type: 'post',       
        success: function (response) {
            console.log(response);
        },
        error: function (response) {
            console.log(response);
        },
        beforeSubmit: function(arr, $form, options) { 
            //console.log(arr);                     
        }
    });   

    $('.frmTimeslotDelete').ajaxForm({
        type: 'post',       
        success: function (response) {
            console.log(response);
        },
        error: function (response) {
            console.log(response);
        },
        beforeSubmit: function(arr, $form, options) { 
            //console.log(arr);                     
        }
    });   

});

function showDiv(uniqueId) {
    document.getElementById(uniqueId).style.display = 'block';
}

function hideDiv(uniqueId) {
    document.getElementById(uniqueId).style.display = 'none';
}