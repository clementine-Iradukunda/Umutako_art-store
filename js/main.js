// ================================================
//  main.js — Umutako Art Store
//  Handles: cart, products, categories, checkout
// ================================================

// Emoji icons matched to category names
const CAT_ICONS = {
    'Baskets & Weaving':    '🧺',
    'Pottery & Ceramics':   '🏺',
    'Wood Carvings':        '🪵',
    'Jewelry & Accessories':'📿',
    'Textiles & Fabrics':   '🎨'
};

// ------------------------------------------------
// CART — persisted in localStorage
// ------------------------------------------------
let cart = [];

try {
    cart = JSON.parse(localStorage.getItem('umutako_cart')) || [];
} catch(e) {
    cart = [];
}

function saveCart() {
    localStorage.setItem('umutako_cart', JSON.stringify(cart));
    updateCartBadge();
}

function updateCartBadge() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'inline-flex' : 'none';
    });
}

// ------------------------------------------------
// ADD / REMOVE / UPDATE QUANTITY
// ------------------------------------------------
function addToCart(product) {
    const found = cart.find(i => i.id === product.id);
    if (found) {
        found.quantity += 1;
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    saveCart();
    renderCartItems();
    showToast(`✅  "${product.name}" added to cart!`);
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    saveCart();
    renderCartItems();
}

function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        removeFromCart(id);
    } else {
        saveCart();
        renderCartItems();
    }
}

// ------------------------------------------------
// RENDER CART SIDEBAR
// ------------------------------------------------
function renderCartItems() {
    const list    = document.getElementById('cart-items-list');
    const totalEl = document.getElementById('cart-total-amount');
    if (!list) return;

    if (cart.length === 0) {
        list.innerHTML = `
            <div class="cart-empty">
                <div class="empty-icon">🛒</div>
                <p>Your cart is empty</p>
                <small>Discover our handcrafted pieces and add them here!</small>
            </div>`;
        if (totalEl) totalEl.textContent = 'RWF 0';
        return;
    }

    let html  = '';
    let total = 0;

    cart.forEach(item => {
        const icon    = CAT_ICONS[item.category_name] || '🎁';
        const subtotal = Number(item.price) * item.quantity;
        total += subtotal;
        const thumbHtml = item.image
            ? `<img src="${item.image}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-sm)"/>`
            : icon;

        html += `
        <div class="cart-item">
            <div class="ci-icon">${thumbHtml}</div>
            <div class="ci-info">
                <div class="ci-name">${item.name}</div>
                <div class="ci-price">RWF ${Number(item.price).toLocaleString()} each</div>
                <div class="ci-controls">
                    <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                    <span class="qty-num">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${item.id},  1)">+</button>
                    <button class="remove-btn" onclick="removeFromCart(${item.id})" title="Remove">🗑️</button>
                </div>
            </div>
        </div>`;
    });

    list.innerHTML = html;
    if (totalEl) totalEl.textContent = `RWF ${total.toLocaleString()}`;
}

// ------------------------------------------------
// OPEN / CLOSE CART
// ------------------------------------------------
function openCart() {
    renderCartItems();
    document.getElementById('cart-sidebar').classList.add('open');
    document.getElementById('cart-overlay').classList.add('open');
}

function closeCart() {
    document.getElementById('cart-sidebar').classList.remove('open');
    document.getElementById('cart-overlay').classList.remove('open');
}

// ------------------------------------------------
// CHECKOUT MODAL
// ------------------------------------------------
function openCheckout() {
    if (cart.length === 0) {
        showToast('⚠️  Your cart is empty. Add some products first!');
        return;
    }
    closeCart();
    fillOrderSummary();
    resetCheckoutForm();
    document.getElementById('checkout-modal').classList.add('open');
}

function closeCheckout() {
    document.getElementById('checkout-modal').classList.remove('open');
}

function fillOrderSummary() {
    const itemsEl = document.getElementById('order-summary-items');
    const totalEl = document.getElementById('order-summary-total');
    if (!itemsEl) return;

    let html  = '';
    let total = 0;

    cart.forEach(item => {
        const sub = Number(item.price) * item.quantity;
        total += sub;
        html += `<div class="summary-item">
                    <span>${item.name} × ${item.quantity}</span>
                    <span>RWF ${sub.toLocaleString()}</span>
                 </div>`;
    });

    itemsEl.innerHTML = html;
    if (totalEl) totalEl.textContent = `RWF ${total.toLocaleString()}`;
}

