window.addEventListener("beforeunload", function () {
    navigator.sendBeacon("sessionDestroy.php");
});
