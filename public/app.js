'use_strict'

$('form').on('submit',function(e) {
    e.preventDefault();
    getTable();
});

function getTable() {
    var message = $("#message").val();

    if (!message){
        alert('Enter your message');
    }

    $.ajax({
        type: "POST",
        url: "/add_message",
        data: "message=" + message,
        success: function(html) {
            $("#MessageTable").html(html);
            $("#message").val('')
        }
    });
}