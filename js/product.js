// product.js — Product Detail Page logic

(function () {
    const CAT_ICONS_LOCAL = {
        'Baskets & Weaving':     '🧺',
        'Pottery & Ceramics':    '🏺',
        'Wood Carvings':         '🪵',
        'Jewelry & Accessories': '📿',
        'Textiles & Fabrics':    '🎨'
    };

    function getIcon(categoryName) {
        return CAT_ICONS_LOCAL[categoryName] || '🎁';
    }

    function getIdFromUrl() {
        return new URLSearchParams(window.location.search).get('id');
    }

    function renderProduct(p) {
        const icon  = getIcon(p.category_name);
        const stock = Number(p.stock);
        const stockLabel =
            stock === 0   ? '<span class="pdp-out">❌ Out of stock</span>'
            : stock <= 5  ? `<span class="pdp-low">⚠️ Only ${stock} left in stock</span>`
            : `<span class="pdp-in">✅ In stock (${stock} available)</span>`;

        document.title = `${p.name} — Umutako Art Store`;
        document.getElementById('bc-name').textContent = p.name;

        document.getElementById('pdp-inner').innerHTML = `
            <div class="pdp-media">
                <div class="pdp-thumb">${icon}</div>
            </div>
            <div class="pdp-info">
                <div class="product-cat">${p.category_name || ''}</div>
                <h1 class="pdp-title">${p.name}</h1>
                <div class="pdp-price">RWF ${Number(p.price).toLocaleString()} <small>/item</small></div>
                ${stockLabel}
                <p class="pdp-desc">${p.description}</p>
                <div class="pdp-actions">
                    ${stock === 0
                        ? `<button class="add-btn pdp-add" disabled style="opacity:.45;cursor:not-allowed">Sold Out</button>`
                        : `<button class="add-btn pdp-add" onclick='addToCart(${JSON.stringify(p)})'>🛒 Add to Cart</button>`
                    }
                    <a href="index.html#products" class="btn-secondary pdp-back">← Back to Shop</a>
                </div>
            </div>`;
    }

    function renderRelated(products, currentId) {
        const related = products.filter(p => Number(p.id) !== Number(currentId)).slice(0, 4);
        if (related.length === 0) return;

        document.getElementById('related-section').style.display = '';
        let html = '';
        related.forEach(p => {
            const icon    = getIcon(p.category_name);
            const pJson   = encodeProductForAttr(p);
            const soldOut = Number(p.stock) === 0;
            html += `
            <div class="product-card">
                <div class="product-thumb" onclick="window.location='product.html?id=${p.id}'" style="cursor:pointer">${icon}</div>
                <div class="product-info">
                    <div class="product-cat">${p.category_name || ''}</div>
                    <div class="product-name">${p.name}</div>
                    <div class="product-desc">${p.description}</div>
                    <div class="product-footer">
                        <div class="product-price">RWF ${Number(p.price).toLocaleString()} <small>/item</small></div>
                        ${soldOut
                            ? `<button class="add-btn" disabled style="opacity:.45">Sold Out</button>`
                            : `<button class="add-btn" onclick="addToCart(${pJson})">+ Cart</button>`}
                    </div>
                    <a class="view-link" href="product.html?id=${p.id}">View details →</a>
                </div>
            </div>`;
        });
        document.getElementById('related-grid').innerHTML = html;
    }

    function loadDemo(id) {
        const all  = getDemoProducts();
        const prod = all.find(p => p.id === Number(id));
        if (!prod) {
            document.getElementById('pdp-inner').innerHTML =
                `<div class="loading">😕 Product not found. <a href="index.html">Go back</a></div>`;
            return;
        }
        renderProduct(prod);
        const same = all.filter(p => p.category_id === prod.category_id);
        renderRelated(same, id);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const id = getIdFromUrl();
        if (!id) {
            document.getElementById('pdp-inner').innerHTML =
                `<div class="loading">😕 No product selected. <a href="index.html">Go back to shop</a></div>`;
            return;
        }

        fetch(`php/get_product.php?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(p => {
                if (p.error) throw new Error(p.error);
                renderProduct(p);
                // Load related from same category
                return fetch(`php/get_products.php?category=${p.category_id}`);
            })
            .then(r => r.json())
            .then(related => renderRelated(related, id))
            .catch(() => loadDemo(id));
    });
})();
