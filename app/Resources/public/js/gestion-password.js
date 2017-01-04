/**
 * Created by mathias on 30/11/2016.
 */

// display password button
$(".password").each(function (index, element) {
    displayInputPassword($(element));
});
function displayInputPassword($element) {
    var visuPassword = '<button type="button" class="btn btn-default button-password-display" aria-label="Left Align"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>';
    var $visuPassword = $(visuPassword);
    var $button = $element.parent().find('button.button-password-display')[0];
    if ($button == null) {
        $visuPassword.insertAfter($element);
    }
    else {
        $visuPassword = $($button);
    }
    $visuPassword.on('mousedown', function () {
        $element.prop('type', "text");
    });
    $visuPassword.on('mouseup', function () {
        $element.prop('type', "password");
    });
}

$(document).bind('DOMNodeInserted', function (event) {
    var $element = $(event.target);
    $element.find('input.password:password').each(function () {
        displayInputPassword($(this));
    })
});