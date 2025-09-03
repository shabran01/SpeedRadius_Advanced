{include file="sections/header.tpl"}

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{$_title}</h1>
                        <p class="mt-1 text-sm text-gray-500">View and manage all your expenses</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                        <a href="{$_url}plugin/expenditure&action=add" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Expenditure
                        </a>
                        <a href="{$_url}plugin/expenditure" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <form method="get" class="space-y-4">
                    <input type="hidden" name="_route" value="plugin/expenditure">
                    <input type="hidden" name="action" value="list">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="{$search}" 
                                   placeholder="Description, vendor, receipt..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category" 
                                    name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">All Categories</option>
                                {foreach $categories as $cat}
                                <option value="{$cat.id}" {if $cat.id == $category}selected{/if}>{$cat.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{$date_from}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{$date_to}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                            <a href="{$_url}plugin/expenditure&action=list" 
                               class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Export Buttons -->
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-700 flex items-center">Export:</span>
                        <a href="{$_url}plugin/expenditure&action=export&format=csv&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}"
                           class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            CSV
                        </a>
                        <a href="{$_url}plugin/expenditure&action=export_pdf&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}"
                           class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            PDF
                        </a>
                        <a href="{$_url}plugin/expenditure&action=export_excel&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                            </svg>
                            Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {if $expenses}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {assign var="total" value=0}
                        {foreach $expenses as $expense}
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm text-gray-900">{$expense.expense_date}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{$expense.description}</div>
                                {if $expense.notes}
                                <div class="text-sm text-gray-500 mt-1">{$expense.notes}</div>
                                {/if}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {if $expense.category_name}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {$expense.category_name}
                                </span>
                                {else}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Uncategorized
                                </span>
                                {/if}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{$currency_code} {number_format($expense.amount, 2)}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{$expense.vendor}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{$expense.receipt_number}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex space-x-2">
                                    <a href="{$_url}plugin/expenditure&action=edit&id={$expense.id}" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>
                                    <a href="{$_url}plugin/expenditure&action=delete&id={$expense.id}" 
                                       class="text-red-600 hover:text-red-800 font-medium"
                                       onclick="return confirm('Are you sure you want to delete this expenditure?')">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        {assign var="total" value=$total+$expense.amount}
                        {/foreach}
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total</th>
                            <th class="px-6 py-3 text-sm font-bold text-gray-900">{$currency_code} {number_format($total, 2)}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {else}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No expenses found</h3>
                <p class="mt-1 text-sm text-gray-500">No expenses match your search criteria. Try adjusting your filters or add a new expenditure.</p>
                <div class="mt-6">
                    <a href="{$_url}plugin/expenditure&action=add" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Expenditure
                    </a>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
