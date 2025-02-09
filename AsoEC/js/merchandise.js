document.addEventListener("DOMContentLoaded", () => {
    const merchandiseGrid = document.getElementById("merchandise-grid");
    const categoryTabs = document.querySelectorAll(".category-tab");

    let currentCategory = null;

    // 初期データ取得
    fetchMerchandise();

    // カテゴリタブのクリックイベント
    categoryTabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            categoryTabs.forEach((t) => t.classList.remove("active"));
            tab.classList.add("active");
            currentCategory = tab.dataset.category;

            fetchMerchandise();
        });
    });

    // サーバーから商品データを取得して表示
    function fetchMerchandise() {
        const params = new URLSearchParams();
        if (currentCategory) params.append("category", currentCategory);

        fetch(`/AsoEC/php/merchandise.php?${params.toString()}`)
            .then((response) => response.json())
            .then((data) => renderMerchandise(data))
            .catch((error) => {
                merchandiseGrid.innerHTML = `<p>エラーが発生しました: ${error.message}</p>`;
            });
    }

    // 商品を表示する関数
    function renderMerchandise(items) {
        merchandiseGrid.innerHTML = ""; 
        if (items.length === 0) {
            merchandiseGrid.innerHTML = "<p>該当する商品が見つかりません。</p>";
            return;
        }

        items.forEach((item) => {
            merchandiseGrid.appendChild(createMerchandiseItem(item));
        });
    }

    // 商品アイテムを作成
    function createMerchandiseItem(item) {
        const div = document.createElement("div");
        div.classList.add("merchandise-item");

        div.innerHTML = `
            <img src="${item.image_url}" alt="${item.name}">
            <h3>${item.name}</h3>
            <p>価格: ¥${item.price.toLocaleString()}</p>
            <button class="details-btn">詳細を見る</button>
            <button class="add-to-cart-btn" data-id="${item.merch_id}">カートに追加</button>
        `;

        const addToCartBtn = div.querySelector(".add-to-cart-btn");
        addToCartBtn.addEventListener("click", () => addToCart(item.merch_id));

        return div;
    }

    // カートに追加
    function addToCart(merchId) {
        fetch("/AsoEC/php/cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ merch_id: merchId, quantity: 1 }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // カート追加成功時
                    const goToCart = confirm("カートに追加しました！カートに移動しますか？");
                    if (goToCart) {
                        window.location.href = "/AsoEC/cart.html";
                    }
                } else {
                    alert(`エラー: ${data.message}`);
                }
            })
            .catch((error) => alert(`通信エラー: ${error.message}`));
    }
});
