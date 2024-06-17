<script>
$(document).ready(function() {
    $('#recipe').change(function() {
        const recipeId = $(this).val();
        if (recipeId) {
            $.ajax({
                url: 'fetch_ingredients.php',
                type: 'POST',
                data: { recipe_id: recipeId },
                dataType: 'json',
                success: function(data) {
                    console.log("Ingredientes recibidos:", data);
                    let html = '<h4>Ingredientes</h4>';
                    data.forEach(ingredient => {
                        html += `<div class="form-group">
                                    <label>${ingredient.ingredient_name}</label>
                                    <input type="hidden" name="ingredients[${ingredient.ingredient_name}][name]" value="${ingredient.ingredient_name}">
                                    <input type="text" name="ingredients[${ingredient.ingredient_name}][lot]" class="form-control" placeholder="Lote de ${ingredient.ingredient_name}" required>
                                 </div>`;
                    });
                    $('#ingredients-container').html(html);
                },
                error: function(error) {
                    console.log("Error al obtener los ingredientes:", error);
                }
            });
        } else {
            $('#ingredients-container').html('');
        }
    });
});


</script>
<script>
        function addNewIngredientField() {
            const ingredientsDiv = document.getElementById('ingredients');
            const count = ingredientsDiv.getElementsByClassName('form-control').length + 1;
            const newDiv = document.createElement('div');
            newDiv.classList.add('form-group', 'd-flex', 'mt-2');
            newDiv.innerHTML = `<input type="text" class="form-control" id="ingredient_${count}" name="ingredients[]" placeholder="Ingrediente ${count}">
                                <button type="button" class="btn btn-success ml-2" onclick="addNewIngredientField()">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>`;
            ingredientsDiv.appendChild(newDiv);
            const buttons = ingredientsDiv.querySelectorAll('button');
            buttons.forEach((button, index) => {
                if (index < buttons.length - 1) {
                    button.style.display = 'none';
                } else {
                    button.style.display = 'block';
                }
            });
        }

        function addIngredient(recipeId) {
            const ingredientName = prompt("Ingrese el nombre del nuevo ingrediente:");
            if (ingredientName) {
                const formData = new FormData();
                formData.append('action', 'add_ingredient');
                formData.append('recipe_id', recipeId);
                formData.append('ingredient_name', ingredientName);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al añadir el ingrediente');
                      }
                  });
            }
        }

        document.getElementById('recipe-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_recipe');
            fetch('alta_receta.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      alert(data.message);
                      location.reload();
                  } else {
                      alert('Error al añadir la receta');
                  }
              });
        });

        function deleteRecipe(recipeId) {
            if (confirm('¿Está seguro de que desea eliminar esta receta?')) {
                const formData = new FormData();
                formData.append('action', 'delete_recipe');
                formData.append('recipe_id', recipeId);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al eliminar la receta');
                      }
                  });
            }
        }

        function deleteIngredient(ingredientId) {
            if (confirm('¿Está seguro de que desea eliminar este ingrediente?')) {
                const formData = new FormData();
                formData.append('action', 'delete_ingredient');
                formData.append('ingredient_id', ingredientId);
                fetch('alta_receta.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.status === 'success') {
                          alert(data.message);
                          location.reload();
                      } else {
                          alert('Error al eliminar el ingrediente');
                      }
                  });
            }
        }

        function editIngredient(ingredientId) {
            document.getElementById('ingredient-input-' + ingredientId).disabled = false;
            document.getElementById('save-btn-' + ingredientId).classList.remove('hidden');
        }

        function saveIngredient(ingredientId) {
            const ingredientName = document.getElementById('ingredient-input-' + ingredientId).value;
            const formData = new FormData();
            formData.append('action', 'update_ingredient');
            formData.append('ingredient_id', ingredientId);
            formData.append('ingredient_name', ingredientName);
            fetch('alta_receta.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      alert(data.message);
                      document.getElementById('ingredient-input-' + ingredientId).disabled = true;
                      document.getElementById('save-btn-' + ingredientId).classList.add('hidden');
                  } else {
                      alert('Error al actualizar el ingrediente');
                  }
              });
        }
    </script>
    <script>
function addMaterialField() {
    const materialsDiv = document.getElementById('materials');
    const count = materialsDiv.getElementsByClassName('form-group').length;
    const newDiv = document.createElement('div');
    newDiv.classList.add('form-group');
    newDiv.innerHTML = `<label>Artículo ${count + 1}:</label>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <input type="text" class="form-control" name="materials[${count}][name]" placeholder="Material" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input type="number" step="any" class="form-control" name="materials[${count}][weight]" placeholder="Peso" required>
                            </div>
                            <div class="form-group col-md-1">
                                <input type="number" step="any" class="form-control" name="materials[${count}][quantity]" placeholder="Cantidad" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input type="text" class="form-control" name="materials[${count}][lot]" placeholder="Lote" required>
                            </div>
                            <div class="form-group col-md-2">
                                <input id="demasart" type="date" class="form-control" name="materials[${count}][fec_cad]" placeholder="Fecha de cad." required>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-success" onclick="addMaterialField()">
                                    <i class="bi bi-plus-square-fill"></i>
                                </button>
                            </div>
                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" onclick="removeMaterialField(this)">
                                    <i class="bi bi-dash-square-fill"></i>
                                </button>
                            </div>
                        </div>`;
    materialsDiv.appendChild(newDiv);
    updateAddButtons();
}

