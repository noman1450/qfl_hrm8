$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

    function isInt(n) {
        return n % 1 === 0;
    }

   function isFloat(x) {
        // return !!(x % 1);s
        return Number(x) === x && x % 1 !== 0;
   }

   //checking if the string is empty or not
   function isBlank(str) {
        return (!str || /^\s*$/.test(str));
   }

   function calcDaysBetween(startDate, endDate) {
        return (endDate - startDate) / (1000 * 60 * 60 * 24);
   }

   function IsNumeric(input){
        var RE = /^-{0,1}\d*\.{0,1}\d+$/;
        return (RE.test(input));
   }

   function isNegative(selector) {
    let input = document.querySelector(selector)

    var events = ['keyup', 'change']

    if (input) {
        addEventToElement(input, events, checkNegative)
    }
}

function checkNegative() {
    if (this.value === "") return

    if (this.value < 0) {
        alert("You can't give negative value")

        this.value = 0
    }
}

function addEventToElement(element, events, handler) {
    events.forEach(event => element.addEventListener(event, handler, false))
}


// dynamic form Submit
$(document).on('submit', '.dynamicFormSubmit', function (e) {
    e.preventDefault();
    var isValid = 0;
    $('.required').each(function () {
        $(this).keyup(function () {
            $(this).css("border", "1px solid #ccc");
        });

        $(this).change(function () {
            $(this).next('span').css("border", "1px solid #ccc");
        });

        if ($(this).val() == "") {
            $(this).css("border", "1px solid red");
            $(this).next('span').css("border", "1px solid red");
            $(this).next('.chosen-container').css("border", "1px solid red");
            isValid = 1;
            // return false;
        } else {
            $(this).css("border", "1px solid #ccc");
            $(this).next('span').css("border", "1px solid #ccc");
            $(this).next('.chosen-container').css("border", "1px solid #ccc");
        }
    });

    if (isValid == 0) {
        if (confirm("Are You Sure?")) {
            var postUrl = $(this).attr('action');
            var tableName = $(this).data('table-name');
            var redirectUrl = $(this).attr('redirectUrl');
            var formData = new FormData(this);
            $(".submit-button").attr("disabled", true);
            e.preventDefault();
            //
            $.ajax({
                type: "POST",
                url: postUrl,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    $(".loadingImg").html("<img src='/img/loader-small.gif' />");
                    //$(".loadingImg").html('<i class="fa fa-refresh fa-spin"></i>');
                },

                success: function (data) {
                    /*Check form Validation*/
                    if(data['error'] != ''){
                        var errors = data['error'];
                        $(".submit-button").removeAttr("disabled");
                        //--
                        $(".frmMsg").html("<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Warning!</strong> "+ errors +"</div>");
                        //--
                        clearInputErrors()

                        $.each(errors, function (i, error) {
                            var el = $(document).find('[id="'+i+'"]');
                            el.after($('<p style="color: red;" class="show-error">'+error[0]+'</p>'));
                        });
                    }
                    /*Show Message*/
                    var messageClass = data['status'] === true ? "success" : 'danger';
                    var messageType = data['status'] === true ? "Success" : 'Warning';
                    var message = data['message'];
                    $(".frmMsg").html("");
                    $(".frmMsg").html(`<div class="alert alert-${messageClass} alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>${messageType}!</strong> ${message}</div>`);
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    $(".loadingImg").html("");
                    //

                    if(data['status'] === true) {
                        if (redirectUrl !== '' && typeof redirectUrl !== 'undefined') {
                            setTimeout(function() {// wait for 1 sec
                                window.location.href = redirectUrl;
                            }, 1000);
                        } else {
                            $('#showDetailModal').modal('hide')

                            $(tableName).DataTable().ajax.reload()

                            setTimeout(function() {// wait for 2 sec
                                $(".frmMsg").html("");
                            }, 2000);
                        }
                    } else {
                        $(".submit-button").removeAttr("disabled");

                        setTimeout(function() {// wait for 1 sec
                            $(".frmMsg").html("");
                        }, 10000);
                    }
                }
            });
        } else {
            return false;
        }
    } else {
        return false;
    }
});