function resetCheckoutForm() {
    ['cust-name','cust-email','cust-phone','cust-address','cust-notes'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const btn = document.getElementById('place-order-btn');
    if (btn) { btn.textContent = '✅ Place Order'; btn.disabled = false; }
}

// ------------------------------------------------
// PLACE ORDER
// ------------------------------------------------
function placeOrder() {
    const name    = (document.getElementById('cust-name')?.value    || '').trim();
    const email   = (document.getElementById('cust-email')?.value   || '').trim();
    const phone   = (document.getElementById('cust-phone')?.value   || '').trim();
    const address = (document.getElementById('cust-address')?.value || '').trim();
    const notes   = (document.getElementById('cust-notes')?.value   || '').trim();

    // Validate required fields
    if (!name) { showToast('⚠️  Please enter your full name.'); return; }
    if (!email || !email.includes('@')) { showToast('⚠️  Please enter a valid email address.'); return; }
    if (!address) { showToast('⚠️  Please enter your delivery address.'); return; }

    const total = cart.reduce((sum, i) => sum + Number(i.price) * i.quantity, 0);

    const orderData = {
        customer_name:    name,
        customer_email:   email,
        customer_phone:   phone,
        customer_address: address,
        notes,
        total,
        cart
    };

    // Disable button while sending
    const btn = document.getElementById('place-order-btn');
    btn.textContent = '⏳ Placing order…';
    btn.disabled    = true;

    fetch('php/place_order.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(orderData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showOrderSuccess(data.order_id);
            cart = [];
            saveCart();
            renderCartItems();
        } else {
            showToast('❌  ' + (data.message || 'Something went wrong.'));
            btn.textContent = '✅ Place Order';
            btn.disabled    = false;
        }
    })
    .catch(() => {
        // DEMO MODE – PHP not running (e.g. opened as a static file)
        const fakeId = Math.floor(Math.random() * 90000) + 10000;
        showOrderSuccess(fakeId);
        cart = [];
        saveCart();
        renderCartItems();
    });
}

function showOrderSuccess(orderId) {
    const body = document.getElementById('checkout-body');
    body.innerHTML = `
        <div class="success-box">
            <div class="success-icon">🎉</div>
            <h3>Order Confirmed!</h3>
            <p>Thank you for supporting Rwandan artisans.<br/>We will contact you shortly to arrange delivery.</p>
            <div class="order-num-badge">Order #${orderId}</div>
            <button class="place-order-btn" onclick="closeCheckout()">Continue Shopping</button>
        </div>`;
}

// ------------------------------------------------
// LOAD PRODUCTS FROM PHP (or demo fallback)
// ------------------------------------------------
function loadProducts(categoryId = 0, search = '') {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    grid.innerHTML = `<div class="loading"><div class="spinner"></div>Loading products…</div>`;

    let url = 'php/get_products.php?';
    if (categoryId > 0) url += `category=${categoryId}&`;
    if (search)         url += `search=${encodeURIComponent(search)}`;

    fetch(url)
        .then(res => res.json())
        .then(products => {
            if (!Array.isArray(products) || products.length === 0) {
                grid.innerHTML = `<div class="loading">😕  No products found. Try a different search.</div>`;
                return;
            }
            renderProducts(products);
        })
        .catch(() => {
            // PHP not running – show demo products
            let demo = getDemoProducts();
            if (categoryId > 0) demo = demo.filter(p => p.category_id === categoryId);
            if (search)         demo = demo.filter(p =>
                p.name.toLowerCase().includes(search.toLowerCase()) ||
                p.description.toLowerCase().includes(search.toLowerCase())
            );
            if (demo.length === 0) {
                grid.innerHTML = `<div class="loading">😕  No products found.</div>`;
                return;
            }
            renderProducts(demo);
        });
}

function renderProducts(products) {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    let html = '';
    products.forEach(p => {
        const icon     = CAT_ICONS[p.category_name] || '🎁';
        const lowStock = Number(p.stock) > 0 && Number(p.stock) <= 5
                         ? `<div class="stock-warning">⚠️  Only ${p.stock} left!</div>` : '';
        const soldOut  = Number(p.stock) === 0;
        const pJson    = encodeProductForAttr(p);
        const thumbContent = p.image
            ? `<img src="${p.image}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0"/>`
            : icon;

        html += `
        <div class="product-card">
            <div class="product-thumb" onclick="window.location='product.html?id=${p.id}'" title="View details" style="cursor:pointer">
                ${thumbContent}
            </div>
            <div class="product-info">
                <div class="product-cat">${p.category_name || ''}</div>
                <div class="product-name">${p.name}</div>
                <div class="product-desc">${p.description}</div>
                ${lowStock}
                <div class="product-footer">
                    <div class="product-price">
                        RWF ${Number(p.price).toLocaleString()}
                        <small>/item</small>
                    </div>
                    ${soldOut
                        ? `<button class="add-btn" disabled style="opacity:.45">Sold Out</button>`
                        : `<button class="add-btn" onclick="addToCart(${pJson})">+ Cart</button>`
                    }
                </div>
                <a class="view-link" href="product.html?id=${p.id}">View details →</a>
            </div>
        </div>`;
    });

    grid.innerHTML = html;
}

