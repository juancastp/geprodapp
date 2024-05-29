document.getElementById('recipe-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const recipeName = document.getElementById('recipe_name').value;
    const ingredients = Array.from(document.querySelectorAll('input[name="ingredients[]"]'))
                            .map(input => input.value);

    const response = await fetch('http://localhost/backend/public/recipes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ recipe_name: recipeName, ingredients }),
    });

    const result = await response.json();
    if (result.status === 'success') {
        loadRecipes();
    } else {
        console.error('Error al guardar la receta');
    }
});

function addIngredient() {
    const ingredientsDiv = document.getElementById('ingredients');
    const newIngredient = document.createElement('div');
    newIngredient.className = 'form-group';
    newIngredient.innerHTML = '<input type="text" name="ingredients[]" class="form-control" placeholder="Ingrediente">';
    ingredientsDiv.appendChild(newIngredient);
}

async function loadRecipes() {
    const response = await fetch('http://localhost/backend/public/recipes');
    const recipes = await response.json();
    const recipesTable = document.getElementById('recipes-table').getElementsByTagName('tbody')[0];

    recipesTable.innerHTML = '';
    recipes.forEach(recipe => {
        const row = recipesTable.insertRow();
        const cell1 = row.insertCell(0);
        const cell2 = row.insertCell(1);

        cell1.innerHTML = `<strong>${recipe.product_name}</strong><ul>${recipe.ingredients.map(ing => `<li>${ing}</li>`).join('')}</ul>`;
        cell2.innerHTML = `<button class="btn btn-warning btn-sm">Editar</button><button class="btn btn-danger btn-sm">Eliminar</button>`;
    });
}

// Load recipes on page load
loadRecipes();
