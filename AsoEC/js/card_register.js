document.addEventListener("DOMContentLoaded", () => {
    const cardRegisterForm = document.getElementById("card-register-form");

    cardRegisterForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(cardRegisterForm);
        const data = Object.fromEntries(formData.entries());

        fetch("/AsoEC/php/card_register.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((result) => {
                if (result.success) {
                    alert("カード情報が登録されました！");
                    // 注文確認画面など次のステップへ移動
                    window.location.href = "/AsoEC/checkout_summary.html";
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