// Encode a product object safely for use inside HTML onclick attributes
function encodeProductForAttr(p) {
    return `JSON.parse(decodeURIComponent('${encodeURIComponent(JSON.stringify(p))}'))`;
}

// ------------------------------------------------
// PRODUCT DETAIL MODAL
// ------------------------------------------------
function openProductModal(product) {
    const icon = CAT_ICONS[product.category_name] || '🎁';

    document.getElementById('pm-title').textContent       = product.name;
    document.getElementById('pm-icon').textContent        = icon;
    document.getElementById('pm-category').textContent    = product.category_name || '';
    document.getElementById('pm-description').textContent = product.description;
    document.getElementById('pm-price').textContent       = `RWF ${Number(product.price).toLocaleString()}`;

    const stock = Number(product.stock);
    document.getElementById('pm-stock').textContent =
        stock === 0 ? '❌ Out of stock'
        : stock <= 5 ? `⚠️  Only ${stock} in stock`
        : `✅  In stock (${stock} available)`;

    const addBtn = document.getElementById('pm-add-btn');
    addBtn.textContent = stock === 0 ? 'Out of Stock' : '🛒  Add to Cart';
    addBtn.disabled    = stock === 0;
    addBtn.onclick     = stock === 0 ? null : () => {
        addToCart(product);
        closeProductModal();
    };

    document.getElementById('product-modal').classList.add('open');
}

function closeProductModal() {
    document.getElementById('product-modal').classList.remove('open');
}

// ------------------------------------------------
// LOAD CATEGORIES FROM PHP
// ------------------------------------------------
function loadCategories() {
    fetch('php/get_categories.php')
        .then(res => res.json())
        .then(cats => renderCategoryButtons(cats))
        .catch(() => {
            // Demo categories
            renderCategoryButtons([
                { id:1, name:'Baskets & Weaving',     icon:'🧺' },
                { id:2, name:'Pottery & Ceramics',    icon:'🏺' },
                { id:3, name:'Wood Carvings',          icon:'🪵' },
                { id:4, name:'Jewelry & Accessories', icon:'📿' },
                { id:5, name:'Textiles & Fabrics',    icon:'🎨' },
            ]);
        });
}

function renderCategoryButtons(categories) {
    const bar = document.getElementById('categories-bar');
    if (!bar) return;

    let html = `<button class="cat-btn active" onclick="filterByCategory(0, this)">All Products</button>`;
    categories.forEach(c => {
        const icon = c.icon || CAT_ICONS[c.name] || '🎁';
        html += `<button class="cat-btn" onclick="filterByCategory(${c.id}, this)">${icon} ${c.name}</button>`;
    });

    bar.innerHTML = html;
}

function filterByCategory(id, btn) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    // Clear search input
    const s = document.getElementById('search-input');
    if (s) s.value = '';
    loadProducts(id);
}

// ------------------------------------------------
// SEARCH
// ------------------------------------------------
function searchProducts() {
    const query = document.getElementById('search-input')?.value.trim() || '';
    // Reset category active state
    document.querySelectorAll('.cat-btn').forEach((b, i) => {
        b.classList.toggle('active', i === 0);
    });
    loadProducts(0, query);
}

function clearSearch() {
    const s = document.getElementById('search-input');
    if (s) s.value = '';
    document.querySelectorAll('.cat-btn').forEach((b, i) => b.classList.toggle('active', i === 0));
    loadProducts();
}

// ------------------------------------------------
// MOBILE NAV TOGGLE
// ------------------------------------------------
function toggleNav() {
    document.getElementById('nav-links')?.classList.toggle('open');
}

// ------------------------------------------------
// TOAST
// ------------------------------------------------
let toastTimer;
function showToast(msg) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    clearTimeout(toastTimer);
    toast.textContent = msg;
    toast.classList.add('show');
    toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
}

