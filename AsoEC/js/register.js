document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("/AsoEC/php/register.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.text())
        .then(data => {
            document.getElementById("responseMessage").textContent = data;
            if (data.includes("成功")) {
                document.getElementById("registerForm").reset();
            }
        })
        .catch(error => {
            document.getElementById("responseMessage").textContent = "エラーが発生しました。";
            console.error("Error:", error);
        });
});
