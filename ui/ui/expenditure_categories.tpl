{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-default btn-xs" title="Back to Dashboard" href="{$_url}plugin/expenditure">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Dashboard
                    </a>
                </div>
                {$_title}
            </div>
            <div class="panel-body">
                
                <!-- Add New Category Form -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Add New Category</h4>
                            </div>
                            <div class="panel-body">
                                <form method="post" action="{$_url}plugin/expenditure&action=save_category">
                                    <div class="form-group">
                                        <label for="name">Category Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Category description"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Existing Categories</h4>
                            </div>
                            <div class="panel-body">
                                {if $categories}
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach $categories as $category}
                                            <tr>
                                                <td><strong>{$category.name}</strong></td>
                                                <td>{$category.description}</td>
                                                <td>
                                                    <button type="button" class="btn btn-xs btn-warning" onclick="editCategory({$category.id}, '{$category.name|escape:'javascript'}', '{$category.description|escape:'javascript'}')" title="Edit">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                    </button>
                                                    <a href="{$_url}plugin/expenditure&action=delete_category&id={$category.id}" class="btn btn-xs btn-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                {else}
                                <p class="text-muted">No categories found.</p>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{$_url}plugin/expenditure&action=save_category">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Category</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_category_id" name="id">
                    <div class="form-group">
                        <label for="edit_name">Category Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description) {
    $('#edit_category_id').val(id);
    $('#edit_name').val(name);
    $('#edit_description').val(description);
    $('#editCategoryModal').modal('show');
}
</script>

{include file="sections/footer.tpl"}
