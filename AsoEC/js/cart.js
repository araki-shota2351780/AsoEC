document.addEventListener("DOMContentLoaded", () => {
    const cartItemsContainer = document.getElementById("cart-items");
    const totalPriceElement = document.getElementById("total-price");

    // 初期データ取得
    fetchCartItems();

    // カート内容を取得
    function fetchCartItems() {
        fetch("/AsoEC/php/get_cart.php")
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    renderCartItems(data.items, data.total_price);
                } else {
                    cartItemsContainer.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch((error) => {
                cartItemsContainer.innerHTML = `<p>エラーが発生しました: ${error.message}</p>`;
            });
    }

    // カート内容を表示
    function renderCartItems(items, totalPrice) {
        cartItemsContainer.innerHTML = ""; // 初期化

        items.forEach((item) => {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");

            cartItem.innerHTML = `
                <img src="${item.image_url}" alt="${item.name}">
                <div class="cart-item-details">
                    <h3>${item.name}</h3>
                    <p>価格: ¥${item.price.toLocaleString()}</p>
                    <p>数量: ${item.quantity}</p>
                </div>
                <div class="cart-item-actions">
                    <button class="quantity-btn" data-id="${item.merch_id}" data-change="-1">-</button>
                    <input type="number" min="1" value="${item.quantity}" data-id="${item.merch_id}">
                    <button class="quantity-btn" data-id="${item.merch_id}" data-change="1">+</button>
                    <button class="delete-btn" data-id="${item.merch_id}">削除</button>
                </div>
            `;

            const quantityInput = cartItem.querySelector("input");
            const minusBtn = cartItem.querySelector('button[data-change="-1"]');
            const plusBtn = cartItem.querySelector('button[data-change="1"]');
            const deleteBtn = cartItem.querySelector(".delete-btn");

            // 数量変更イベント
            quantityInput.addEventListener("change", (e) => {
                const newQuantity = parseInt(e.target.value, 10);
                if (newQuantity > 0) {
                    updateCartItem(item.merch_id, newQuantity);
                } else {
                    alert("購入個数は1以上にしてください");
                    e.target.value = item.quantity; // 元に戻す
                }
            });

            minusBtn.addEventListener("click", () => {
                const newQuantity = parseInt(quantityInput.value, 10) - 1;
                if (newQuantity > 0) {
                    quantityInput.value = newQuantity;
                    updateCartItem(item.merch_id, newQuantity);
                }
            });

            plusBtn.addEventListener("click", () => {
                const newQuantity = parseInt(quantityInput.value, 10) + 1;
                quantityInput.value = newQuantity;
                updateCartItem(item.merch_id, newQuantity);
            });

            // 削除ボタンイベント
            deleteBtn.addEventListener("click", () => {
                if (confirm("本当にこの商品を削除しますか？")) {
                    removeCartItem(item.merch_id);
                }
            });

            cartItemsContainer.appendChild(cartItem);
        });

        totalPriceElement.textContent = `¥${totalPrice.toLocaleString()}`;
    }

    // カートアイテムを更新
    function updateCartItem(merchId, quantity) {
        fetch("/AsoEC/php/update_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ merch_id: merchId, quantity }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    alert(`エラー: ${data.message}`);
                } else {
                    fetchCartItems(); // 更新後に再取得
                }
            })
            .catch((error) => {
                alert(`通信エラー: ${error.message}`);
            });
    }

    // カートアイテムを削除
    function removeCartItem(merchId) {
        fetch("/AsoEC/php/remove_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ merch_id: merchId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    fetchCartItems(); // 更新後に再取得
                } else {
                    alert(`エラー: ${data.message}`);
                }
            })
            .catch((error) => {
                alert(`通信エラー: ${error.message}`);
            });
    }
});
