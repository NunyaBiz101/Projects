function openModal(mode) {
  document.getElementById('modalTitle').textContent = 'Add Product';
  document.getElementById('formAction').value = 'add';
  document.getElementById('formId').value = '';
  document.getElementById('productForm').reset();
  document.getElementById('fStock').checked = true;
  document.getElementById('fExistingImage').value = '';
  document.getElementById('fImageFile').required = true;
  document.getElementById('submitBtn').textContent = 'Add Product';
  document.getElementById('productModal').classList.add('open');
}

function openEditModalFromButton(button) {
  const product = {
    id: button.dataset.id,
    name: button.dataset.name,
    category: button.dataset.category,
    price: button.dataset.price,
    description: button.dataset.description,
    in_stock: button.dataset.in_stock,
    image_url: button.dataset.image_url,
  };
  openEditModal(product);
}

function openEditModal(product) {
  document.getElementById('modalTitle').textContent = 'Edit Product';
  document.getElementById('formAction').value = 'edit';
  document.getElementById('formId').value = product.id;
  document.getElementById('fName').value = product.name;
  document.getElementById('fCategory').value = product.category;
  document.getElementById('fPrice').value = product.price;
  document.getElementById('fDesc').value = product.description || '';
  document.getElementById('fStock').checked = product.in_stock == 1;
  document.getElementById('fExistingImage').value = product.image_url || '';
  document.getElementById('fImageFile').value = '';
  document.getElementById('fImageFile').required = false;
  document.getElementById('submitBtn').textContent = 'Save Changes';
  document.getElementById('productModal').classList.add('open');
}

function closeModal() {
  document.getElementById('productModal').classList.remove('open');
}

document.getElementById('productModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

function filterTable() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#productsTable tbody tr').forEach(row => {
    row.style.display = row.dataset.search.includes(q) ? '' : 'none';
  });
}
