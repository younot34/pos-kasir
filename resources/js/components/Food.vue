<template>
  <div class="container">
    <div v-if="showSuccessPopup" class="popup-center">
        <div class="popup-content">
            <div class="checkmark-animation">
            <svg class="checkmark" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14 27l7 7 16-16"/>
            </svg>
            </div>
            <p class="popup-text">Menu berhasil disimpan!</p>
        </div>
    </div>
    <h2 class="title">Tambahkan menu makanan yang ada di resto</h2>
    <button class="btn primary" @click="showAddForm = true">+ Tambah Menu</button>

    <div v-if="showAddForm" class="modal-overlay">
      <div class="modal">
        <h3 class="modal-title">Tambahkan Menu</h3>

        <label>Nama Menu</label>
        <input type="text" v-model="newItem.name" placeholder="Nama Menu" class="input" />

        <label>Upload Gambar</label>
        <div
          class="upload-area"
          @dragover.prevent
          @drop.prevent="handleDrop"
        >
          <p v-if="!newItem.image">Drag & drop file di sini atau klik untuk pilih</p>
          <img v-if="newItem.image" :src="newItem.image" class="preview-img" />
          <input type="file" @change="handleFileChange" class="file-input" />
        </div>

        <label>Harga</label>
        <div class="price-input">
          <span class="prefix">Rp.</span>
          <input type="number" v-model.number="newItem.price" placeholder="Harga" class="input input-inline" />
        </div>

        <div class="modal-actions">
          <button class="btn primary" @click="addProduct">Simpan</button>
          <button class="btn danger" @click="showAddForm = false">Batal</button>
        </div>
      </div>
    </div>

    <table class="menu-table">
      <thead>
        <tr><th>#</th><th>Nama</th><th>Foto</th><th>Harga</th></tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in products" :key="product.id">
          <td>{{ index + 1 }}</td>
          <td>{{ product.name }}</td>
          <td><img :src="product.image" alt="" class="product-img" /></td>
          <td>Rp {{ product.price.toLocaleString() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>


<script>
import Navbar from './Navbar.vue'
export default {
  components: {Navbar},
  data() {
    return {
      showAddForm: false,
      showSuccessPopup: false,
      products: [],
      newItem: {
        name: '',
        image: '',
        price: 0,
      },
    };
  },
  mounted() {
    this.getProducts();
  },
  methods: {
    getProducts() {
      fetch('/api/products')
        .then(res => res.json())
        .then(data => (this.products = data));
    },
    addProduct() {
      fetch('/api/products', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(this.newItem),
      })
        .then(() => {
          this.getProducts();
          this.newItem = { name: '', image: '', price: 0 };
          this.showAddForm = false;

          this.showSuccessPopup = true;
          setTimeout(() => {
              this.showSuccessPopup = false;
          }, 3000);
        });
    },
    handleFileChange(e) {
      const file = e.target.files[0];
      this.uploadImage(file);
    },
    handleDrop(e) {
      const file = e.dataTransfer.files[0];
      this.uploadImage(file);
    },
    uploadImage(file) {
      const formData = new FormData();
      formData.append('image', file);

      fetch('api/upload-image', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.json())
      .then(data => {
        this.newItem.image = data.path;
      });
    }
  },
};
</script>
