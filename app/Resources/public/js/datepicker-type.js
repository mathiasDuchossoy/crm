/**
 * Created by Stephane on 28/07/2016.
 */
$('[data-provide="datepicker"]').datepicker({
    todayBtn: "linked",
    format: "dd/mm/yyyy",
    autoclose: "true",
    pickerPosition: "bottom-left",
    startView: "day",
    minView: "month",
    daysOfWeekHighlighted: [0, 6],
    language: "fr",
    todayHighlight: true
});
$('[data-provide="datepicker-futur"]').datepicker({
    todayBtn: "linked",
    format: "dd/mm/yyyy",
    startDate: '+0d',
    autoclose: "true",
    pickerPosition: "bottom-left",
    startView: "day",
    minView: "month",
    daysOfWeekHighlighted: [0, 6],
    language: "fr",
    todayHighlight: true
});
$('[data-provide="datepicker-passe"]').datepicker({
    todayBtn: "linked",
    format: "dd/mm/yyyy",
    endDate: '+0d',
    autoclose: "true",
    pickerPosition: "bottom-left",
    startView: "day",
    minView: "month",
    daysOfWeekHighlighted: [0, 6],
    language: "fr",
    todayHighlight: true
});
$('[data-provide="datepicker-futur-tranche-cinq-ans"]').datepicker({
    todayBtn: "linked",
    format: "dd/mm/yyyy",
    startDate: '+0d',
    endDate: '+5y',
    autoclose: "true",
    pickerPosition: "bottom-left",
    startView: "day",
    minView: "month",
    daysOfWeekHighlighted: [0, 6],
    language: "fr",
    todayHighlight: true
});
// $('[data-provide="datepicker-futur-tranche-cinq-ans"]').datepicker({
//     todayBtn: "linked",
//     format: "dd/mm/yyyy",
//     startDate: '+0d',
//     endDate: '+5y',
//     autoclose: "true",
//     pickerPosition: "bottom-left",
//     startView: "day",
//     minView: "month",
//     daysOfWeekHighlighted: [0, 6],
//     language: "{{ app.request.locale | slice(0,2) }}",
//     todayHighlight: true
// });