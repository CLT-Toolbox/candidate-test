<div class="space-y-6">
    <div class="relative group">
        <input type="file" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50">
        <div class="p-8 border-2 border-dashed border-gray-200 rounded-2xl bg-white group-hover:border-brand-500 group-hover:bg-brand-50/10 transition-all duration-500 text-center relative overflow-hidden">
            <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 mx-auto mb-4 group-hover:scale-110 transition-transform duration-500 shadow-sm border border-brand-100">
                <i data-lucide="cloud-upload" class="w-8 h-8" x-show="!loading"></i>
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin" x-show="loading"></i>
            </div>
            
            <div class="space-y-1">
                <p class="text-sm font-bold text-gray-900">
                    <span class="text-brand-600 group-hover:underline decoration-2 underline-offset-4">Click to upload</span>
                    <span class="text-gray-400"> or drag and drop</span>
                </p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">CSV or JSON up to 10MB</p>
            </div>

            <template x-if="fileName">
                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full border border-brand-100 animate-in fade-in slide-in-from-bottom-1">
                    <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                    <span class="text-[10px] font-bold uppercase tracking-tight" x-text="fileName"></span>
                </div>
            </template>
        </div>
    </div>

    <div class="space-y-2">
        <x-ui.label value="Conflict Resolution Strategy" />
        <div class="relative">
            <select x-model="strategy" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold text-gray-700 appearance-none focus:ring-2 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all cursor-pointer">
                <option value="skip">Skip conflicts (Default)</option>
                <option value="overwrite">Overwrite existing data</option>
                <option value="duplicate">Keep both (Create new)</option>
            </select>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    <div class="p-4 bg-gray-50/50 border border-gray-100 rounded-2xl flex items-center justify-between group hover:border-gray-200 transition-colors">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-brand-600 transition-colors shadow-sm">
                <i data-lucide="beaker" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-900">Run as Dry Run</p>
                <p class="text-[10px] text-gray-400 font-medium leading-tight">Simulate the import process without saving changes.</p>
            </div>
        </div>
        <div class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" x-model="dryRun" class="sr-only peer">
            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-600"></div>
        </div>
    </div>

    <template x-if="analysis && analysis.summary.conflict_count > 0">
        <div class="p-4 bg-red-50/50 border border-red-100 rounded-2xl flex items-start gap-3 animate-in zoom-in-95 duration-300">
            <div class="mt-0.5 text-red-500">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-bold text-red-900">Potential Conflicts Detected</h4>
                <p class="text-[10px] text-red-700/80 font-medium leading-snug">
                    <span x-text="analysis.summary.conflict_count"></span> Layups differ significantly from current records. 
                    <button @click="step = 'conflicts'" class="text-red-700 underline decoration-red-300/50 underline-offset-2 hover:text-red-900 transition-colors">View details</button>
                </p>
            </div>
        </div>
    </template>

    <div class="flex items-center justify-end gap-3 pt-2">
        <x-ui.button variant="secondary" x-on:click="show = false">
            Cancel
        </x-ui.button>
        <x-ui.button icon="file-spreadsheet" @click="confirm" ::disabled="!file || loading">
            Confirm Import
        </x-ui.button>
    </div>
</div>