// ------------------------------------------------
// DEMO PRODUCTS (shown when PHP is not running)
// ------------------------------------------------
function getDemoProducts() {
    return [
        { id:1,  name:'Agaseke Gift Basket',     description:'Beautiful traditional Rwandan agaseke basket, handwoven with fine sisal and raffia. Perfect as a gift or home decoration.',  price:15000, stock:20, category_id:1, category_name:'Baskets & Weaving'     },
        { id:2,  name:'Large Market Basket',      description:'Spacious handwoven basket ideal for carrying goods at the market. Durable with vibrant geometric patterns.',                  price:22000, stock:15, category_id:1, category_name:'Baskets & Weaving'     },
        { id:3,  name:'Woven Place Mat Set',      description:'Set of 4 handwoven place mats featuring classic Rwandan patterns. Adds warmth to any dining table.',                        price:12000, stock:30, category_id:1, category_name:'Baskets & Weaving'     },
        { id:4,  name:'Clay Water Pot (Inkono)',  description:'Traditional Rwandan clay pot for water storage. Keeps water naturally cool without electricity.',                             price:18000, stock:10, category_id:2, category_name:'Pottery & Ceramics'    },
        { id:5,  name:'Decorative Ceramic Bowl',  description:'Beautiful painted ceramic bowl, perfect for fruit display or as a centerpiece decoration.',                                   price:9500,  stock:25, category_id:2, category_name:'Pottery & Ceramics'    },
        { id:6,  name:'Clay Candle Holder',       description:'Handmade clay candle holder with traditional engravings. Creates a warm ambiance in any room.',                              price:5500,  stock:40, category_id:2, category_name:'Pottery & Ceramics'    },
        { id:7,  name:'Carved Wooden Giraffe',    description:'Hand-carved wooden giraffe figurine made from local hardwood. A stunning African art piece for any shelf.',                 price:25000, stock:8,  category_id:3, category_name:'Wood Carvings'          },
        { id:8,  name:'Wooden Serving Tray',      description:'Handcrafted wooden tray with beautifully carved border patterns. Functional and decorative.',                                price:16000, stock:12, category_id:3, category_name:'Wood Carvings'          },
        { id:9,  name:'Carved Wall Mask',         description:'Traditional African wall mask, hand-carved from aged hardwood. A unique statement piece for walls.',                         price:35000, stock:5,  category_id:3, category_name:'Wood Carvings'          },
        { id:10, name:'Beaded Necklace',          description:'Colorful handmade beaded necklace with traditional Rwandan patterns. Lightweight and elegant for any occasion.',             price:8000,  stock:50, category_id:4, category_name:'Jewelry & Accessories'  },
        { id:11, name:'Beaded Bracelet Set',      description:'Set of 3 matching handmade beaded bracelets in complementary colors. A great gift idea.',                                    price:5000,  stock:60, category_id:4, category_name:'Jewelry & Accessories'  },
        { id:12, name:'Beaded Earrings',          description:'Beautiful handmade beaded earrings, lightweight and perfect for everyday wear or special events.',                           price:4000,  stock:45, category_id:4, category_name:'Jewelry & Accessories'  },
        { id:13, name:'Imigongo Wall Art',        description:'Traditional Rwandan imigongo geometric art on canvas. Striking black and white cow-dung patterns — a cultural treasure.',   price:45000, stock:7,  category_id:5, category_name:'Textiles & Fabrics'     },
        { id:14, name:'Kitenge Fabric (2m)',      description:'2 metres of colorful African kitenge fabric. Great for clothing, bags, or home decoration projects.',                        price:12000, stock:35, category_id:5, category_name:'Textiles & Fabrics'     },
        { id:15, name:'Hand-Painted Scarf',       description:'Silk scarf hand-painted with Rwandan landscape and bird motifs. A wearable piece of art.',                                   price:20000, stock:18, category_id:5, category_name:'Textiles & Fabrics'     },
    ];
}

// ------------------------------------------------
// INIT on page load
// ------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadProducts();
    updateCartBadge();

    // Search on Enter key
    document.getElementById('search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') searchProducts();
    });

    // Close modals when clicking background overlay
    document.getElementById('cart-overlay')?.addEventListener('click', closeCart);

    document.getElementById('checkout-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCheckout();
    });

    document.getElementById('product-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeProductModal();
    });

    // Smooth scroll for nav links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                // close mobile nav if open
                document.getElementById('nav-links')?.classList.remove('open');
            }
        });
    });
});

// ------------------------------------------------
// SESSION — update nav with login/account links
// ------------------------------------------------
function updateNavSession() {
    const slot = document.getElementById('nav-account-item');
    if (!slot) return;
    fetch('php/session_info.php')
        .then(r => r.json())
        .then(d => {
            if (d.loggedIn) {
                slot.innerHTML = `
                    <a href="${d.role === 'admin' ? 'admin/dashboard.php' : 'my-orders.php'}"
                       style="color:var(--gold-300)">
                       👤 ${d.name}
                    </a>`;
            } else {
                slot.innerHTML = `<a href="login.html">🔐 Login</a>`;
            }
        })
        .catch(() => {
            slot.innerHTML = `<a href="login.html">🔐 Login</a>`;
        });
}

document.addEventListener('DOMContentLoaded', updateNavSession);
