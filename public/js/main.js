document.addEventListener("DOMContentLoaded", function () {

    const success = document.getElementById("successAlert");
    const error = document.getElementById("errorAlert");

    function autoHide(element) {
        if (!element) return;

        setTimeout(() => {
            element.classList.add("opacity-0");

            setTimeout(() => {
                element.remove();
            }, 500);
        }, 3000); // 3 detik
    }

    autoHide(success);
    autoHide(error);
});