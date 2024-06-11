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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>