$(document).on('submit', '.dynamicFormSubmit2', function (e) {
    e.preventDefault();
    var isValid = 0;
    $('.required').each(function () {
        $(this).keyup(function () {
            $(this).css("border", "1px solid #ccc");
        });

        $(this).change(function () {
            $(this).next('span').css("border", "1px solid #ccc");
        });

        if ($(this).val() == "") {
            $(this).css("border", "1px solid red");
            $(this).next('span').css("border", "1px solid red");
            $(this).next('.chosen-container').css("border", "1px solid red");
            isValid = 1;
            // return false;
        } else {
            $(this).css("border", "1px solid #ccc");
            $(this).next('span').css("border", "1px solid #ccc");
            $(this).next('.chosen-container').css("border", "1px solid #ccc");
        }
    });

    if (isValid == 0) {
        if (confirm("Are You Sure?")) {
            var postUrl = $(this).attr('action');
            var tableName = $(this).data('table-name');
            var redirectUrl = $(this).attr('redirectUrl');
            var formData = new FormData(this);
            $(".submit-button").attr("disabled", true);
            e.preventDefault();
            //
            $.ajax({
                type: "POST",
                url: postUrl,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    $(".loadingImg").html("<img src='/img/loader-small.gif' />");
                    //$(".loadingImg").html('<i class="fa fa-refresh fa-spin"></i>');
                },

                success: function (data) {
                    /*Check form Validation*/
                    if(data['error'] != ''){
                        var errors = data['error'];
                        $(".submit-button").removeAttr("disabled");
                        //--
                        $(".frmMsg").html("<div class='alert alert-danger alert-dismissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Warning!</strong> "+ errors +"</div>");
                        //--
                        clearInputErrors()

                        $.each(errors, function (i, error) {
                            var el = $(document).find('[id="'+i+'"]');
                            el.after($('<p style="color: red;" class="show-error">'+error[0]+'</p>'));
                        });
                    }
                    /*Show Message*/
                    var messageClass = data['status'] === true ? "success" : 'danger';
                    var messageType = data['status'] === true ? "Success" : 'Warning';
                    var message = data['message'];
                    $(".frmMsg").html("");
                    $(".frmMsg").html(`<div class="alert alert-${messageClass} alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>${messageType}!</strong> ${message}</div>`);
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    $(".loadingImg").html("");
                    //

                    if(data['status'] === true) {
                        if (redirectUrl !== '' && typeof redirectUrl !== 'undefined') {
                            setTimeout(function() {// wait for 1 sec
                                window.location.href = redirectUrl;
                            }, 1000);
                        } else {
                            $('#showDetailModal').modal('hide')

                            $(tableName).DataTable().ajax.reload()

                            setTimeout(function() {// wait for 2 sec
                                $(".frmMsg").html("");
                            }, 2000);
                        }
                    } else {
                        $(".submit-button").removeAttr("disabled");

                        setTimeout(function() {// wait for 1 sec
                            $(".frmMsg").html("");
                        }, 10000);
                    }
                }
            });
        } else {
            return false;
        }
    } else {
        return false;
    }
});

function clearInputErrors() {
    const clearErrors = document.querySelectorAll('.show-error')
    clearErrors.forEach((element) => element.textContent = '')
}


function select2Dropdown(selector, url, placeholder = 'Search') {
    return $(selector).select2({
        placeholder: placeholder,
        width: '100%',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: url,
            delay: 100,
            data: function(params) {
                return {
                    term: params.term
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data
                };
            },
        },
    });
}

//===== Modal View =====//
$(document).on("click", ".modalLink", function(e) {
    e.preventDefault();

    var modal_size = $(this).data('modal-size');

    var footerNone = $(this).is("[footer-none]");
    var modalCenter = $(this).is("[modal-center]");

    if (footerNone) {
        $('#modal-footer').css('display', 'none')
    }

    if (modalCenter) {
        $("#modalSize").addClass('modal-dialog-centered');
    }

    if (modal_size !== '' && typeof modal_size !== typeof undefined && modal_size !== false) {
        $("#modalSize").addClass('modal-'+modal_size);
    } else {
        $("#modalSize").addClass('modal-md');
    }
    var title = $(this).data('title');

    $("#showDetailModalTile").html(title);
    //

    $.ajax({
        type: "GET",
        url: $(this).attr('href'),
        beforeSend: function () {
            $("#showDetailModalBody").html(`<div style="display:flex;align-items:center;justify-content:center;flex-direction:column">
                    <svg version="1.1" id="L4" style="width: 100px; height: 100px; display:inline-block;" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                        <circle fill="#9ca3af" stroke="none" cx="6" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite"
                            begin="0.1"/>
                        </circle>
                        <circle fill="#9ca3af" stroke="none" cx="26" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite"
                            begin="0.2"/>
                        </circle>
                        <circle fill="#9ca3af" stroke="none" cx="46" cy="50" r="6">
                            <animate
                            attributeName="opacity"
                            dur="1s"
                            values="0;1;0"
                            repeatCount="indefinite"
                            begin="0.3"/>
                        </circle>
                    </svg>
                </div>
            `);

            $("#showDetailModal").modal('show');
        },
        success: function (data) {
            setTimeout(() => {
                $("#showDetailModalBody").html(data);
            }, 1000)
        }
    });
});

function confirmationWithAjaxReload({
    selector,
    refreshTable,
    method = 'delete',
    confirmMsg = 'Are you sure to delete this.?',
    timeout = 500
}) {

    $(document).on('click', selector, function (e) {
        e.preventDefault();

        if (confirm(confirmMsg)) {
            if (! ['function', 'undefined'].includes(typeof refreshTable)) {
                console.error(`second argument must be type of a function.`)
            } else {
                $.post(this.action, { '_method': method }, function(data) {
                    if (data.status === true) {
                        $(".frmMsg").html("<div class='alert alert-success' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success!</strong> "+ data.message +"</div>");

                        setTimeout(() => {
                            refreshTable()
                        }, timeout)

                    } else if (data.status === false) {
                        $(".frmMsg").html("<div class='alert alert-danger' alert-dismissible><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error!</strong> "+ data.message +"</div>");
                    }
                })
            }
        }
    })
}
