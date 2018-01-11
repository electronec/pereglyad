var auto_refresh = setInterval(
function ()
{
    $.ajax({
      url: "activ.php",
      type: "POST",
      data: "answer=1",
        headers: {
            'Cookie': document.cookie},
      timeout: 15000
    });
}, 15000); // refresh every 30 seconds