function removeMaterialField(button) {
    console.log('Botón de eliminar presionado');
    const formRow = button.closest('.form-row');
    console.log('Elemento formRow encontrado:', formRow);
    if (formRow) {
        formRow.parentElement.remove();
        console.log('formRow eliminado');
    } else {
        console.log('formRow no encontrado');
    }
    updateAddButtons();
}

function updateAddButtons() {
    const materialsDiv = document.getElementById('materials');
    const addButtons = materialsDiv.querySelectorAll('button.btn-success');
    addButtons.forEach((button, index) => {
        button.style.display = (index === addButtons.length - 1) ? 'block' : 'none';
    });
}

document.getElementById('entry-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'add_entry');
    fetch('entradas.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              location.reload();
          } else {
              alert('Error al añadir la entrada');
          }
      });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function toggleMaterials(entryId) {
    const materialesRow = document.getElementById('materiales-' + entryId);
    if (materialesRow.style.display === 'none') {
        materialesRow.style.display = '';
    } else {
        materialesRow.style.display = 'none';
    }
}

function downloadCSV() {
    let csv = 'Proveedor,Referencia,Fecha de Entrada,Materiales\n';
    const rows = document.querySelectorAll('#entriesTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 4) {
            const supplier = columns[0].innerText;
            const ref = columns[1].innerText;
            const entryDate = columns[2].innerText;
            let materiales = '';
            const materialesRow = row.nextElementSibling;
            if (materialesRow && materialesRow.querySelector('ul')) {
                const listItems = materialesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    materiales += item.innerText;
                    if (index < listItems.length - 1) materiales += ' | ';
                });
            }
            csv += `"${supplier}","${ref}","${entryDate}","${materiales}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'entradas.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.querySelector("#printableArea")).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth() - 20; // Ajuste de margen
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        doc.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight); // Ajuste de margen
        doc.save('entradas.pdf');
    });
}

function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const supplierFilter = document.getElementById('supplierFilter').value.toLowerCase();
    const refFilter = document.getElementById('refFilter').value.toLowerCase();
    const loteFilter = document.getElementById('loteFilter').value.toLowerCase();
    const cadDateFilter = document.getElementById('cadDateFilter').value;

    const rows = document.querySelectorAll('#entriesTable tbody tr');
    let entries = [];
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 4) {
            const supplier = columns[0].innerText.toLowerCase();
            const ref = columns[1].innerText.toLowerCase();
            const entryDate = columns[2].innerText;
            let materiales = '';
            const materialesRow = row.nextElementSibling;
            if (materialesRow && materialesRow.querySelector('ul')) {
                const listItems = materialesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    materiales += item.innerText.toLowerCase();
                    if (index < listItems.length - 1) materiales += ' | ';
                });
            }
            entries.push({ row, supplier, ref, entryDate, materiales, materialesRow });
        }
    });

    entries = entries.filter(e => {
        let dateCondition = true;
        let supplierCondition = true;
        let refCondition = true;
        let loteCondition = true;
        let cadDateCondition = true;

        if (startDate && endDate) {
            dateCondition = new Date(e.entryDate) >= new Date(startDate) && new Date(e.entryDate) <= new Date(endDate);
        }
        if (supplierFilter) {
            supplierCondition = e.supplier.includes(supplierFilter);
        }
        if (refFilter) {
            refCondition = e.ref.includes(refFilter);
        }
        if (loteFilter) {
            loteCondition = e.materiales.includes(loteFilter);
        }
        if (cadDateFilter) {
            cadDateCondition = e.materiales.includes(cadDateFilter);
        }

        return dateCondition && supplierCondition && refCondition && loteCondition && cadDateCondition;
    });

    rows.forEach(row => {
        row.style.display = 'none';
    });

    entries.forEach(e => {
        e.row.style.display = '';
        e.materialesRow.style.display = 'none';
    });
}

function editMaterial(materialId) {
    const materialSpan = document.getElementById('material-' + materialId);
    const parts = materialSpan.innerText.split(' - ');
    const name = parts[0].trim();
    const weight = parts[1].replace('kg', '').trim();
    const quantity = parts[2].replace('unidades', '').trim();
    const lot = parts[3].replace('Lote:', '').trim();
    const fec_cad = parts[4].replace('Fecha de Cad:', '').trim();

    const newHtml = `<input type="text" value="${name}" id="edit-name-${materialId}">
                     <input type="number" value="${weight}" id="edit-weight-${materialId}">
                     <input type="number" value="${quantity}" id="edit-quantity-${materialId}">
                     <input type="text" value="${lot}" id="edit-lot-${materialId}">
                     <input type="date" value="${fec_cad}" id="edit-fec-cad-${materialId}">
                     <button class="btn btn-success btn-sm" onclick="saveMaterial(${materialId})">Guardar</button>`;

    materialSpan.innerHTML = newHtml;
}

function saveMaterial(materialId) {
    const name = document.getElementById('edit-name-' + materialId).value;
    const weight = document.getElementById('edit-weight-' + materialId).value;
    const quantity = document.getElementById('edit-quantity-' + materialId).value;
    const lot = document.getElementById('edit-lot-' + materialId).value;
    const fec_cad = document.getElementById('edit-fec-cad-' + materialId).value;

    const formData = new FormData();
    formData.append('action', 'update_material');
    formData.append('material_id', materialId);
    formData.append('name', name);
    formData.append('weight', weight);
    formData.append('quantity', quantity);
    formData.append('lot', lot);
    formData.append('fec_cad', fec_cad);

    fetch('list_entries.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              location.reload();
          } else {
              alert('Error al actualizar el material');
          }
      });
}
</script>
<script>
const production_ingredients = <?= json_encode($production_ingredients) ?>;

console.log('Mapa de ingredientes de producción:', production_ingredients);

function toggleIngredients(productId) {
    console.log('toggleIngredients called with productId:', productId);
    const ingredientesRow = document.getElementById('ingredientes-' + productId);
    if (ingredientesRow.style.display === 'none') {
        console.log('Showing ingredients for productId:', productId);
        const ingredientes = production_ingredients[productId];
        if (ingredientes && ingredientes.length > 0) {
            let list = '';
            ingredientes.forEach(ingrediente => {
                list += `<li class="list-group-item"><strong>${ingrediente.ingredient_name}:</strong> ${ingrediente.ingredient_lot}</li>`;
            });
            ingredientesRow.querySelector('.list-group').innerHTML = list;
        } else {
            ingredientesRow.querySelector('.list-group').innerHTML = '<li class="list-group-item">No hay ingredientes registrados</li>';
        }
        ingredientesRow.style.display = '';
    } else {
        ingredientesRow.style.display = 'none';
    }
}

function downloadCSV() {
    let csv = 'Fecha,Cantidad,Producto,Lote,Ingredientes\n';
    const rows = document.querySelectorAll('#produccionTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 5) {
            const date = columns[0].innerText;
            const quantity = columns[1].innerText;
            const product = columns[2].innerText;
            const lote = columns[3].innerText;
            let ingredientes = '';
            const ingredientesRow = row.nextElementSibling;
            if (ingredientesRow && ingredientesRow.querySelector('ul')) {
                const listItems = ingredientesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    ingredientes += item.innerText;
                    if (index < listItems.length - 1) ingredientes += ' | ';
                });
            }
            csv += `"${date}","${quantity}","${product}","${lote}","${ingredientes}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'produccion.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Mostrar todos los ingredientes antes de generar el PDF
    document.querySelectorAll('#produccionTable tbody tr').forEach(row => {
        if (row.id.startsWith('ingredientes-')) {
            row.style.display = '';
        }
    });

    html2canvas(document.querySelector("#printableArea")).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth() - 20; // Ajuste de margen
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        doc.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight); // Ajuste de margen
        doc.save('produccion.pdf');

        // Ocultar nuevamente los ingredientes
        document.querySelectorAll('#produccionTable tbody tr').forEach(row => {
            if (row.id.startsWith('ingredientes-')) {
                row.style.display = 'none';
            }
        });
    });
}

