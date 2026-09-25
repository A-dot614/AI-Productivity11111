@props(['name', 'value' => '', 'rows' => 12])

<div
    x-data="markdownEditor(@js(old($name, $value)), 'editor')"
    class="overflow-hidden rounded-xl border border-slate-300 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/30 dark:border-slate-700"
>
    <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-700 dark:bg-slate-800/60">
        <button type="button" @click="tab = 'write'" :class="tab === 'write' ? 'bg-white text-slate-800 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 dark:text-slate-400'" class="rounded-lg px-3 py-1 text-xs font-semibold">Write</button>
        <button type="button" @click="tab = 'preview'" :class="tab === 'preview' ? 'bg-white text-slate-800 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 dark:text-slate-400'" class="rounded-lg px-3 py-1 text-xs font-semibold">Preview</button>
        <span class="mx-1 h-4 w-px bg-slate-200 dark:bg-slate-700"></span>
        <button type="button" @click="insert('**')" title="Bold" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300"><span class="text-sm font-bold">B</span></button>
        <button type="button" @click="insert('*')" title="Italic" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300"><span class="text-sm italic">I</span></button>
        <button type="button" @click="insert('# ')" title="Heading" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300"><span class="text-sm font-bold">H</span></button>
        <button type="button" @click="insert('- ')" title="List" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
        </button>
        <button type="button" @click="insert('- [ ] ')" title="Checklist" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
        </button>
        <button type="button" @click="insert('> ')" title="Quote" class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17h10M6.343 14.343a1 1 0 010-1.414L9.293 10l-2.95-2.929a1 1 0 011.414-1.414l3.657 3.657a1 1 0 010 1.414l-3.657 3.657a1 1 0 01-1.414 0z" /></svg>
        </button>
    </div>

    <textarea x-show="tab === 'write'" x-ref="editor" x-model="value" name="{{ $name }}" rows="{{ $rows }}" placeholder="Write your note in markdown..." class="w-full resize-y bg-transparent px-4 py-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 dark:text-slate-100 dark:placeholder:text-slate-500">{{ $value }}</textarea>

    <div x-show="tab === 'preview'" x-cloak class="markdown-body min-h-[12rem] w-full bg-white px-4 py-3 text-sm text-slate-700 dark:bg-slate-900 dark:text-slate-300" x-html="previewHtml"></div>
</div>
