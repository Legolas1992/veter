<?php
// facturacion/nuevo.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();
$clientes = $db->query("SELECT * FROM clientes ORDER BY nombre ASC")->fetchAll();
$productos = $db->query("SELECT * FROM productos WHERE estado='activo' AND stock_actual > 0 ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Nueva Factura</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST" id="invoiceForm">
        <!-- Header: Client & Date -->
        <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
            <div class="form-group" style="flex: 1;">
                <label for="cliente_id">Cliente *</label>
                <select name="cliente_id" class="form-control" required>
                    <option value="">Seleccione Cliente</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Fecha</label>
                <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" disabled>
            </div>
        </div>

        <!-- Items Table -->
        <h4>Detalle de Factura</h4>
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5; text-align: left;">
                        <th style="padding: 10px;">Tipo</th>
                        <th style="padding: 10px;">Descripción / Producto</th>
                        <th style="padding: 10px; width: 100px;">Cant.</th>
                        <th style="padding: 10px; width: 150px;">Precio Unit.</th>
                        <th style="padding: 10px; width: 150px;">Subtotal</th>
                        <th style="padding: 10px;"></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <!-- Rows will be added here -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; padding: 15px;">TOTAL:</td>
                        <td style="font-weight: bold; font-size: 1.2rem;">
                            $<span id="total-amount">0.00</span>
                            <input type="hidden" name="monto_total" id="input-total" value="0">
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="addItemRow()" style="margin-top: 10px;">+ Agregar Ítem</button>

        <hr style="margin: 2rem 0;">

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Generar Factura</button>
        </div>
    </form>
</div>

<!-- Products Data for JS -->
<script>
const productsData = <?php echo json_encode($productos); ?>;

function addItemRow() {
    const tbody = document.getElementById('items-body');
    const rowId = 'row-' + Date.now();
    
    let prodOptions = '<option value="">-- Seleccionar --</option>';
    productsData.forEach(p => {
        prodOptions += `<option value="${p.id}" data-price="${p.precio_venta}">${p.nombre}</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = rowId;
    tr.innerHTML = `
        <td>
            <select name="tipos[]" class="form-control type-select" onchange="toggleInput('${rowId}')" style="width: 120px;">
                <option value="servicio">Servicio</option>
                <option value="producto">Producto</option>
            </select>
        </td>
        <td>
            <div class="input-desc">
                <input type="text" name="descripciones[]" class="form-control" placeholder="Descripción del servicio" required>
                <input type="hidden" name="productos_ids[]" value="">
            </div>
            <div class="select-prod" style="display: none;">
                <select class="form-control prod-select" onchange="updatePrice('${rowId}')">
                    ${prodOptions}
                </select>
            </div>
        </td>
        <td>
            <input type="number" name="cantidades[]" class="form-control qty-input" value="1" min="1" onchange="calculateRow('${rowId}')">
        </td>
        <td>
            <input type="number" name="precios[]" class="form-control price-input" step="0.01" value="0.00" onchange="calculateRow('${rowId}')">
        </td>
        <td>
            <input type="text" class="form-control subtotal-show" value="0.00" disabled>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${rowId}')">X</button>
        </td>
    `;
    tbody.appendChild(tr);
}

function toggleInput(rowId) {
    const row = document.getElementById(rowId);
    const type = row.querySelector('.type-select').value;
    const inputDesc = row.querySelector('.input-desc');
    const selectProd = row.querySelector('.select-prod');
    const descInput = inputDesc.querySelector('input[type="text"]');
    const prodHidden = inputDesc.querySelector('input[type="hidden"]');
    const prodSelect = selectProd.querySelector('select');
    const priceInput = row.querySelector('.price-input');

    if (type === 'producto') {
        inputDesc.style.display = 'none';
        selectProd.style.display = 'block';
        descInput.removeAttribute('required');
        prodSelect.setAttribute('required', 'required');
        priceInput.readOnly = true;
    } else {
        inputDesc.style.display = 'block';
        selectProd.style.display = 'none';
        descInput.setAttribute('required', 'required');
        prodSelect.removeAttribute('required');
        prodSelect.value = "";
        prodHidden.value = "";
        priceInput.readOnly = false;
        priceInput.value = "0.00";
    }
    calculateRow(rowId);
}

function updatePrice(rowId) {
    const row = document.getElementById(rowId);
    const select = row.querySelector('.prod-select');
    const option = select.options[select.selectedIndex];
    const price = option.getAttribute('data-price') || 0;
    const priceInput = row.querySelector('.price-input');
    const descInput = row.querySelector('.input-desc input[type="text"]');
    const prodHidden = row.querySelector('.input-desc input[type="hidden"]');
    
    priceInput.value = price;
    prodHidden.value = select.value;
    descInput.value = option.text; // Store name as desc for simplicity
    
    calculateRow(rowId);
}

function calculateRow(rowId) {
    const row = document.getElementById(rowId);
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const subtotal = qty * price;
    
    row.querySelector('.subtotal-show').value = subtotal.toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-show').forEach(el => {
        total += parseFloat(el.value) || 0;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2);
    document.getElementById('input-total').value = total.toFixed(2);
}

function removeRow(rowId) {
    document.getElementById(rowId).remove();
    calculateTotal();
}

// Add one row by default
addItemRow();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
