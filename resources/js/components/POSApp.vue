<template>
  <div class="app-wrapper">
    <main class="main">
      <div class="container">
        <section class="product-list">
          <div
            v-for="product in products"
            :key="product.id"
            class="product-card"
            @click="addToCart(product)"
          >
            <img :src="product.image" alt="" class="product-img" />
            <h3>{{ product.name }}</h3>
            <p class="price">Rp. {{ product.price.toLocaleString() }}</p>
          </div>
        </section>

        <aside class="cart">
          <h2 class="cart-title">
            <span class="icon">🧾</span> Pesanan
          </h2>
          <div v-if="cart.length === 0" class="empty-cart">Belum ada pesanan.</div>
          <div v-for="item in cart" :key="item.id" class="cart-item">
            <img :src="item.image" class="cart-img" />
            <div class="cart-info">
              <strong>{{ item.name }}</strong>
              <span>x {{ item.qty }} - Rp. {{ (item.qty * item.price).toLocaleString() }}</span>
            </div>
          </div>

          <div class="cart-buttons">
            <button class="btn danger" @click="clearCart">Clear Cart</button>
            <div class="btn-row">
              <button class="btn" @click="saveBill">Save Bill</button>
              <button class="btn" @click="printBill">Print Bill</button>
            </div>
            <button class="btn primary" @click="openChargeModal">
              Charge Rp. {{ total.toLocaleString() }}
            </button>
          </div>
        </aside>
      </div>
    </main>

    <footer class="footer">
      Faruqi Resto © 2020 | Developed by Faruqi
    </footer>

    <div id="print-section" v-show="false" ref="printSection">
        <h2 style="text-align: center;">🍽️ Faruqi Resto</h2>
        <p style="text-align: center;">Invoice</p>
        <p><strong>Tanggal:</strong> {{ formattedDate }}</p>
        <hr />
        <table style="width:100%; border-collapse: collapse;" border="1" cellpadding="6">
            <thead>
            <tr><th>#</th><th>Nama</th><th>Qty</th><th>Harga</th></tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in cart" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.qty }}</td>
                <td>Rp {{ (item.qty * item.price).toLocaleString() }}</td>
            </tr>
            </tbody>
        </table>
        <p style="margin-top:10px"><strong>Total:</strong> Rp {{ total.toLocaleString() }}</p>
    </div>

    <div v-if="showSavePopup" class="popup-center">
        <div class="popup-content">
            <div class="checkmark-animation">
            <svg class="checkmark" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14 27l7 7 16-16"/>
            </svg>
            </div>
            <p class="popup-text">Bill berhasil disimpan!</p>
        </div>
    </div>

    <div v-if="showChargeModal" class="modal-overlay">
        <div class="modal-box charge-layout">
            <div class="charge-left">
            <h3>Detail Pesanan</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Foto</th>
                    <th>Harga</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in cart" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.name }} x {{ item.qty }}</td>
                    <td><img :src="item.image" width="40" /></td>
                    <td>Rp {{ (item.price * item.qty).toLocaleString() }}</td>
                </tr>
                </tbody>
            </table>
            </div>

            <div class="charge-right">
            <label><strong>Uang Pembeli (Rp)</strong></label>
            <input
                type="number"
                v-model.number="buyerAmount"
                @input="calculateChange"
                class="input-field"
            />

            <div class="modal-actions-vertical">
                <button class="btn close" @click="showChargeModal = false">Close</button>
                <button class="btn primary" @click="confirmPayment" :disabled="buyerAmount < total">Pay!</button>
            </div>

            <p class="text-red" v-if="buyerAmount < total">Kembalian :</p>
            <p class="text-green" v-else><strong>Kembalian :</strong> Rp {{ changeAmount.toLocaleString() }}</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import Navbar from './Navbar.vue'
export default {
  components: {Navbar},
  data() {
    return {
      products: [],
      cart: [],
      showSavePopup: false,
      showChargeModal: false,
      buyerAmount: 0,
      changeAmount: 0,
      transactionDate: new Date(),
    };
  },
  computed: {
    total() {
      return this.cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    },
    formattedDate() {
        return this.transactionDate.toLocaleString("id-ID", {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
  },
  mounted() {
    fetch("/api/products")
      .then(res => res.json())
      .then(data => (this.products = data));
  },
  methods: {
    addToCart(product) {
      const item = this.cart.find(p => p.id === product.id);
      if (item) {
        item.qty++;
      } else {
        this.cart.push({ ...product, qty: 1 });
      }
    },
    clearCart() {
      this.cart = [];
    },
    saveBill() {
      this.showSavePopup = true;
      setTimeout(() => (this.showSavePopup = false), 3000);
    },
    printBill() {
        this.$nextTick(() => {
            const content = this.$refs.printSection.innerHTML;
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(`
            <html>
                <head>
                <title>Invoice</title>
                <style>
                    body { font-family: Arial; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
                </head>
                <body>
                ${content}
                </body>
            </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        });
    },
    openChargeModal() {
      this.buyerAmount = 0;
      this.changeAmount = 0;
      this.showChargeModal = true;
    },
    calculateChange() {
      this.changeAmount = this.buyerAmount - this.total;
    },
    confirmPayment() {
      if (this.buyerAmount >= this.total) {
        alert("Transaksi berhasil!");
        this.clearCart();
        this.showChargeModal = false;
      }
    },
  },
};
</script>
