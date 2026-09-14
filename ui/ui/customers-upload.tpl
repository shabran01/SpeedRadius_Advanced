{include file="sections/header.tpl"}

<style>
    .upload-container {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
    }
    
    .upload-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .upload-header h2 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .upload-area {
        border: 2px dashed #3498db;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        background: #f8f9fa;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #2980b9;
        background: #e3f2fd;
    }
    
    .upload-icon {
        font-size: 48px;
        color: #3498db;
        margin-bottom: 15px;
    }
    
    .btn-upload {
        background: linear-gradient(145deg, #3498db, #2980b9);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }
    
    .btn-upload:hover {
        background: linear-gradient(145deg, #2980b9, #1f5f99);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(52, 152, 219, 0.4);
    }
    
    .csv-format {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .csv-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    
    .csv-table th,
    .csv-table td {
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        text-align: left;
    }
    
    .csv-table th {
        background: #e9ecef;
        font-weight: 600;
    }
    
    .required {
        color: #e74c3c;
        font-weight: 600;
    }
    
    .optional {
        color: #7f8c8d;
        font-style: italic;
    }
    
    .error-list {
        background: #fff5f5;
        border: 1px solid #fed7d7;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .error-list h4 {
        color: #c53030;
        margin-bottom: 10px;
    }
    
    .error-list ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .error-list li {
        color: #c53030;
        margin-bottom: 5px;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}customers" class="btn btn-default btn-xs">
                        <i class="fa fa-arrow-left"></i> {Lang::T('Back to Customers')}
                    </a>
                    <a href="{$_url}customers/sample_csv" class="btn btn-success btn-xs">
                        <i class="fa fa-download"></i> Download Sample CSV
                    </a>
                </div>
                <i class="fa fa-upload"></i> {Lang::T('Upload Customers')}
            </div>
            
            <div class="panel-body">
                <div class="upload-container">
                    <div class="upload-header">
                        <h2><i class="fa fa-users"></i> {Lang::T('Import Customers from CSV')}</h2>
                        <p class="text-muted">Upload a CSV file to bulk import customer data into your system</p>
                    </div>
                    
                    <form action="{$_url}customers/upload_process" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="{$csrf_token}">
                        
                        <div class="upload-area">
                            <div class="upload-icon">
                                <i class="fa fa-cloud-upload"></i>
                            </div>
                            <h4>Choose CSV File</h4>
                            <p class="text-muted">Select a CSV file containing customer data</p>
                            <input type="file" name="csv_file" accept=".csv" required 
                                   style="margin: 15px 0;" class="form-control" id="csv_file">
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn-upload">
                                <i class="fa fa-upload"></i> Upload and Import Customers
                            </button>
                        </div>
                    </form>
                    
                    <div class="csv-format">
                        <h4><i class="fa fa-info-circle"></i> CSV Format Requirements</h4>
                        <p>Your CSV file must include the following columns (headers must match exactly):</p>
                        
                        <table class="csv-table">
                            <thead>
                                <tr>
                                    <th>Column Name</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>username</strong></td>
                                    <td><span class="required">Required</span></td>
                                    <td>Unique username for login</td>
                                    <td>john_doe</td>
                                </tr>
                                <tr>
                                    <td><strong>fullname</strong></td>
                                    <td><span class="required">Required</span></td>
                                    <td>Customer's full name</td>
                                    <td>John Doe</td>
                                </tr>
                                <tr>
                                    <td><strong>phonenumber</strong></td>
                                    <td><span class="required">Required</span></td>
                                    <td>Phone number (must be unique)</td>
                                    <td>254712345678</td>
                                </tr>
                                <tr>
                                    <td><strong>email</strong></td>
                                    <td><span class="optional">Optional</span></td>
                                    <td>Email address</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><strong>address</strong></td>
                                    <td><span class="optional">Optional</span></td>
                                    <td>Physical address</td>
                                    <td>123 Main Street</td>
                                </tr>
                                <tr>
                                    <td><strong>balance</strong></td>
                                    <td><span class="optional">Optional</span></td>
                                    <td>Initial account balance</td>
                                    <td>100.00</td>
                                </tr>
                                <tr>
                                    <td><strong>service_type</strong></td>
                                    <td><span class="optional">Optional</span></td>
                                    <td>Service type (Hotspot, PPPoE, VPN, Others)</td>
                                    <td>Hotspot</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="alert alert-info" style="margin-top: 20px;">
                            <h5><i class="fa fa-lightbulb-o"></i> Tips:</h5>
                            <ul>
                                <li>Make sure your CSV file has headers in the first row</li>
                                <li>Usernames and phone numbers must be unique</li>
                                <li>Password will be auto-generated for each customer</li>
                                <li>All customers will be set to "Active" status by default</li>
                                <li>If service_type is not specified, "Hotspot" will be used</li>
                                <li>Phone numbers should include country code (e.g., 254712345678)</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5><i class="fa fa-exclamation-triangle"></i> Important:</h5>
                            <ul>
                                <li>Backup your database before importing large amounts of data</li>
                                <li>Test with a small CSV file first</li>
                                <li>Existing usernames and phone numbers will be skipped</li>
                            </ul>
                        </div>
                    </div>
                    
                    {if isset($upload_errors)}
                        <div class="error-list">
                            <h4><i class="fa fa-exclamation-circle"></i> Import Errors</h4>
                            <ul>
                                {foreach $upload_errors as $error}
                                    <li>{$error}</li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const uploadArea = document.querySelector('.upload-area');
        uploadArea.style.borderColor = '#27ae60';
        uploadArea.style.backgroundColor = '#d5f4e6';
        
        const icon = uploadArea.querySelector('.upload-icon i');
        icon.className = 'fa fa-check-circle';
        icon.style.color = '#27ae60';
        
        const title = uploadArea.querySelector('h4');
        title.textContent = 'File Selected: ' + fileName;
        title.style.color = '#27ae60';
    }
});
</script>

{include file="sections/footer.tpl"}
