document.addEventListener("DOMContentLoaded", () => {
    const checkoutSummaryElement = document.getElementById("checkout-summary");
    const completePurchaseButton = document.getElementById("complete-purchase-btn");

    // 購入情報を取得
    fetch("/AsoEC/php/checkout_summary.php")
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const { checkout_data, cart_info, total_amount } = data;

                checkoutSummaryElement.innerHTML = `
                    <h2>配送情報</h2>
                    <p><strong>名前:</strong> ${checkout_data.first_name} ${checkout_data.last_name}</p>
                    <p><strong>配達日:</strong> ${checkout_data.delivery_date}</p>
                    <p><strong>時間帯:</strong> ${checkout_data.delivery_time}</p>
                    <p><strong>住所:</strong> ${checkout_data.country}, ${checkout_data.prefecture}, ${checkout_data.city}, ${checkout_data.address} ${checkout_data.building}</p>
                    <p><strong>支払い方法:</strong> ${checkout_data.payment_method === "credit_card" ? "クレジットカード" : "コンビニ決済"}</p>

                    <h2>カート情報</h2>
                    ${cart_info.map(item => `
                        <p>${item.name} × ${item.quantity} = ¥${(item.price * item.quantity).toLocaleString()}</p>
                    `).join('')}

                    <h2>合計金額</h2>
                    <p><strong>¥${total_amount.toLocaleString()}</strong></p>
                `;
            } else {
                alert(`エラー: ${data.message}`);
            }
        })
        .catch((error) => {
            console.error("通信エラー:", error);
            alert("データの取得に失敗しました。");
        });

    // 購入完了ボタンの処理
    completePurchaseButton.addEventListener("click", () => {
        fetch("/AsoEC/php/complate_purchase.php", { method: "POST" })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("購入が完了しました！");
                    window.location.href = "/AsoEC/home.html";
                } else {
                    alert(`エラー: ${data.message}`);
                }
            })
            .catch((error) => {
                console.error("通信エラー:", error);
                alert("購入処理に失敗しました。");
            });
    });
});
