{include file="sections/header.tpl"}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{$_url}plugin/notes_ui" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {Lang::T('Back to Notes')}
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="bg-indigo-600 text-white p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    {Lang::T('View Note')}
                </h1>
                <div class="flex space-x-3">
                    <a href="{$_url}plugin/notes_ui/edit/{$note.id}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {Lang::T('Edit')}
                    </a>
                    <a href="{$_url}plugin/notes_ui/delete/{$note.id}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('{Lang::T('Are you sure you want to delete this note?')}')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {Lang::T('Delete')}
                    </a>
                </div>
            </div>
        </div>

        <!-- Note Content Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-900 mb-4 leading-tight">{$note.title}</h2>
                
                <!-- Divider -->
                <div class="h-px bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 mb-6"></div>
                
                <!-- Description -->
                <div class="prose prose-gray max-w-none">
                    {if $note.description}
                        <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-wrap">{$note.description|nl2br}</p>
                    {else}
                        <p class="text-gray-400 italic text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            {Lang::T('No description provided')}
                        </p>
                    {/if}
                </div>
            </div>
            
            <!-- Footer with Timestamps -->
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm text-gray-500">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {Lang::T('Created')}: <span class="ml-1 font-medium text-gray-700">{Lang::dateTimeFormat($note.created_at)}</span>
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {Lang::T('Last Updated')}: <span class="ml-1 font-medium text-gray-700">{Lang::dateTimeFormat($note.updated_at)}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="sections/footer.tpl"}
