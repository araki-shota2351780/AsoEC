document.addEventListener("DOMContentLoaded", () => {
    const checkoutForm = document.getElementById("checkout-form");

    checkoutForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(checkoutForm);
        const data = Object.fromEntries(formData.entries());

        // サーバーにデータを送信してセッションに保存
        fetch("/AsoEC/php/save_checkout_data.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((result) => {
                if (result.success) {
                    // 成功した場合、次のページに遷移
                    window.location.href = "/AsoEC/card_register.html";
                } else {
                    alert(`エラー: ${result.message}`);
                }
            })
            .catch((error) => {
                console.error("通信エラー:", error);
                alert("通信エラーが発生しました。");
            });
    });
});
