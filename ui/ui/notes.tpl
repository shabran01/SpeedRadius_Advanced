{include file="sections/header.tpl"}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="bg-blue-600 text-white p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    {Lang::T('My Notes')}
                </h1>
                <p class="mt-2 text-sm text-gray-600">{Lang::T('Keep track of your ideas and important information')}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{$_url}plugin/notes_ui/add" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {Lang::T('Add New Note')}
                </a>
            </div>
        </div>

        <!-- Notes Grid -->
        {if count($notes) > 0}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {foreach $notes as $note}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2 flex-1 mr-2">
                            <a href="{$_url}plugin/notes_ui/view/{$note.id}" class="hover:text-blue-600 transition-colors">
                                {$note.title}
                            </a>
                        </h3>
                        <span class="text-xs text-gray-400 whitespace-nowrap flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {Lang::dateTimeFormat($note.updated_at)}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed">
                            {if $note.description}
                                {substr(strip_tags($note.description), 0, 120)}{if strlen(strip_tags($note.description)) > 120}...{/if}
                            {else}
                                <span class="italic text-gray-400">{Lang::T('No description')}</span>
                            {/if}
                        </p>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{$_url}plugin/notes_ui/view/{$note.id}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{Lang::T('View')}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{$_url}plugin/notes_ui/edit/{$note.id}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="{Lang::T('Edit')}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </div>
                        <a href="{$_url}plugin/notes_ui/delete/{$note.id}" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" onclick="return confirm('{Lang::T('Are you sure you want to delete this note?')}')" title="{Lang::T('Delete')}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
        {else}
        <!-- Empty State -->
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="mx-auto w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{Lang::T('No notes yet')}</h3>
            <p class="text-gray-500 mb-8 max-w-sm mx-auto">{Lang::T('Get started by creating your first note to keep track of important information.')}</p>
            <a href="{$_url}plugin/notes_ui/add" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {Lang::T('Create First Note')}
            </a>
        </div>
        {/if}
    </div>
</div>
{include file="sections/footer.tpl"}
