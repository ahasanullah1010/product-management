<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product CRUD with Axios</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        img { width: 80px; height: auto; margin: 5px; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center text-primary mb-4">🎯 Product CRUD with Axios & Bootstrap</h2>

    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">Add / Edit Product</div>
        <div class="card-body">
            <form id="productForm" class="row g-3">
                <input type="hidden" id="product_id">
                <div class="col-md-6">
                    <input type="text" id="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" id="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" id="price" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-4">
                    <input type="number" id="stock" class="form-control" placeholder="Stock" required>
                </div>
                <div class="col-md-4">
                    <input type="file" id="image" class="form-control">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">💾 Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productsList" class="table-responsive"></div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

    document.getElementById('productForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let id = document.getElementById('product_id').value;
        let formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('price', document.getElementById('price').value);
        formData.append('stock', document.getElementById('stock').value);

        let image = document.getElementById('image').files[0];
        if (image) {
            formData.append('image', image);
        }

        let url = '/api/products';
        let method = 'post';

        if (id) {
            url += '/' + id;
            method = 'post';
            formData.append('_method', 'PUT');
        }

        axios({
            method: method,
            url: url,
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => {
            alert("✅ Product saved!");
            document.getElementById('productForm').reset();
            loadProducts();
        }).catch(err => {
            alert("❌ Error: " + err.response.data.message);
        });
    });

    function loadProducts() {
        axios.get('/api/products').then(res => {
            let html = `
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">📷 Image</th>
                            <th scope="col">📝 Name</th>
                            <th scope="col">💰 Price</th>
                            <th scope="col">📦 Stock</th>
                            <th scope="col">⚙️ Actions</th>
                        </tr>
                    </thead>
                    <tbody>`;
            res.data.forEach(product => {
                html += `
                    <tr>
                        <td><img src="/storage/${product.image}" class="img-thumbnail"></td>
                        <td>${product.name}</td>
                        <td>$${product.price}</td>
                        <td>${product.stock}</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-2" onclick="editProduct(${product.id})">✏️ Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">🗑️ Delete</button>
                        </td>
                    </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('productsList').innerHTML = html;
        });
    }

    function editProduct(id) {
        axios.get(`/api/products/${id}`).then(res => {
            let product = res.data;
            document.getElementById('product_id').value = product.id;
            document.getElementById('name').value = product.name;
            document.getElementById('description').value = product.description;
            document.getElementById('price').value = product.price;
            document.getElementById('stock').value = product.stock;
        });
    }

    function deleteProduct(id) {
        if (confirm("Are you sure you want to delete this product?")) {
            axios.delete(`/api/products/${id}`)
                .then(res => {
                    alert("🗑️ Deleted!");
                    loadProducts();
                });
        }
    }

    loadProducts();
</script>
</body>
</html>
