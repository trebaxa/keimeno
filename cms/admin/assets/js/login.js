function set_login_secs(seconds) {
        seconds = seconds - 1;
        if (seconds <= 0) {
                $('#js-seconds').html(0);
                location.href = "/admin/login.html";
        } else {
                $('#js-seconds').html(seconds);
                setTimeout("set_login_secs("+seconds+")", 1000);
        }
}
