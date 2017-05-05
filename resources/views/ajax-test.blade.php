<form name="ajaxform" id="ajaxform" action="http://blitzer.loc/api/msg/send/2016-01-03_13:25:00?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQsImlzcyI6Imh0dHA6XC9cL2JsaXR6ZXIubG9jXC9hcGlcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNDkzODQyNDgzLCJleHAiOjIxNDc0ODM2NDcsIm5iZiI6MTQ5Mzg0MjQ4MywianRpIjoiUU9DN245UHVmN0E5RDIwZCJ9.NWyubrQjdKPM7BGfdXnUQ_-lpC2NRWZ8qVn6z3ggyJo" method="POST">
    <input type="submit" id="submit">
    <input type="hidden" name="user_id" value="5">
    <input type="hidden" name="reply" value="test">
    <input type="hidden" name="pin_id" value="24">
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$(function () {
    $("#ajaxform").on("submit", function(ev)
    {

        var postData = {user_id:5, reply:"test", pin_id:24};
        var formURL = $(this).attr("action");
        $.ajax(
                {
                    url : formURL,
                    type: "POST",
                    data : postData,
                     beforeSend: function(request) {
                        request.setRequestHeader("Authorization", "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQsImlzcyI6Imh0dHA6XC9cL2JsaXR6ZXIubG9jXC9hcGlcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNDkzODQyNDgzLCJleHAiOjIxNDc0ODM2NDcsIm5iZiI6MTQ5Mzg0MjQ4MywianRpIjoiUU9DN245UHVmN0E5RDIwZCJ9.NWyubrQjdKPM7BGfdXnUQ_-lpC2NRWZ8qVn6z3ggyJo");
                      },
                    success:function(data, textStatus, jqXHR)
                    {
                        console.log(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        console.log(errorThrown);
                        //if fails
                    }
                });
            return false;
    });
});
</script>