function applyFilters() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const loteFilter = document.getElementById('loteFilter').value.toLowerCase();
    const orderBy = document.getElementById('orderBy').value;

    const rows = document.querySelectorAll('#produccionTable tbody tr');
    let products = [];
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length === 5) {
            const date = columns[0].innerText;
            const quantity = columns[1].innerText;
            const product = columns[2].innerText;
            const lote = columns[3].innerText;
            let ingredientes = '';
            const ingredientesRow = row.nextElementSibling;
            if (ingredientesRow && ingredientesRow.querySelector('ul')) {
                const listItems = ingredientesRow.querySelectorAll('li');
                listItems.forEach((item, index) => {
                    ingredientes += item.innerText.toLowerCase();
                    if (index < listItems.length - 1) ingredientes += ' | ';
                });
            }
            products.push({ row, date, quantity, product, lote, ingredientes, ingredientesRow });
        }
    });

    products = products.filter(p => {
        let dateCondition = true;
        let loteCondition = true;

        if (startDate && endDate) {
            dateCondition = new Date(p.date) >= new Date(startDate) && new Date(p.date) <= new Date(endDate);
        }
        if (loteFilter) {
            loteCondition = p.ingredientes.includes(loteFilter);
        }

        return dateCondition && loteCondition;
    });

    if (orderBy === 'asc') {
        products.sort((a, b) => new Date(a.date) - new Date(b.date));
    } else {
        products.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    rows.forEach(row => {
        row.style.display = 'none';
    });

    products.forEach(p => {
        p.row.style.display = '';
        p.ingredientesRow.style.display = 'none';
    });
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>

